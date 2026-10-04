@extends('layouts.app')

@section('title', 'Doctor Portal - MediCare')
@section('page-header', 'Doctor Portal')

@section('content')
<!-- Doctor Profile Welcome Banner -->
<div style="background: linear-gradient(135deg, rgba(13, 148, 136, 0.25), rgba(56, 189, 248, 0.15)); border: 1px solid var(--primary); border-radius: var(--radius-lg); padding: 1.75rem; margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div>
        <div style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase; color: var(--accent); letter-spacing: 1px;">
            <i class="fa-solid fa-stethoscope"></i> Specialist Physician Console
        </div>
        <div style="font-family: var(--font-title); font-size: 1.6rem; font-weight: 700; margin: 6px 0;">
            Welcome, {{ $doctorProfile->name ?? auth()->user()->name }}
        </div>
        <div style="font-size: 0.95rem; color: var(--text-main); display: flex; gap: 1.5rem; flex-wrap: wrap;">
            <span><i class="fa-solid fa-user-doctor" style="color: var(--accent)"></i> {{ $doctorProfile->specialization ?? 'Specialist' }}</span>
            <span><i class="fa-solid fa-door-open" style="color: #fbbf24"></i> {{ $doctorProfile->room_number ?? 'Consultation Room' }}</span>
            <span><i class="fa-solid fa-hand-holding-dollar" style="color: #34d399"></i> Fee: ${{ number_format($doctorProfile->consultation_fee ?? 100, 2) }}</span>
        </div>
    </div>
    @if($doctorProfile)
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <a href="{{ route('doctors.show', $doctorProfile) }}" class="btn btn-secondary">
                <i class="fa-solid fa-address-card"></i> View Profile
            </a>
        </div>
    @endif
</div>

<!-- Doctor Stats Grid -->
<div class="stats-grid">
    <a href="#todays-appointments" class="stat-card" style="border-color: rgba(13, 148, 136, 0.4); text-decoration: none;">
        <div class="stat-icon" style="background: rgba(13, 148, 136, 0.15); color: #2dd4bf;">
            <i class="fa-solid fa-calendar-day"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value" style="color: #2dd4bf;">{{ $stats['today'] }}</div>
            <div class="stat-label">Today's Visits</div>
        </div>
    </a>

    <a href="#upcoming-appointments" class="stat-card" style="border-color: rgba(56, 189, 248, 0.4); text-decoration: none;">
        <div class="stat-icon" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8;">
            <i class="fa-solid fa-calendar-week"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value" style="color: #38bdf8;">{{ $stats['upcoming'] }}</div>
            <div class="stat-label">Upcoming Visits</div>
        </div>
    </a>

    <a href="{{ route('dashboard', ['status' => 'Pending']) }}#all-consultations" class="stat-card" style="text-decoration: none;">
        <div class="stat-icon" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24;">
            <i class="fa-solid fa-hourglass-half"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value">{{ $stats['pending'] }}</div>
            <div class="stat-label">Pending Approval</div>
        </div>
    </a>

    <a href="{{ route('dashboard', ['status' => 'Confirmed']) }}#all-consultations" class="stat-card" style="text-decoration: none;">
        <div class="stat-icon" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa;">
            <i class="fa-solid fa-calendar-check"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value" style="color: #60a5fa;">{{ $stats['confirmed'] }}</div>
            <div class="stat-label">Confirmed Visits</div>
        </div>
    </a>

    <a href="{{ route('dashboard', ['status' => 'Completed']) }}#all-consultations" class="stat-card" style="text-decoration: none;">
        <div class="stat-icon" style="background: rgba(16, 185, 129, 0.15); color: #34d399;">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value" style="color: #34d399;">{{ $stats['completed'] }}</div>
            <div class="stat-label">Completed Visits</div>
        </div>
    </a>

    <a href="{{ route('dashboard', ['status' => 'Cancelled']) }}#all-consultations" class="stat-card" style="border-color: rgba(239, 68, 68, 0.3); text-decoration: none;">
        <div class="stat-icon" style="background: rgba(239, 68, 68, 0.15); color: #fca5a5;">
            <i class="fa-solid fa-circle-xmark"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value" style="color: #fca5a5;">{{ $stats['cancelled'] }}</div>
            <div class="stat-label">Cancelled Visits</div>
        </div>
    </a>
</div>

<!-- TODAY'S APPOINTMENTS SECTION -->
<div class="card" id="todays-appointments" style="border-color: rgba(13, 148, 136, 0.3); margin-bottom: 2rem;">
    <div class="card-header" style="background: rgba(13, 148, 136, 0.05); margin: -1.5rem -1.5rem 1.5rem -1.5rem; padding: 1.25rem 1.5rem; border-bottom: 1px solid rgba(13, 148, 136, 0.2);">
        <div class="card-title" style="display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-calendar-day" style="color: #2dd4bf; font-size: 1.3rem;"></i>
            <span>Today's Appointments</span>
            <span style="font-size: 0.85rem; font-weight: 500; color: var(--text-muted); margin-left: 6px;">
                ({{ now()->format('l, F j, Y') }})
            </span>
        </div>
        <span class="badge badge-active" style="font-size: 0.85rem; padding: 0.4rem 0.85rem;">
            {{ count($todayAppointments) }} {{ Str::plural('Patient', count($todayAppointments)) }} Scheduled
        </span>
    </div>

    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Time Slot</th>
                    <th>Patient Details</th>
                    <th>Symptoms / Reason</th>
                    <th>Status</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($todayAppointments as $appointment)
                    <tr onclick="if (!event.target.closest('a, button, select, input')) window.location.href='{{ route('appointments.show', $appointment) }}';" style="cursor: pointer;">
                        <td style="white-space: nowrap;">
                            <div style="font-weight: 700; color: #38bdf8; font-size: 1rem;">
                                <i class="fa-regular fa-clock"></i> {{ $appointment->appointment_time }}
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">Today</div>
                        </td>
                        <td>
                            <a href="{{ route('appointments.show', $appointment) }}" style="text-decoration: none;" title="View Appointment Details">
                                <div style="font-weight: 700; color: var(--accent);">{{ $appointment->patient->name }}</div>
                            </a>
                            <div style="font-size: 0.8rem; color: var(--text-muted);"><i class="fa-regular fa-envelope"></i> {{ $appointment->patient->email }}</div>
                            <div style="font-size: 0.75rem; color: #2dd4bf;"><i class="fa-solid fa-phone"></i> {{ $appointment->patient->phone ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <div style="font-size: 0.85rem; max-width: 250px; color: var(--text-main);">
                                {{ $appointment->symptoms_notes ? Str::limit($appointment->symptoms_notes, 80) : 'General consultation' }}
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-{{ strtolower($appointment->status) }}">
                                {{ $appointment->status }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: flex; gap: 4px; justify-content: flex-end; align-items: center;">
                                <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-secondary btn-sm" title="View Appointment Details">
                                    <i class="fa-solid fa-eye"></i> View
                                </a>
                                <form action="{{ route('appointments.updateStatus', $appointment) }}" method="POST" style="display: flex; gap: 4px;">
                                    @csrf
                                    @method('PATCH')
                                    @if($appointment->status === 'Pending')
                                        <input type="hidden" name="status" value="Confirmed">
                                        <button type="submit" class="btn btn-primary btn-sm" title="Confirm Appointment"><i class="fa-solid fa-check"></i> Confirm</button>
                                    @elseif($appointment->status === 'Confirmed')
                                        <input type="hidden" name="status" value="Completed">
                                        <button type="submit" class="btn btn-primary btn-sm" style="background: var(--success);" title="Mark Completed"><i class="fa-solid fa-check-double"></i> Complete</button>
                                    @endif
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2.5rem 1rem;">
                            <i class="fa-solid fa-calendar-check" style="font-size: 2.5rem; color: rgba(13, 148, 136, 0.4); margin-bottom: 0.75rem; display: block;"></i>
                            <div>No appointments scheduled for today.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- UPCOMING APPOINTMENTS SECTION & SCHEDULE -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Upcoming Appointments Card -->
    <div class="card" id="upcoming-appointments" style="border-color: rgba(56, 189, 248, 0.3);">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-calendar-week" style="color: #38bdf8"></i> Upcoming Appointments
            </div>
            <span class="badge badge-confirmed">
                {{ count($upcomingAppointments) }} Upcoming
            </span>
        </div>

        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Patient Name</th>
                        <th>Symptoms / Reason</th>
                        <th>Status</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($upcomingAppointments as $appointment)
                        <tr onclick="if (!event.target.closest('a, button, select, input')) window.location.href='{{ route('appointments.show', $appointment) }}';" style="cursor: pointer;">
                            <td style="white-space: nowrap;">
                                <div style="font-weight: 700; color: var(--text-main);">
                                    <i class="fa-regular fa-calendar-days" style="color: var(--primary)"></i> {{ $appointment->appointment_date->format('M d, Y') }}
                                </div>
                                <div style="font-size: 0.8rem; color: var(--accent);"><i class="fa-regular fa-clock"></i> {{ $appointment->appointment_time }}</div>
                            </td>
                            <td>
                                <a href="{{ route('appointments.show', $appointment) }}" style="text-decoration: none;" title="View Appointment Details">
                                    <div style="font-weight: 600; color: var(--accent);">{{ $appointment->patient->name }}</div>
                                </a>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $appointment->patient->phone ?? $appointment->patient->email }}</div>
                            </td>
                            <td>
                                <div style="font-size: 0.85rem; max-width: 180px; color: var(--text-main);">
                                    {{ $appointment->symptoms_notes ? Str::limit($appointment->symptoms_notes, 50) : 'General checkup' }}
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-{{ strtolower($appointment->status) }}">
                                    {{ $appointment->status }}
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 4px; justify-content: flex-end; align-items: center;">
                                    <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-secondary btn-sm" title="View Appointment Details">
                                        <i class="fa-solid fa-eye"></i> View
                                    </a>
                                    <form action="{{ route('appointments.updateStatus', $appointment) }}" method="POST" style="display: flex; gap: 4px;">
                                        @csrf
                                        @method('PATCH')
                                        @if($appointment->status === 'Pending')
                                            <input type="hidden" name="status" value="Confirmed">
                                            <button type="submit" class="btn btn-primary btn-sm" title="Confirm Appointment"><i class="fa-solid fa-check"></i> Confirm</button>
                                        @elseif($appointment->status === 'Confirmed')
                                            <input type="hidden" name="status" value="Completed">
                                            <button type="submit" class="btn btn-primary btn-sm" style="background: var(--success);" title="Mark Completed"><i class="fa-solid fa-check-double"></i> Complete</button>
                                        @endif
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2rem;">No upcoming appointments scheduled.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Practice Schedule & Working Hours Info -->
    <div>
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fa-regular fa-clock" style="color: var(--accent)"></i> Practice Schedule</div>
                @if($doctorProfile)
                    <a href="{{ route('doctors.edit', $doctorProfile) }}" class="btn btn-secondary btn-sm" title="Edit Working Hours & Profile">
                        <i class="fa-solid fa-pen"></i> Edit Profile
                    </a>
                @endif
            </div>
            
            @if($doctorProfile)
                <div style="font-size: 0.9rem; margin-bottom: 1rem;">
                    <div style="color: var(--text-muted); font-size: 0.8rem;">WORKING HOURS</div>
                    <div style="font-weight: 700; font-size: 1.1rem; color: var(--text-main); margin-top: 2px;">
                        {{ \Carbon\Carbon::parse($doctorProfile->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($doctorProfile->end_time)->format('h:i A') }}
                    </div>
                </div>

                <div style="font-size: 0.9rem; margin-bottom: 1rem;">
                    <div style="color: var(--text-muted); font-size: 0.8rem;">SLOT DURATION</div>
                    <div style="font-weight: 700; font-size: 1rem; color: var(--text-main); margin-top: 2px;">
                        {{ $doctorProfile->slot_duration_minutes }} Minutes per consultation
                    </div>
                </div>

                <div style="font-size: 0.85rem; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-muted);">ACTIVE DAYS</div>
                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                    @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                        @php $isAvailable = in_array($day, $doctorProfile->available_days ?? []); @endphp
                        <span style="padding: 0.35rem 0.65rem; border-radius: 6px; font-size: 0.75rem; font-weight: 600; {{ $isAvailable ? 'background: rgba(13, 148, 136, 0.2); color: #2dd4bf; border: 1px solid rgba(13, 148, 136, 0.4);' : 'background: rgba(255,255,255,0.03); color: var(--text-muted); opacity: 0.4;' }}">
                            {{ substr($day, 0, 3) }}
                        </span>
                    @endforeach
                </div>
            @else
                <p style="color: var(--text-muted); font-size: 0.9rem;">No schedule assigned yet.</p>
            @endif
        </div>
    </div>
</div>

<!-- ALL PATIENT CONSULTATIONS TABLE WITH STATUS FILTER -->
<div class="card" id="all-consultations">
    <div class="card-header" style="flex-wrap: wrap; gap: 10px;">
        <div class="card-title">
            <i class="fa-solid fa-list-check" style="color: var(--primary)"></i> All Patient Consultations History
        </div>

        <!-- Search & Filter Controls -->
        <form action="{{ route('dashboard') }}#all-consultations" method="GET" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
            <div style="position: relative;">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search patient name, email, phone..." value="{{ request('search') }}" style="max-width: 240px; padding-left: 2rem;">
                <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.85rem;"></i>
            </div>

            <select name="status" class="form-select form-select-sm" style="max-width: 150px;" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Confirmed" {{ request('status') == 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>

            <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-magnifying-glass"></i> Search</button>

            @if(request('status') || request('search'))
                <a href="{{ route('dashboard') }}#all-consultations" class="btn btn-secondary btn-sm" title="Reset Filter">
                    <i class="fa-solid fa-rotate-left"></i> Reset
                </a>
            @endif
        </form>
    </div>

    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Patient Name</th>
                    <th>Date & Time</th>
                    <th>Symptoms / Reason</th>
                    <th>Status</th>
                    <th style="text-align: right;">Update Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentAppointments as $appointment)
                    <tr onclick="if (!event.target.closest('a, button, select, input')) window.location.href='{{ route('appointments.show', $appointment) }}';" style="cursor: pointer;">
                        <td>
                            <a href="{{ route('appointments.show', $appointment) }}" style="text-decoration: none;" title="View Appointment Details">
                                <div style="font-weight: 700; color: var(--accent);">{{ $appointment->patient->name }}</div>
                            </a>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $appointment->patient->email }}</div>
                            <div style="font-size: 0.75rem; color: #2dd4bf;">{{ $appointment->patient->phone ?? 'No phone' }}</div>
                        </td>
                        <td>
                            <div><i class="fa-regular fa-calendar-days" style="color: var(--primary)"></i> {{ $appointment->appointment_date->format('M d, Y') }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);"><i class="fa-regular fa-clock" style="color: var(--accent)"></i> {{ $appointment->appointment_time }}</div>
                        </td>
                        <td>
                            <div style="font-size: 0.85rem; max-width: 200px; color: var(--text-main);">
                                {{ $appointment->symptoms_notes ? Str::limit($appointment->symptoms_notes, 60) : 'N/A' }}
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-{{ strtolower($appointment->status) }}">
                                {{ $appointment->status }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: flex; gap: 4px; justify-content: flex-end; align-items: center;">
                                <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-secondary btn-sm" title="View Appointment Details">
                                    <i class="fa-solid fa-eye"></i> View
                                </a>
                                <form action="{{ route('appointments.updateStatus', $appointment) }}" method="POST" style="display: flex; gap: 4px;">
                                    @csrf
                                    @method('PATCH')
                                    @if($appointment->status === 'Pending')
                                        <input type="hidden" name="status" value="Confirmed">
                                        <button type="submit" class="btn btn-primary btn-sm" title="Confirm Appointment"><i class="fa-solid fa-check"></i> Confirm</button>
                                    @elseif($appointment->status === 'Confirmed')
                                        <input type="hidden" name="status" value="Completed">
                                        <button type="submit" class="btn btn-primary btn-sm" style="background: var(--success);" title="Mark Completed"><i class="fa-solid fa-check-double"></i> Complete</button>
                                    @endif
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2rem;">No patient appointments found for the selected status.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
