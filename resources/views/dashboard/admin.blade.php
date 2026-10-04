@extends('layouts.app')

@section('title', 'Admin Dashboard - MediCare')
@section('page-header', 'Administrator Overview')

@section('content')
<div class="stats-grid">
    <a href="{{ route('doctors.index') }}" style="text-decoration: none; color: inherit;">
        <div class="stat-card" style="cursor: pointer;">
            <div class="stat-icon" style="background: rgba(13, 148, 136, 0.15); color: #2dd4bf;">
                <i class="fa-solid fa-user-doctor"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['total_doctors'] }}</div>
                <div class="stat-label">Total Doctors</div>
            </div>
        </div>
    </a>

    <a href="{{ route('patients.index') }}" style="text-decoration: none; color: inherit;">
        <div class="stat-card" style="cursor: pointer;">
            <div class="stat-icon" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8;">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['total_patients'] }}</div>
                <div class="stat-label">Registered Patients</div>
            </div>
        </div>
    </a>

    <a href="{{ route('dashboard', ['status' => 'Today']) }}#recent-appointments" style="text-decoration: none; color: inherit;">
        <div class="stat-card" style="cursor: pointer; {{ request('status') === 'Today' ? 'border-color: #38bdf8; background: rgba(56, 189, 248, 0.1);' : '' }}">
            <div class="stat-icon" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8;">
                <i class="fa-solid fa-calendar-day"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value" style="color: #38bdf8;">{{ $stats['today_appointments'] }}</div>
                <div class="stat-label">Today's Visits</div>
            </div>
        </div>
    </a>

    <a href="{{ route('dashboard', ['status' => 'Pending']) }}#recent-appointments" style="text-decoration: none; color: inherit;">
        <div class="stat-card" style="cursor: pointer; {{ request('status') === 'Pending' ? 'border-color: #fbbf24; background: rgba(245, 158, 11, 0.1);' : '' }}">
            <div class="stat-icon" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24;">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value" style="color: #fbbf24;">{{ $stats['pending_appointments'] }}</div>
                <div class="stat-label">Pending Bookings</div>
            </div>
        </div>
    </a>

    <a href="{{ route('dashboard', ['status' => 'Confirmed']) }}#recent-appointments" style="text-decoration: none; color: inherit;">
        <div class="stat-card" style="cursor: pointer; {{ request('status') === 'Confirmed' ? 'border-color: #60a5fa; background: rgba(59, 130, 246, 0.1);' : '' }}">
            <div class="stat-icon" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa;">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value" style="color: #60a5fa;">{{ $stats['confirmed_appointments'] }}</div>
                <div class="stat-label">Confirmed Bookings</div>
            </div>
        </div>
    </a>

    <a href="{{ route('dashboard', ['status' => 'Completed']) }}#recent-appointments" style="text-decoration: none; color: inherit;">
        <div class="stat-card" style="cursor: pointer; {{ request('status') === 'Completed' ? 'border-color: #34d399; background: rgba(16, 185, 129, 0.1);' : '' }}">
            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.15); color: #34d399;">
                <i class="fa-solid fa-check-double"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value" style="color: #34d399;">{{ $stats['completed_appointments'] }}</div>
                <div class="stat-label">Completed Visits</div>
            </div>
        </div>
    </a>

    <a href="{{ route('dashboard', ['status' => 'Cancelled']) }}#recent-appointments" style="text-decoration: none; color: inherit;">
        <div class="stat-card" style="cursor: pointer; {{ request('status') === 'Cancelled' ? 'border-color: #fca5a5; background: rgba(239, 68, 68, 0.1);' : 'border-color: rgba(239, 68, 68, 0.3);' }}">
            <div class="stat-icon" style="background: rgba(239, 68, 68, 0.15); color: #fca5a5;">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value" style="color: #fca5a5;">{{ $stats['cancelled_appointments'] }}</div>
                <div class="stat-label">Cancelled Visits</div>
            </div>
        </div>
    </a>
    <a href="{{ route('reports.index') }}" style="text-decoration: none; color: inherit;">
        <div class="stat-card" style="cursor: pointer; border-color: rgba(192, 132, 252, 0.3);">
            <div class="stat-icon" style="background: rgba(168, 85, 247, 0.15); color: #c084fc;">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value" style="font-size: 1.4rem; color: #c084fc;">Reports</div>
                <div class="stat-label">Doctor & Date Wise</div>
            </div>
        </div>
    </a>
</div>

<!-- Pending Doctor Applications Alert Block -->
@if(isset($pendingDoctors) && $pendingDoctors->count() > 0)
    <div class="card" style="border: 1px solid var(--warning); background: rgba(245, 158, 11, 0.08); margin-bottom: 2rem;">
        <div class="card-header">
            <div class="card-title" style="color: #fbbf24;">
                <i class="fa-solid fa-user-clock"></i> Pending Doctor Applications ({{ $pendingDoctors->count() }})
            </div>
            <a href="{{ route('doctors.index', ['status' => 'inactive']) }}" class="btn btn-secondary btn-sm">View In Directory</a>
        </div>
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Doctor Name</th>
                        <th>Specialization</th>
                        <th>Email & Phone</th>
                        <th>Applied On</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingDoctors as $pDoctor)
                        <tr>
                            <td style="font-weight: 700;">{{ $pDoctor->name }}</td>
                            <td><span style="color: var(--accent); font-weight: 600;">{{ $pDoctor->specialization }}</span></td>
                            <td>
                                <div>{{ $pDoctor->email }}</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $pDoctor->phone }}</div>
                            </td>
                            <td>{{ $pDoctor->created_at ? $pDoctor->created_at->format('M d, Y') : 'Recent' }}</td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                    <form action="{{ route('doctors.approve', $pDoctor) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fa-solid fa-check"></i> Approve & Activate
                                        </button>
                                    </form>
                                    <a href="{{ route('doctors.edit', $pDoctor) }}" class="btn btn-secondary btn-sm" title="Edit Schedule / Room">
                                        <i class="fa-solid fa-pen"></i> Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
    <!-- Recent Appointments -->
    <div class="card" id="recent-appointments">
        <div class="card-header" style="flex-wrap: wrap; gap: 10px;">
            <div class="card-title"><i class="fa-solid fa-list-check" style="color: var(--primary)"></i> Bookings & Status Overview</div>

            <!-- Filter & Reset Controls -->
            <form action="{{ route('dashboard') }}#recent-appointments" method="GET" style="display: flex; gap: 8px; align-items: center;">
                <select name="status" class="form-select form-select-sm" style="max-width: 170px;" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="Today" {{ request('status') == 'Today' ? 'selected' : '' }}>Today's Visits</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Confirmed" {{ request('status') == 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                    <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>

                @if(request('status'))
                    <a href="{{ route('dashboard') }}#recent-appointments" class="btn btn-secondary btn-sm" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i> Reset
                    </a>
                @endif

                <a href="{{ route('appointments.history') }}" class="btn btn-secondary btn-sm">Manage All</a>
            </form>
        </div>

        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Date & Time</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentAppointments as $appointment)
                        <tr onclick="if (!event.target.closest('a, button, select, input')) window.location.href='{{ route('appointments.show', $appointment) }}';" style="cursor: pointer;">
                            <td>
                                <a href="{{ route('appointments.show', $appointment) }}" style="text-decoration: none; color: inherit;">
                                    <div style="font-weight: 600; color: var(--accent);">{{ $appointment->patient->name }}</div>
                                </a>
                                <div style="font-size: 0.8rem; color: var(--text-muted)">{{ $appointment->patient->email }}</div>
                            </td>
                            <td>
                                <a href="{{ route('doctors.show', $appointment->doctor) }}" style="text-decoration: none; color: inherit;">
                                    <div style="font-weight: 600; color: var(--text-main);">{{ $appointment->doctor->name }}</div>
                                </a>
                                <div style="font-size: 0.8rem; color: var(--text-muted)">{{ $appointment->doctor->specialization }}</div>
                            </td>
                            <td>
                                <div><i class="fa-regular fa-calendar-days"></i> {{ $appointment->appointment_date->format('M d, Y') }}</div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);"><i class="fa-regular fa-clock"></i> {{ $appointment->appointment_time }}</div>
                            </td>
                            <td>
                                <form action="{{ route('appointments.updateStatus', $appointment) }}" method="POST" style="margin: 0;">
                                    @csrf
                                    @method('PATCH')
                                    <select name="status" class="form-select form-select-sm" style="font-size: 0.8rem; padding: 0.35rem 0.5rem; width: 130px; font-weight: 600; cursor: pointer;" onchange="this.form.submit()">
                                        <option value="Pending" {{ $appointment->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="Confirmed" {{ $appointment->status === 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                                        <option value="Completed" {{ $appointment->status === 'Completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="Cancelled" {{ $appointment->status === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </form>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 4px; justify-content: flex-end; align-items: center;">
                                    <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-secondary btn-sm" title="View Appointment Details">
                                        <i class="fa-solid fa-eye"></i> View
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2rem;">No appointments found for the selected status.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Actions & Featured Doctors -->
    <div>
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fa-solid fa-bolt" style="color: var(--accent)"></i> Quick Actions</div>
            </div>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <a href="{{ route('doctors.create') }}" class="btn btn-primary" style="justify-content: flex-start;">
                    <i class="fa-solid fa-user-plus"></i> Add New Doctor
                </a>
                <a href="{{ route('doctors.index') }}" class="btn btn-secondary" style="justify-content: flex-start;">
                    <i class="fa-solid fa-user-doctor"></i> Manage Doctors
                </a>
                <a href="{{ route('patients.index') }}" class="btn btn-secondary" style="justify-content: flex-start;">
                    <i class="fa-solid fa-users"></i> Manage Patients
                </a>
                <a href="{{ route('appointments.history') }}" class="btn btn-secondary" style="justify-content: flex-start;">
                    <i class="fa-solid fa-calendar-days"></i> Manage All Appointments
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fa-solid fa-award" style="color: #fbbf24"></i> Active Doctors</div>
            </div>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                @foreach($topDoctors as $doc)
                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <div>
                            <div style="font-weight: 600; font-size: 0.95rem;">{{ $doc->name }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted)">{{ $doc->specialization }}</div>
                        </div>
                        <span class="badge badge-active">${{ number_format($doc->consultation_fee, 2) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
