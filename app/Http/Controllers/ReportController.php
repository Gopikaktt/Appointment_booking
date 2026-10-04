<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Display Doctor-wise & Date-wise Appointment Reports
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->isPatient()) {
            abort(403, 'Unauthorized. Reports are available for staff and administrators only.');
        }

        // Filter inputs
        $doctorId = $request->get('doctor_id');
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $status = $request->get('status');
        $search = $request->get('search');
        $export = $request->get('export');

        // If user is doctor, lock doctor_id to their doctor profile ID
        if ($user->isDoctor()) {
            $doctorProfile = $user->doctorProfile;
            $doctorId = $doctorProfile ? $doctorProfile->id : null;
        }

        // Get list of doctors for dropdown filter (Admin only)
        $doctorsList = Doctor::orderBy('name')->get();

        // Build Query for Appointments
        $query = Appointment::with(['doctor', 'patient']);

        if ($doctorId) {
            $query->where('doctor_id', $doctorId);
        }

        if ($fromDate) {
            $query->whereDate('appointment_date', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('appointment_date', '<=', $toDate);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($search) {
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

        // Handle CSV Export
        if ($export === 'csv') {
            return $this->exportCsv($query->orderBy('appointment_date', 'desc')->orderBy('appointment_time', 'asc')->get());
        }

        // Aggregate statistics for filtered scope
        $stats = [
            'total' => (clone $query)->count(),
            'pending' => (clone $query)->where('status', 'Pending')->count(),
            'confirmed' => (clone $query)->where('status', 'Confirmed')->count(),
            'completed' => (clone $query)->where('status', 'Completed')->count(),
            'cancelled' => (clone $query)->where('status', 'Cancelled')->count(),
        ];

        // Doctor-wise Summary Data
        $doctorSummaries = Doctor::withCount([
            'appointments as total_appointments' => function($q) use ($fromDate, $toDate, $status) {
                if ($fromDate) $q->whereDate('appointment_date', '>=', $fromDate);
                if ($toDate) $q->whereDate('appointment_date', '<=', $toDate);
                if ($status) $q->where('status', $status);
            },
            'appointments as pending_appointments' => function($q) use ($fromDate, $toDate) {
                $q->where('status', 'Pending');
                if ($fromDate) $q->whereDate('appointment_date', '>=', $fromDate);
                if ($toDate) $q->whereDate('appointment_date', '<=', $toDate);
            },
            'appointments as confirmed_appointments' => function($q) use ($fromDate, $toDate) {
                $q->where('status', 'Confirmed');
                if ($fromDate) $q->whereDate('appointment_date', '>=', $fromDate);
                if ($toDate) $q->whereDate('appointment_date', '<=', $toDate);
            },
            'appointments as completed_appointments' => function($q) use ($fromDate, $toDate) {
                $q->where('status', 'Completed');
                if ($fromDate) $q->whereDate('appointment_date', '>=', $fromDate);
                if ($toDate) $q->whereDate('appointment_date', '<=', $toDate);
            },
            'appointments as cancelled_appointments' => function($q) use ($fromDate, $toDate) {
                $q->where('status', 'Cancelled');
                if ($fromDate) $q->whereDate('appointment_date', '>=', $fromDate);
                if ($toDate) $q->whereDate('appointment_date', '<=', $toDate);
            }
        ]);

        if ($doctorId) {
            $doctorSummaries->where('id', $doctorId);
        }

        $doctorSummaries = $doctorSummaries->orderBy('name')->get();

        // Paginated records for detailed view
        $appointments = $query->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'asc')
            ->paginate(15);

        return view('reports.index', compact(
            'appointments',
            'doctorsList',
            'doctorId',
            'fromDate',
            'toDate',
            'status',
            'search',
            'stats',
            'doctorSummaries'
        ));
    }

    /**
     * Export appointments to CSV format
     */
    private function exportCsv($appointments)
    {
        $fileName = 'doctor_date_wise_appointment_report_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'Appointment ID',
            'Date',
            'Time',
            'Doctor Name',
            'Specialization',
            'Patient Name',
            'Patient Email',
            'Patient Phone',
            'Symptoms / Notes',
            'Status',
            'Booked At'
        ];

        $callback = function() use($appointments, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($appointments as $apt) {
                $row = [
                    'Appointment ID'  => $apt->id,
                    'Date'            => $apt->appointment_date ? $apt->appointment_date->format('Y-m-d') : '',
                    'Time'            => $apt->appointment_time,
                    'Doctor Name'     => $apt->doctor ? $apt->doctor->name : 'N/A',
                    'Specialization'  => $apt->doctor ? $apt->doctor->specialization : 'N/A',
                    'Patient Name'    => $apt->patient ? $apt->patient->name : 'N/A',
                    'Patient Email'   => $apt->patient ? $apt->patient->email : 'N/A',
                    'Patient Phone'   => $apt->patient ? ($apt->patient->phone ?? 'N/A') : 'N/A',
                    'Symptoms'        => $apt->symptoms_notes ?? '',
                    'Status'          => $apt->status,
                    'Booked At'       => $apt->created_at ? $apt->created_at->format('Y-m-d H:i') : ''
                ];

                fputcsv($file, array_values($row));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
