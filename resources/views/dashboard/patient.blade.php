@extends('layouts.app')

@section('title', 'Patient Dashboard - MediCare')
@section('page-header', 'Welcome, ' . auth()->user()->name)

@section('content')
<!-- Hero banner / Next Appointment card -->
@if($upcomingAppointment)
    <a href="{{ route('appointments.show', $upcomingAppointment) }}" style="text-decoration: none; color: inherit; display: block;">
        <div style="background: linear-gradient(135deg, rgba(13, 148, 136, 0.25), rgba(56, 189, 248, 0.15)); border: 1px solid var(--primary); border-radius: var(--radius-lg); padding: 1.75rem; margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; cursor: pointer; transition: transform 0.2s ease, border-color 0.2s ease;" onmouseenter="this.style.borderColor='var(--accent)';" onmouseleave="this.style.borderColor='var(--primary)';">
            <div>
                <div style="font-size: 0.85rem; font-weight: 700; text-transform: uppercase; color: var(--accent); letter-spacing: 1px;">
                    <i class="fa-solid fa-clock-rotate-left"></i> Next Upcoming Appointment
                </div>
                <div style="font-family: var(--font-title); font-size: 1.6rem; font-weight: 700; margin: 6px 0;">
                    {{ $upcomingAppointment->doctor->name }} <span style="font-size: 1rem; color: var(--text-muted); font-weight: 400;">({{ $upcomingAppointment->doctor->specialization }})</span>
                </div>
                <div style="font-size: 0.95rem; color: var(--text-main); display: flex; gap: 1.5rem; flex-wrap: wrap;">
                    <span><i class="fa-regular fa-calendar-days" style="color: var(--primary)"></i> {{ $upcomingAppointment->appointment_date->format('l, F j, Y') }}</span>
                    <span><i class="fa-regular fa-clock" style="color: var(--accent)"></i> {{ $upcomingAppointment->appointment_time }}</span>
                    <span><i class="fa-solid fa-door-open" style="color: #fbbf24"></i> {{ $upcomingAppointment->doctor->room_number ?? 'Consultation Room' }}</span>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <span class="badge badge-{{ strtolower($upcomingAppointment->status) }}" style="font-size: 0.9rem; padding: 0.5rem 1rem;">
                    Status: {{ $upcomingAppointment->status }}
                </span>
                <span class="btn btn-secondary btn-sm" style="background: rgba(255,255,255,0.1);"><i class="fa-solid fa-eye"></i> View Details</span>
            </div>
        </div>
    </a>
@endif

<!-- Metric Stats Grid (Clickable to Filter Appointments) -->
<div class="stats-grid">
    <a href="{{ route('dashboard') }}#my-appointments" style="text-decoration: none; color: inherit;">
        <div class="stat-card" style="cursor: pointer; {{ !request('status') ? 'border-color: var(--primary);' : '' }}">
            <div class="stat-icon" style="background: rgba(13, 148, 136, 0.15); color: #2dd4bf;">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['total'] }}</div>
                <div class="stat-label">Total Appointments</div>
            </div>
        </div>
    </a>

    <a href="{{ route('dashboard', ['status' => 'Pending']) }}#my-appointments" style="text-decoration: none; color: inherit;">
        <div class="stat-card" style="cursor: pointer; {{ request('status') === 'Pending' ? 'border-color: #fbbf24; background: rgba(245, 158, 11, 0.1);' : '' }}">
            <div class="stat-icon" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24;">
                <i class="fa-solid fa-hourglass-half"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value" style="color: #fbbf24;">{{ $stats['pending'] }}</div>
                <div class="stat-label">Pending Approval</div>
            </div>
        </div>
    </a>

    <a href="{{ route('dashboard', ['status' => 'Confirmed']) }}#my-appointments" style="text-decoration: none; color: inherit;">
        <div class="stat-card" style="cursor: pointer; {{ request('status') === 'Confirmed' ? 'border-color: #60a5fa; background: rgba(59, 130, 246, 0.1);' : '' }}">
            <div class="stat-icon" style="background: rgba(59, 130, 246, 0.15); color: #60a5fa;">
                <i class="fa-solid fa-calendar-day"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value" style="color: #60a5fa;">{{ $stats['confirmed'] }}</div>
                <div class="stat-label">Confirmed</div>
            </div>
        </div>
    </a>

    <a href="{{ route('dashboard', ['status' => 'Completed']) }}#my-appointments" style="text-decoration: none; color: inherit;">
        <div class="stat-card" style="cursor: pointer; {{ request('status') === 'Completed' ? 'border-color: #34d399; background: rgba(16, 185, 129, 0.1);' : '' }}">
            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.15); color: #34d399;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value" style="color: #34d399;">{{ $stats['completed'] }}</div>
                <div class="stat-label">Completed</div>
            </div>
        </div>
    </a>

    <a href="{{ route('dashboard', ['status' => 'Cancelled']) }}#my-appointments" style="text-decoration: none; color: inherit;">
        <div class="stat-card" style="cursor: pointer; {{ request('status') === 'Cancelled' ? 'border-color: #fca5a5; background: rgba(239, 68, 68, 0.1);' : '' }}">
            <div class="stat-icon" style="background: rgba(239, 68, 68, 0.15); color: #fca5a5;">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value" style="color: #fca5a5;">{{ $stats['cancelled'] }}</div>
                <div class="stat-label">Cancelled Visits</div>
            </div>
        </div>
    </a>
</div>

<!-- My Recent Appointments Section (Including Cancelled Appointments & Filter + Reset) -->
<div class="card" id="my-appointments" style="margin-bottom: 2rem;">
    <div class="card-header" style="flex-wrap: wrap; gap: 10px;">
        <div class="card-title">
            <i class="fa-solid fa-list-check" style="color: var(--primary)"></i> My Appointments Summary
        </div>

        <!-- Filter + Reset Form -->
        <form action="{{ route('dashboard') }}#my-appointments" method="GET" style="display: flex; gap: 8px; align-items: center;">
            <select name="status" class="form-select form-select-sm" style="max-width: 170px;" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Confirmed" {{ request('status') == 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>

            @if(request('status'))
                <a href="{{ route('dashboard') }}#my-appointments" class="btn btn-secondary btn-sm" title="Reset Status Filter">
                    <i class="fa-solid fa-rotate-left"></i> Reset
                </a>
            @endif

            <a href="{{ route('appointments.history') }}" class="btn btn-secondary btn-sm">Full History</a>
        </form>
    </div>

    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Doctor & Specialization</th>
                    <th>Date & Time</th>
                    <th>Symptoms / Reason</th>
                    <th>Status</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentAppointments as $appointment)
                    <tr onclick="if (!event.target.closest('a, button, select, input')) window.location.href='{{ route('appointments.show', $appointment) }}';" style="cursor: pointer;">
                        <td>
                            <a href="{{ route('doctors.show', $appointment->doctor) }}" style="text-decoration: none; color: inherit;">
                                <div style="font-weight: 700; color: var(--accent);">{{ $appointment->doctor->name }}</div>
                            </a>
                            <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $appointment->doctor->specialization }}</div>
                        </td>
                        <td>
                            <div><i class="fa-regular fa-calendar-days" style="color: var(--primary);"></i> {{ $appointment->appointment_date->format('M d, Y') }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);"><i class="fa-regular fa-clock" style="color: var(--accent);"></i> {{ $appointment->appointment_time }}</div>
                        </td>
                        <td>
                            <div style="font-size: 0.85rem; max-width: 250px; color: var(--text-main);">
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
                                <a href="{{ route('appointments.show', $appointment) }}" class="btn btn-secondary btn-sm" title="View Important Details">
                                    <i class="fa-solid fa-eye"></i> View
                                </a>

                                @if(in_array($appointment->status, ['Pending', 'Confirmed']))
                                    <form action="{{ route('appointments.updateStatus', $appointment) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking?');" style="display: inline;">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="Cancelled">
                                        <button type="submit" class="btn btn-danger btn-sm" title="Cancel appointment">
                                            <i class="fa-solid fa-xmark"></i> Cancel
                                        </button>
                                    </form>
                                @elseif(in_array($appointment->status, ['Cancelled', 'Completed']))
                                    <a href="{{ route('appointments.create', ['doctor_id' => $appointment->doctor_id]) }}" class="btn btn-primary btn-sm" title="Book new appointment with this doctor">
                                        <i class="fa-solid fa-calendar-plus"></i> Re-Book
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                            No appointments found for the selected status.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Book Action Header -->
<div id="available-specialists" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 15px;">
    <div>
        <h2 style="font-family: var(--font-title); font-size: 1.3rem;">Available Specialists</h2>
        <p style="font-size: 0.875rem; color: var(--text-muted)">Choose a doctor and schedule your consultation</p>
    </div>

    <!-- Doctor Search & Filter Form -->
    <form action="{{ route('dashboard') }}#available-specialists" method="GET" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif

        <div style="position: relative; min-width: 220px;">
            <input type="text" name="doctor_search" id="doctorSearchInput" class="form-control form-control-sm" placeholder="Search doctor or specialty..." value="{{ request('doctor_search') }}" style="padding-left: 32px;">
            <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.85rem;"></i>
        </div>

        <select name="specialization" class="form-select form-select-sm" style="max-width: 170px;" onchange="this.form.submit()">
            <option value="">All Specializations</option>
            @foreach($specializations as $spec)
                <option value="{{ $spec }}" {{ request('specialization') == $spec ? 'selected' : '' }}>{{ $spec }}</option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-secondary btn-sm"><i class="fa-solid fa-magnifying-glass"></i> Search</button>

        @if(request('doctor_search') || request('specialization'))
            <a href="{{ route('dashboard') }}#available-specialists" class="btn btn-danger btn-sm" style="background: rgba(239, 68, 68, 0.2); border-color: rgba(239, 68, 68, 0.3); color: #fca5a5;" title="Clear doctor search">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </a>
        @endif

        <a href="{{ route('appointments.create') }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus-circle"></i> Book New Appointment
        </a>
    </form>
</div>

<!-- Doctor Grid -->
<div id="doctorGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.25rem;">
    @forelse($availableDoctors as $doctor)
        <div class="card doctor-card" 
             data-name="{{ strtolower($doctor->name) }}" 
             data-spec="{{ strtolower($doctor->specialization) }}" 
             onclick="if (!event.target.closest('a, button')) window.location.href='{{ route('doctors.show', $doctor) }}';"
             onmouseenter="this.style.borderColor='var(--primary)';" 
             onmouseleave="this.style.borderColor='var(--border-color)';"
             style="margin-bottom: 0; display: flex; flex-direction: column; justify-content: space-between; cursor: pointer; transition: all 0.2s ease;">
            <div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                    <div>
                        <a href="{{ route('doctors.show', $doctor) }}" style="text-decoration: none;">
                            <div style="font-family: var(--font-title); font-size: 1.15rem; font-weight: 700; color: var(--text-main);">{{ $doctor->name }}</div>
                        </a>
                        <div style="color: var(--accent); font-size: 0.85rem; font-weight: 600;">{{ $doctor->specialization }}</div>
                    </div>
                    <span class="badge badge-active">${{ number_format($doctor->consultation_fee, 2) }}</span>
                </div>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1rem; line-height: 1.4;">
                    {{ Str::limit($doctor->bio, 90) }}
                </p>
                <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1.25rem;">
                    <div><i class="fa-solid fa-building" style="width: 18px;"></i> {{ $doctor->room_number ?? 'Consultation Room' }}</div>
                    <div><i class="fa-regular fa-clock" style="width: 18px;"></i> {{ $doctor->start_time }} - {{ $doctor->end_time }}</div>
                </div>
            </div>

            <div style="display: flex; gap: 8px;">
                <a href="{{ route('doctors.show', $doctor) }}" class="btn btn-secondary" style="flex: 1; padding: 0.5rem 0.75rem; font-size: 0.85rem;">
                    <i class="fa-solid fa-eye"></i> View Profile
                </a>
                <a href="{{ route('appointments.create', ['doctor_id' => $doctor->id]) }}" class="btn btn-primary" style="flex: 1; padding: 0.5rem 0.75rem; font-size: 0.85rem;">
                    <i class="fa-solid fa-calendar-check"></i> Book Now
                </a>
            </div>
        </div>
    @empty
        <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; background: var(--bg-card); border-radius: var(--radius-lg); border: 1px dashed var(--border-color);">
            <i class="fa-solid fa-user-doctor" style="font-size: 2.5rem; color: var(--text-muted); margin-bottom: 1rem; display: block;"></i>
            <h4 style="font-family: var(--font-title); color: var(--text-main); margin-bottom: 0.5rem;">No specialists found</h4>
            <p style="color: var(--text-muted); font-size: 0.9rem;">No active doctors matched your search query or selected specialization filter.</p>
        </div>
    @endforelse
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('doctorSearchInput');
    const cards = document.querySelectorAll('.doctor-card');
    
    if (searchInput && cards.length > 0) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            cards.forEach(card => {
                const name = card.getAttribute('data-name') || '';
                const spec = card.getAttribute('data-spec') || '';
                if (name.includes(query) || spec.includes(query)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
});
</script>
@endpush
@endsection
