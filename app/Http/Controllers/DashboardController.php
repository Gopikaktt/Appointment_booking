<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $statusFilter = $request->query('status');

        if ($user->isAdmin()) {
            // Admin Dashboard
            $today = now()->toDateString();
            $stats = [
                'total_doctors' => Doctor::count(),
                'total_patients' => User::where('role', 'patient')->count(),
                'today_appointments' => Appointment::whereDate('appointment_date', $today)->count(),
                'pending_appointments' => Appointment::where('status', 'Pending')->count(),
                'confirmed_appointments' => Appointment::where('status', 'Confirmed')->count(),
                'completed_appointments' => Appointment::where('status', 'Completed')->count(),
                'cancelled_appointments' => Appointment::where('status', 'Cancelled')->count(),
                'pending_doctors' => Doctor::where('status', 'inactive')->count(),
            ];

            $searchFilter = $request->query('search');

            $recentQuery = Appointment::with(['doctor', 'patient']);
            if ($statusFilter === 'Today') {
                $recentQuery->whereDate('appointment_date', $today);
            } elseif ($statusFilter) {
                $recentQuery->where('status', $statusFilter);
            }

            if ($searchFilter) {
                $recentQuery->where(function($q) use ($searchFilter) {
                    $q->whereHas('patient', function($pq) use ($searchFilter) {
                        $pq->where('name', 'like', "%{$searchFilter}%")
                           ->orWhere('email', 'like', "%{$searchFilter}%")
                           ->orWhere('phone', 'like', "%{$searchFilter}%");
                    })->orWhereHas('doctor', function($dq) use ($searchFilter) {
                        $dq->where('name', 'like', "%{$searchFilter}%")
                           ->orWhere('specialization', 'like', "%{$searchFilter}%");
                    })->orWhere('symptoms_notes', 'like', "%{$searchFilter}%");
                });
            }

            $recentAppointments = $recentQuery->orderBy('created_at', 'desc')->take(10)->get();

            $topDoctors = Doctor::where('status', 'active')
                ->withCount(['appointments' => function ($q) {
                    $q->where('status', 'Completed');
                }])->orderBy('appointments_count', 'desc')->take(4)->get();

            $pendingDoctors = Doctor::where('status', 'inactive')->get();

            return view('dashboard.admin', compact('stats', 'recentAppointments', 'topDoctors', 'statusFilter', 'pendingDoctors'));

        } elseif ($user->isDoctor()) {
            // Doctor Console / Portal Dashboard
            $doctorProfile = Doctor::where('user_id', $user->id)
                ->orWhere('email', $user->email)
                ->first();

            $doctorId = $doctorProfile ? $doctorProfile->id : 0;
            $today = now()->toDateString();
            $searchFilter = $request->query('search');

            // Today's Appointments
            $todayAppointments = Appointment::with('patient')
                ->where('doctor_id', $doctorId)
                ->whereDate('appointment_date', '=', $today)
                ->orderBy('appointment_time', 'asc')
                ->get();

            // Upcoming Appointments (strictly in future / after today)
            $upcomingAppointments = Appointment::with('patient')
                ->where('doctor_id', $doctorId)
                ->whereDate('appointment_date', '>', $today)
                ->whereIn('status', ['Pending', 'Confirmed'])
                ->orderBy('appointment_date', 'asc')
                ->orderBy('appointment_time', 'asc')
                ->get();

            // All Doctor Appointments Query
            $doctorAppointmentsQuery = Appointment::with(['patient', 'doctor'])
                ->where('doctor_id', $doctorId);

            $allDoctorAppointments = (clone $doctorAppointmentsQuery)->get();

            if ($statusFilter) {
                $doctorAppointmentsQuery->where('status', $statusFilter);
            }

            if ($searchFilter) {
                $doctorAppointmentsQuery->where(function($q) use ($searchFilter) {
                    $q->whereHas('patient', function($pq) use ($searchFilter) {
                        $pq->where('name', 'like', "%{$searchFilter}%")
                           ->orWhere('email', 'like', "%{$searchFilter}%")
                           ->orWhere('phone', 'like', "%{$searchFilter}%");
                    })->orWhere('symptoms_notes', 'like', "%{$searchFilter}%");
                });
            }

            $recentAppointments = $doctorAppointmentsQuery->orderBy('appointment_date', 'desc')
                ->orderBy('appointment_time', 'asc')
                ->get();

            $stats = [
                'total' => $allDoctorAppointments->count(),
                'today' => $todayAppointments->count(),
                'upcoming' => $upcomingAppointments->count(),
                'pending' => $allDoctorAppointments->where('status', 'Pending')->count(),
                'confirmed' => $allDoctorAppointments->where('status', 'Confirmed')->count(),
                'completed' => $allDoctorAppointments->where('status', 'Completed')->count(),
                'cancelled' => $allDoctorAppointments->where('status', 'Cancelled')->count(),
            ];

            return view('dashboard.doctor', compact(
                'stats',
                'todayAppointments',
                'upcomingAppointments',
                'recentAppointments',
                'doctorProfile',
                'statusFilter'
            ));

        } else {
            // Patient Dashboard
            $patientAppointmentsQuery = Appointment::with('doctor')
                ->where('patient_id', $user->id);

            $allPatientAppointments = (clone $patientAppointmentsQuery)->orderBy('appointment_date', 'desc')->get();

            if ($statusFilter) {
                $patientAppointmentsQuery->where('status', $statusFilter);
            }

            $recentAppointments = $patientAppointmentsQuery->orderBy('appointment_date', 'desc')->take(10)->get();

            $upcomingAppointment = Appointment::with('doctor')
                ->where('patient_id', $user->id)
                ->whereDate('appointment_date', '>=', now()->toDateString())
                ->whereIn('status', ['Pending', 'Confirmed'])
                ->orderBy('appointment_date', 'asc')
                ->first();

            $stats = [
                'total' => $allPatientAppointments->count(),
                'pending' => $allPatientAppointments->where('status', 'Pending')->count(),
                'confirmed' => $allPatientAppointments->where('status', 'Confirmed')->count(),
                'completed' => $allPatientAppointments->where('status', 'Completed')->count(),
                'cancelled' => $allPatientAppointments->where('status', 'Cancelled')->count(),
            ];

            $doctorQuery = Doctor::where('status', 'active');

            if ($request->filled('doctor_search')) {
                $search = $request->query('doctor_search');
                $doctorQuery->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('specialization', 'like', "%{$search}%")
                      ->orWhere('room_number', 'like', "%{$search}%");
                });
            }

            if ($request->filled('specialization')) {
                $doctorQuery->where('specialization', $request->query('specialization'));
            }

            $availableDoctors = $doctorQuery->orderBy('name')->get();
            $specializations = Doctor::where('status', 'active')->select('specialization')->distinct()->pluck('specialization');

            return view('dashboard.patient', compact('stats', 'upcomingAppointment', 'recentAppointments', 'availableDoctors', 'specializations', 'statusFilter'));
        }
    }
}
