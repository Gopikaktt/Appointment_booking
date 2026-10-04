@extends('layouts.app')

@section('title', 'Appointment Reports - MediCare')
@section('page-header', 'Appointment Reports (Doctor & Date Wise)')

@section('content')
<style>
/* Print styling for clean physical printing / PDF export */
@media print {
    .sidebar, .topbar, .filter-card, .btn-no-print, .pagination, header, nav {
        display: none !important;
    }
    .main-wrapper {
        margin-left: 0 !important;
        padding: 0 !important;
    }
    .content-container {
        padding: 0 !important;
    }
    body {
        background: #fff !important;
        color: #000 !important;
    }
    .card {
        background: #fff !important;
        border: 1px solid #ccc !important;
        color: #000 !important;
        box-shadow: none !important;
        margin-bottom: 1.5rem !important;
        page-break-inside: avoid;
    }
    .custom-table th {
        background: #f1f5f9 !important;
        color: #000 !important;
        border-bottom: 2px solid #334155 !important;
    }
    .custom-table td {
        color: #000 !important;
        border-bottom: 1px solid #e2e8f0 !important;
    }
    .badge {
        border: 1px solid #000 !important;
        color: #000 !important;
        background: transparent !important;
    }
    .print-header {
        display: block !important;
        text-align: center;
        margin-bottom: 2rem;
    }
}

.print-header {
    display: none;
}
</style>

<!-- Print Header (only visible on print) -->
<div class="print-header">
    <h1 style="font-size: 1.8rem; margin-bottom: 0.25rem; color: #000;">MediCare Medical Center</h1>
    <h3 style="font-size: 1.2rem; color: #475569; font-weight: 500;">Doctor-Wise & Date-Wise Appointment Report</h3>
    <div style="font-size: 0.85rem; color: #64748b; margin-top: 0.5rem;">
        Generated on: {{ now()->format('F j, Y, g:i A') }} | Date Range: {{ $fromDate ? $fromDate : 'All Time' }} to {{ $toDate ? $toDate : 'Present' }}
    </div>
    <hr style="margin-top: 1rem; border-color: #cbd5e1;">
</div>

<!-- Filter Card -->
<div class="card filter-card">
    <div class="card-header" style="margin-bottom: 1rem;">
        <div class="card-title"><i class="fa-solid fa-filter" style="color: var(--primary);"></i> Filter Reports by Doctor & Date Range</div>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" class="btn btn-secondary btn-sm btn-no-print" title="Print this report">
                <i class="fa-solid fa-print"></i> Print Report
            </button>
            <a href="{{ route('reports.index', array_merge(request()->all(), ['export' => 'csv'])) }}" class="btn btn-primary btn-sm btn-no-print" title="Export report data to CSV file">
                <i class="fa-solid fa-file-csv"></i> Export CSV
            </a>
        </div>
    </div>

    <form action="{{ route('reports.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
        <!-- Doctor Filter -->
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label"><i class="fa-solid fa-user-doctor" style="color: var(--accent);"></i> Select Doctor</label>
            @if(auth()->user()->isAdmin())
                <select name="doctor_id" class="form-select">
                    <option value="">All Doctors</option>
                    @foreach($doctorsList as $doc)
                        <option value="{{ $doc->id }}" {{ $doctorId == $doc->id ? 'selected' : '' }}>
                            {{ $doc->name }} ({{ $doc->specialization }})
                        </option>
                    @endforeach
                </select>
            @else
                <input type="text" class="form-control" value="{{ auth()->user()->doctorProfile->name ?? auth()->user()->name }}" disabled readonly>
                <input type="hidden" name="doctor_id" value="{{ $doctorId }}">
            @endif
        </div>

        <!-- From Date -->
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label"><i class="fa-regular fa-calendar-days" style="color: var(--primary);"></i> From Date</label>
            <input type="date" name="from_date" class="form-control" value="{{ $fromDate }}">
        </div>

        <!-- To Date -->
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label"><i class="fa-regular fa-calendar-days" style="color: var(--primary);"></i> To Date</label>
            <input type="date" name="to_date" class="form-control" value="{{ $toDate }}">
        </div>

        <!-- Status Filter -->
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label"><i class="fa-solid fa-tag" style="color: #fbbf24;"></i> Status</label>
            <select name="status" class="form-select">
                <option value="">All Statuses</option>
                <option value="Pending" {{ $status == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Confirmed" {{ $status == 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="Completed" {{ $status == 'Completed' ? 'selected' : '' }}>Completed</option>
                <option value="Cancelled" {{ $status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>

        <!-- Search Input -->
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label"><i class="fa-solid fa-magnifying-glass" style="color: var(--text-muted);"></i> Search Keywords</label>
            <input type="text" name="search" class="form-control" placeholder="Patient / Doctor / Symptoms..." value="{{ $search }}">
        </div>

        <!-- Actions -->
        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn btn-primary" style="flex: 1;"><i class="fa-solid fa-magnifying-glass"></i> Apply Filter</button>
            @if($doctorId || $fromDate || $toDate || $status || $search)
                <a href="{{ route('reports.index') }}" class="btn btn-danger btn-no-print" style="background: rgba(239, 68, 68, 0.2); border-color: rgba(239, 68, 68, 0.4); color: #fca5a5;" title="Reset filters">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Summary Metric Cards Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8;">
            <i class="fa-solid fa-calendar-check"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">Total Appointments</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24;">
            <i class="fa-solid fa-hourglass-half"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['pending'] }}</div>
            <div class="stat-label">Pending Approval</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa;">
            <i class="fa-solid fa-user-check"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['confirmed'] }}</div>
            <div class="stat-label">Confirmed Visits</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(16, 185, 129, 0.15); color: #34d399;">
            <i class="fa-solid fa-square-check"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['completed'] }}</div>
            <div class="stat-label">Completed Visits</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: rgba(239, 68, 68, 0.15); color: #fca5a5;">
            <i class="fa-solid fa-ban"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['cancelled'] }}</div>
            <div class="stat-label">Cancelled Visits</div>
        </div>
    </div>
</div>

<!-- Doctor-Wise Summary Breakdown Table (Admin & Overview) -->
@if(auth()->user()->isAdmin())
<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="fa-solid fa-hospital-user" style="color: var(--accent);"></i> Doctor-Wise Summary Breakdown</div>
    </div>
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Doctor Name</th>
                    <th>Specialization</th>
                    <th>Total</th>
                    <th>Pending</th>
                    <th>Confirmed</th>
                    <th>Completed</th>
                    <th>Cancelled</th>
                    <th style="text-align: right;" class="btn-no-print">Quick Filter</th>
                </tr>
            </thead>
            <tbody>
                @forelse($doctorSummaries as $docSummary)
                    <tr>
                        <td>
                            <div style="font-weight: 700; color: var(--text-main);">{{ $docSummary->name }}</div>
                        </td>
                        <td>
                            <div style="color: var(--accent); font-size: 0.85rem;">{{ $docSummary->specialization }}</div>
                        </td>
                        <td><strong>{{ $docSummary->total_appointments }}</strong></td>
                        <td><span class="badge badge-pending">{{ $docSummary->pending_appointments }}</span></td>
                        <td><span class="badge badge-confirmed">{{ $docSummary->confirmed_appointments }}</span></td>
                        <td><span class="badge badge-completed">{{ $docSummary->completed_appointments }}</span></td>
                        <td><span class="badge badge-cancelled">{{ $docSummary->cancelled_appointments }}</span></td>
                        <td style="text-align: right;" class="btn-no-print">
                            <a href="{{ route('reports.index', array_merge(request()->all(), ['doctor_id' => $docSummary->id])) }}" class="btn btn-secondary btn-sm">
                                <i class="fa-solid fa-filter"></i> Filter Doctor
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                            No doctor summary records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Date-Wise Detailed Appointment Report Table -->
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="fa-solid fa-list-check" style="color: var(--primary);"></i> 
            Date-Wise & Doctor-Wise Detailed Records 
            @if($fromDate || $toDate)
                <span style="font-size: 0.85rem; color: var(--text-muted); font-weight: normal;">
                    ({{ $fromDate ? $fromDate : 'Start' }} to {{ $toDate ? $toDate : 'Today' }})
                </span>
            @endif
        </div>
        <div style="font-size: 0.85rem; color: var(--text-muted);" class="btn-no-print">
            Showing {{ $appointments->firstItem() ?? 0 }} - {{ $appointments->lastItem() ?? 0 }} of {{ $appointments->total() }} records
        </div>
    </div>

    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Date & Time</th>
                    <th>Doctor Details</th>
                    <th>Patient Details</th>
                    <th>Symptoms / Notes</th>
                    <th>Status</th>
                    <th style="text-align: right;" class="btn-no-print">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($appointments as $apt)
                    <tr onclick="if (!event.target.closest('a, button, select, input')) window.location.href='{{ route('appointments.show', $apt) }}';" style="cursor: pointer;">
                        <td>
                            <div style="font-weight: 700; color: var(--text-main);">
                                <i class="fa-regular fa-calendar-days" style="color: var(--primary);"></i> {{ $apt->appointment_date ? $apt->appointment_date->format('M d, Y') : 'N/A' }}
                            </div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);">
                                <i class="fa-regular fa-clock" style="color: var(--accent);"></i> {{ $apt->appointment_time }}
                            </div>
                        </td>
                        <td>
                            <div style="font-weight: 700; color: var(--accent);">
                                <i class="fa-solid fa-user-doctor"></i> {{ $apt->doctor->name ?? 'N/A' }}
                            </div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">
                                {{ $apt->doctor->specialization ?? 'General' }}
                            </div>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: var(--text-main);">
                                {{ $apt->patient->name ?? 'N/A' }}
                            </div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">
                                <i class="fa-regular fa-envelope"></i> {{ $apt->patient->email ?? 'N/A' }}
                                @if($apt->patient && $apt->patient->phone)
                                    | <i class="fa-solid fa-phone"></i> {{ $apt->patient->phone }}
                                @endif
                            </div>
                        </td>
                        <td>
                            <div style="font-size: 0.85rem; color: var(--text-main); max-width: 220px;">
                                {{ $apt->symptoms_notes ? Str::limit($apt->symptoms_notes, 70) : 'No symptoms noted' }}
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-{{ strtolower($apt->status) }}">
                                {{ $apt->status }}
                            </span>
                        </td>
                        <td style="text-align: right;" class="btn-no-print">
                            <a href="{{ route('appointments.show', $apt) }}" class="btn btn-secondary btn-sm" title="View Details">
                                <i class="fa-solid fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 3rem;">
                            <i class="fa-solid fa-file-excel" style="font-size: 2.5rem; margin-bottom: 0.75rem; display: block; color: var(--text-muted);"></i>
                            No appointment records match the selected doctor, date range, or status filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;" class="btn-no-print">
        {{ $appointments->withQueryString()->links() }}
    </div>
</div>
@endsection
