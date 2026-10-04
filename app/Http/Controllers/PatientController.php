<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    /**
     * Display listing of registered patients for Admin & Doctor
     */
    public function index(Request $request)
    {
        if (Auth::user()->isPatient()) {
            abort(403, 'Unauthorized.');
        }

        $query = User::where('role', 'patient')->withCount('appointments');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $patients = $query->orderBy('name')->paginate(10);

        return view('patients.index', compact('patients'));
    }

    /**
     * Display patient details and appointment history
     */
    public function show(User $patient, Request $request)
    {
        if (Auth::user()->isPatient()) {
            if (Auth::id() !== $patient->id) {
                abort(403, 'Unauthorized.');
            }
        }

        if (!$patient->isPatient()) {
            abort(404, 'Patient profile not found.');
        }

        $query = Appointment::with('doctor')
            ->where('patient_id', $patient->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('doctor', function($dq) use ($search) {
                    $dq->where('name', 'like', "%{$search}%")
                       ->orWhere('specialization', 'like', "%{$search}%");
                })->orWhere('symptoms_notes', 'like', "%{$search}%");
            });
        }

        $appointments = $query->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'asc')
            ->paginate(10);

        $stats = [
            'total' => $patient->appointments()->count(),
            'pending' => $patient->appointments()->where('status', 'Pending')->count(),
            'confirmed' => $patient->appointments()->where('status', 'Confirmed')->count(),
            'completed' => $patient->appointments()->where('status', 'Completed')->count(),
            'cancelled' => $patient->appointments()->where('status', 'Cancelled')->count(),
        ];

        return view('patients.show', compact('patient', 'appointments', 'stats'));
    }

    /**
     * Show form to edit patient details
     */
    public function edit(User $patient)
    {
        if (Auth::user()->isPatient() && Auth::id() !== $patient->id) {
            abort(403, 'Unauthorized.');
        }

        if (!$patient->isPatient()) {
            return redirect()->route('patients.index')->with('error', 'Target user is not a patient.');
        }

        return view('patients.edit', compact('patient'));
    }

    /**
     * Update patient details in database
     */
    public function update(Request $request, User $patient)
    {
        if (Auth::user()->isPatient() && Auth::id() !== $patient->id) {
            abort(403, 'Unauthorized.');
        }

        if (!$patient->isPatient()) {
            return redirect()->route('patients.index')->with('error', 'Target user is not a patient.');
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $patient->id,
            'phone' => 'nullable|string|max:20',
        ];

        if (Auth::user()->isAdmin()) {
            $rules['status'] = 'required|in:active,inactive';
        }

        if ($request->filled('password')) {
            $rules['password'] = 'nullable|string|min:6';
        }

        $validated = $request->validate($rules);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ];

        if (Auth::user()->isAdmin() && isset($validated['status'])) {
            $updateData['status'] = $validated['status'];
        }

        if ($request->filled('password')) {
            $updateData['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $patient->update($updateData);

        return redirect()->route('patients.show', $patient)->with('success', "Patient {$patient->name}'s details updated successfully.");
    }

    /**
     * Delete patient account (Admin only)
     */
    public function destroy(User $patient)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only administrators can delete patient accounts.');
        }

        if (!$patient->isPatient()) {
            return back()->with('error', 'Target user is not a patient.');
        }

        $patient->appointments()->delete();
        $patient->delete();

        return redirect()->route('patients.index')->with('success', "Patient account {$patient->name} removed successfully.");
    }

    /**
     * Toggle patient active/inactive status (Admin only)
     */
    public function toggleStatus(User $patient)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Only administrators can activate or deactivate patient accounts.');
        }

        if (!$patient->isPatient()) {
            return back()->with('error', 'Target user is not a patient.');
        }

        $newStatus = $patient->status === 'inactive' ? 'active' : 'inactive';
        $patient->update(['status' => $newStatus]);

        $actionText = $newStatus === 'active' ? 'activated' : 'deactivated';
        return back()->with('success', "Patient account {$patient->name} has been {$actionText} successfully.");
    }
}
