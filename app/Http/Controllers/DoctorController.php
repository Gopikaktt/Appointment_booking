<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $query = Doctor::query();

        // For non-admin users (Patients / Doctors), only list active approved doctors
        if (!Auth::user()->isAdmin()) {
            $query->where('status', 'active');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('specialization', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('specialization')) {
            $query->where('specialization', $request->specialization);
        }

        if ($request->filled('status') && Auth::user()->isAdmin()) {
            $query->where('status', $request->status);
        }

        $doctors = $query->orderBy('name')->paginate(10);

        $specializationQuery = Doctor::query();
        if (!Auth::user()->isAdmin()) {
            $specializationQuery->where('status', 'active');
        }
        $specializations = $specializationQuery->select('specialization')->distinct()->pluck('specialization');

        return view('doctors.index', compact('doctors', 'specializations'));
    }

    public function create()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only administrators can create doctor accounts directly.');
        }
        return view('doctors.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'specialization' => 'required|string|max:255',
            'email' => 'required|email|unique:doctors,email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6',
            'bio' => 'nullable|string',
            'consultation_fee' => 'required|numeric|min:0',
            'room_number' => 'nullable|string|max:255',
            'available_days' => 'required|array|min:1',
            'start_time' => 'required',
            'end_time' => 'required',
            'slot_duration_minutes' => 'required|integer|min:10|max:120',
            'status' => 'required|in:active,inactive',
        ]);

        // 1. Create User account for Doctor login
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role' => 'doctor',
            'password' => Hash::make($validated['password']),
        ]);

        // 2. Create Doctor profile
        $validated['user_id'] = $user->id;
        unset($validated['password']);
        
        Doctor::create($validated);

        return redirect()->route('doctors.index')->with('success', 'Doctor created successfully! They can now sign in using their email and password.');
    }

    public function show(Doctor $doctor)
    {
        $upcomingAppointments = $doctor->appointments()
            ->with('patient')
            ->where('appointment_date', '>=', now()->toDateString())
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->take(5)
            ->get();

        return view('doctors.show', compact('doctor', 'upcomingAppointments'));
    }

    public function edit(Doctor $doctor)
    {
        $user = Auth::user();

        // Check permission: Admin can edit any doctor; Doctor can edit their own profile
        if ($user->isDoctor()) {
            if ($doctor->user_id !== $user->id && $doctor->email !== $user->email) {
                abort(403, 'Unauthorized to edit another doctor profile.');
            }
        } elseif (!$user->isAdmin()) {
            abort(403, 'Unauthorized.');
        }

        return view('doctors.edit', compact('doctor'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $user = Auth::user();

        if ($user->isDoctor()) {
            if ($doctor->user_id !== $user->id && $doctor->email !== $user->email) {
                abort(403, 'Unauthorized.');
            }
        } elseif (!$user->isAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'specialization' => 'required|string|max:255',
            'email' => 'required|email|unique:doctors,email,' . $doctor->id,
            'phone' => 'required|string|max:20',
            'bio' => 'nullable|string',
            'consultation_fee' => 'required|numeric|min:0',
            'room_number' => 'nullable|string|max:255',
            'available_days' => 'required|array|min:1',
            'start_time' => 'required',
            'end_time' => 'required',
            'slot_duration_minutes' => 'required|integer|min:10|max:120',
            'status' => 'required|in:active,inactive',
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:6|confirmed',
        ]);

        // Handle Change Password if requested
        if (!empty($request->new_password)) {
            if ($user->isDoctor()) {
                if (empty($request->current_password)) {
                    return back()->withErrors(['current_password' => 'Please enter your current password to set a new password.'])->withInput();
                }

                if (!Hash::check($request->current_password, $user->password)) {
                    return back()->withErrors(['current_password' => 'The provided current password does not match your account password.'])->withInput();
                }

                $user->password = Hash::make($request->new_password);
                $user->save();
            } elseif ($user->isAdmin() && $doctor->user) {
                $doctor->user->password = Hash::make($request->new_password);
                $doctor->user->save();
            }
        }

        unset($validated['current_password'], $validated['new_password'], $validated['new_password_confirmation']);

        $doctor->update($validated);

        // Update corresponding user record if linked
        if ($doctor->user) {
            $doctor->user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
            ]);
        }

        if ($user->isDoctor()) {
            return redirect()->route('dashboard')->with('success', 'Your practice profile, schedule, and security settings have been updated successfully!');
        }

        return redirect()->route('doctors.index')->with('success', 'Doctor details updated successfully!');
    }

    public function approve(Doctor $doctor)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized.');
        }

        $doctor->status = 'active';
        $doctor->save();

        return back()->with('success', "Doctor {$doctor->name} has been approved and activated for patient bookings!");
    }

    public function destroy(Doctor $doctor)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized.');
        }

        if ($doctor->user) {
            $doctor->user->delete();
        }
        $doctor->delete();
        return redirect()->route('doctors.index')->with('success', 'Doctor removed successfully!');
    }
}
