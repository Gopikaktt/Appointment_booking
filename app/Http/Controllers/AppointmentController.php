<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    /**
     * Show booking form
     */
    public function create(Request $request)
    {
        if (!Auth::user()->isPatient()) {
            return redirect()->route('dashboard')->with('error', 'Only registered patients can book doctor appointments.');
        }

        $doctors = Doctor::where('status', 'active')->orderBy('name')->get();
        $selectedDoctorId = $request->query('doctor_id');

        return view('appointments.create', compact('doctors', 'selectedDoctorId'));
    }

    /**
     * AJAX endpoint to fetch available slots for a doctor on a specific date
     */
    public function getSlots(Doctor $doctor, Request $request)
    {
        if ($doctor->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'This doctor profile is currently pending administrator approval and cannot accept bookings.',
                'slots' => [],
            ], 403);
        }

        $request->validate([
            'date' => 'required|date|after_or_equal:today',
        ]);

        $slots = $doctor->getAvailableSlots($request->date);

        return response()->json([
            'success' => true,
            'doctor' => $doctor->name,
            'date' => $request->date,
            'slots' => $slots,
        ]);
    }

    /**
     * Store a new appointment with strict duplicate booking prevention
     */
    public function store(Request $request)
    {
        if (!Auth::user()->isPatient()) {
            return redirect()->route('dashboard')->with('error', 'Only registered patients can book doctor appointments.');
        }

        $validated = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|string',
            'symptoms_notes' => 'nullable|string|max:1000',
        ]);

        $doctor = Doctor::findOrFail($request->doctor_id);

        if ($doctor->status !== 'active') {
            return back()->withInput()->withErrors([
                'doctor_id' => 'Selected doctor profile is currently pending administrator approval and cannot accept bookings.',
            ]);
        }

        $patientId = Auth::id();

        // 1. Prevent Duplicate Booking for same doctor at same date & time
        $existingDoctorBooking = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->where('status', '!=', 'Cancelled')
            ->exists();

        if ($existingDoctorBooking) {
            return back()->withInput()->withErrors([
                'appointment_time' => "The slot ({$request->appointment_time}) with {$doctor->name} on {$request->appointment_date} is already booked by another patient. Please select another time slot.",
            ]);
        }

        // 2. Prevent Patient from booking overlapping appointment for themselves at same time
        $existingPatientBooking = Appointment::where('patient_id', $patientId)
            ->whereDate('appointment_date', $request->appointment_date)
            ->where('appointment_time', $request->appointment_time)
            ->where('status', '!=', 'Cancelled')
            ->exists();

        if ($existingPatientBooking) {
            return back()->withInput()->withErrors([
                'appointment_time' => "You already have another appointment scheduled at {$request->appointment_time} on {$request->appointment_date}.",
            ]);
        }

        // 3. Prevent Patient from booking multiple appointments with the SAME doctor on the SAME date
        $existingPatientSameDoctorBooking = Appointment::where('patient_id', $patientId)
            ->where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $request->appointment_date)
            ->where('status', '!=', 'Cancelled')
            ->exists();

        if ($existingPatientSameDoctorBooking) {
            return back()->withInput()->withErrors([
                'appointment_date' => "You already have an active appointment scheduled with {$doctor->name} on {$request->appointment_date}. A patient cannot book multiple appointments with the same doctor on the same day.",
            ]);
        }

        // Create the appointment
        $appointment = Appointment::create([
            'patient_id' => $patientId,
            'doctor_id' => $doctor->id,
            'appointment_date' => $request->appointment_date,
            'appointment_time' => $request->appointment_time,
            'status' => 'Pending',
            'symptoms_notes' => $request->symptoms_notes,
        ]);

        return redirect()->route('appointments.history')->with('success', 'Appointment booked successfully! Your status is currently Pending confirmation.');
    }

    /**
     * Display important appointment details
     */
    public function show(Appointment $appointment)
    {
        $user = Auth::user();

        if ($user->isPatient()) {
            if ($appointment->patient_id !== $user->id) {
                abort(403, 'Unauthorized access to appointment details.');
            }
        } elseif ($user->isDoctor()) {
            $doctorProfile = Doctor::where('user_id', $user->id)
                ->orWhere('email', $user->email)
                ->first();

            if (!$doctorProfile || $appointment->doctor_id !== $doctorProfile->id) {
                abort(403, 'Unauthorized access to patient appointment.');
            }
        }

        $appointment->load(['doctor', 'patient']);

        return view('appointments.show', compact('appointment'));
    }

    /**
     * View Appointment History
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        $query = Appointment::with(['doctor', 'patient']);

        // Scope records according to user role
        if ($user->isPatient()) {
            $query->where('patient_id', $user->id);
        } elseif ($user->isDoctor()) {
            $doctorProfile = Doctor::where('user_id', $user->id)
                ->orWhere('email', $user->email)
                ->first();

            if ($doctorProfile) {
                $query->where('doctor_id', $doctorProfile->id);
            } else {
                $query->whereRaw('1 = 0'); // No doctor profile linked
            }
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('doctor_id') && $user->isAdmin()) {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('patient', function($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%")
                       ->orWhere('phone', 'like', "%{$search}%");
                })->orWhereHas('doctor', function($dq) use ($search) {
                    $dq->where('name', 'like', "%{$search}%")
                       ->orWhere('specialization', 'like', "%{$search}%");
                })->orWhere('symptoms_notes', 'like', "%{$search}%");
            });
        }

        $appointments = $query->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'asc')
            ->paginate(10);

        $doctors = $user->isAdmin() ? Doctor::all() : collect();

        return view('appointments.history', compact('appointments', 'doctors'));
    }

    /**
     * Update Appointment Status (Pending -> Confirmed -> Completed -> Cancelled)
     */
    public function updateStatus(Request $request, Appointment $appointment)
    {
        $user = Auth::user();

        // Check permission
        if ($user->isPatient()) {
            if ($appointment->patient_id !== $user->id) {
                abort(403, 'Unauthorized action.');
            }
            if ($request->status !== 'Cancelled') {
                return back()->with('error', 'Patients can only cancel their appointments.');
            }
        } elseif ($user->isDoctor()) {
            $doctorProfile = Doctor::where('user_id', $user->id)
                ->orWhere('email', $user->email)
                ->first();

            if (!$doctorProfile || $appointment->doctor_id !== $doctorProfile->id) {
                abort(403, 'Unauthorized. You can only manage appointments assigned to your profile.');
            }
        }

        $validated = $request->validate([
            'status' => 'required|in:Pending,Confirmed,Completed,Cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);

        $appointment->status = $validated['status'];
        if (isset($validated['notes'])) {
            $appointment->notes = $validated['notes'];
        }
        $appointment->save();

        return back()->with('success', "Appointment status updated to {$appointment->status}.");
    }
}
