@extends('layouts.app')

@section('title', 'Appointment History - MediCare')
@section('page-header', auth()->user()->isAdmin() ? 'All Appointments Management' : 'My Appointment History')

@section('content')
<!-- Filter & Search bar -->
<div class="card">
    <form action="{{ route('appointments.history') }}" method="GET" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
        <div style="position: relative; min-width: 220px; flex: 1;">
            <input type="text" name="search" class="form-control" placeholder="Search patient name, email, phone, doctor..." value="{{ request('search') }}" style="padding-left: 2.2rem;">
            <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
        </div>

        <select name="status" class="form-select" style="max-width: 170px;" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
            <option value="Confirmed" {{ request('status') == 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
            <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
            <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>

        @if(auth()->user()->isAdmin())
            <select name="doctor_id" class="form-select" style="max-width: 200px;" onchange="this.form.submit()">
                <option value="">All Doctors</option>
                @foreach($doctors as $doc)
                    <option value="{{ $doc->id }}" {{ request('doctor_id') == $doc->id ? 'selected' : '' }}>{{ $doc->name }}</option>
                @endforeach
            </select>
        @endif

        <input type="date" name="date" class="form-control" style="max-width: 170px;" value="{{ request('date') }}" onchange="this.form.submit()">

        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i> Search</button>

        @if(request('status') || request('doctor_id') || request('date') || request('search'))
            <a href="{{ route('appointments.history') }}" class="btn btn-secondary" title="Reset all filters">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </a>
        @endif

        @if(auth()->user()->isPatient())
            <a href="{{ route('appointments.create') }}" class="btn btn-primary" style="margin-left: auto;">
                <i class="fa-solid fa-plus"></i> New Appointment
            </a>
        @endif
    </form>
</div>

<!-- Appointments Table -->
<div class="card">
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th># ID</th>
                    @if(auth()->user()->isAdmin() || auth()->user()->isDoctor())
                        <th>Patient Details</th>
                    @endif
                    @if(auth()->user()->isAdmin() || auth()->user()->isPatient())
                        <th>Doctor</th>
                    @endif
                    <th>Date & Time</th>
                    <th>Symptoms / Notes</th>
                    <th>Status</th>
                    <th style="text-align: right;">Action / Update</th>
                </tr>
            </thead>
            <tbody>
                @forelse($appointments as $apt)
                    <tr onclick="if (!event.target.closest('a, button, select, input')) window.location.href='{{ route('appointments.show', $apt) }}';" style="cursor: pointer;">
                        <td style="font-weight: 700; color: var(--text-muted);">
                            <a href="{{ route('appointments.show', $apt) }}" style="text-decoration: none; color: inherit;">
                                #APT-{{ str_pad($apt->id, 4, '0', STR_PAD_LEFT) }}
                            </a>
                        </td>
                        
                        @if(auth()->user()->isAdmin() || auth()->user()->isDoctor())
                            <td>
                                <a href="{{ route('appointments.show', $apt) }}" style="text-decoration: none;" title="View Appointment Details">
                                    <div style="font-weight: 700; color: var(--accent);">{{ $apt->patient->name }}</div>
                                </a>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $apt->patient->email }}</div>
                                <div style="font-size: 0.75rem; color: #2dd4bf;">{{ $apt->patient->phone ?? 'No phone' }}</div>
                            </td>
                        @endif

                        @if(auth()->user()->isAdmin() || auth()->user()->isPatient())
                            <td>
                                <a href="{{ route('doctors.show', $apt->doctor) }}" style="text-decoration: none; color: inherit;">
                                    <div style="font-weight: 700; color: var(--accent);">{{ $apt->doctor->name }}</div>
                                </a>
                                <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $apt->doctor->specialization }}</div>
                                <div style="font-size: 0.75rem; color: #34d399;">${{ number_format($apt->doctor->consultation_fee, 2) }}</div>
                            </td>
                        @endif

                        <td>
                            <div style="font-weight: 600;"><i class="fa-regular fa-calendar-days" style="color: var(--primary);"></i> {{ $apt->appointment_date->format('M d, Y') }}</div>
                            <div style="font-size: 0.85rem; color: var(--text-muted);"><i class="fa-regular fa-clock" style="color: var(--accent);"></i> {{ $apt->appointment_time }}</div>
                        </td>

                        <td>
                            <div style="font-size: 0.85rem; max-width: 250px; color: var(--text-main);">
                                {{ $apt->symptoms_notes ? Str::limit($apt->symptoms_notes, 80) : 'N/A' }}
                            </div>
                            @if($apt->notes)
                                <div style="font-size: 0.75rem; color: var(--accent); margin-top: 4px;">
                                    <i class="fa-solid fa-note-sticky"></i> Doctor/Admin Note: {{ $apt->notes }}
                                </div>
                            @endif
                        </td>

                        <td>
                            <span class="badge badge-{{ strtolower($apt->status) }}">
                                {{ $apt->status }}
                            </span>
                        </td>

                        <td style="text-align: right;">
                            <div style="display: flex; justify-content: flex-end; gap: 6px; align-items: center;">
                                <a href="{{ route('appointments.show', $apt) }}" class="btn btn-secondary btn-sm" title="View Important Details">
                                    <i class="fa-solid fa-eye"></i> View
                                </a>

                                @if(auth()->user()->isAdmin() || auth()->user()->isDoctor())
                                    <!-- Admin / Doctor Status Selector (including Re-activation) -->
                                    <form action="{{ route('appointments.updateStatus', $apt) }}" method="POST" style="display: flex; gap: 4px;">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" class="form-select form-select-sm" style="font-size: 0.8rem; padding: 0.3rem 0.5rem; width: 120px;" onchange="this.form.submit()">
                                            <option value="Pending" {{ $apt->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="Confirmed" {{ $apt->status === 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                                            <option value="Completed" {{ $apt->status === 'Completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="Cancelled" {{ $apt->status === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </form>
                                @else
                                    <!-- Patient Actions -->
                                    @if(in_array($apt->status, ['Pending', 'Confirmed']))
                                        <form action="{{ route('appointments.updateStatus', $apt) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this appointment?');" style="display: inline;">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="Cancelled">
                                            <button type="submit" class="btn btn-danger btn-sm" title="Cancel this appointment">
                                                <i class="fa-solid fa-xmark"></i> Cancel
                                            </button>
                                        </form>
                                    @elseif(in_array($apt->status, ['Cancelled', 'Completed']))
                                        <a href="{{ route('appointments.create', ['doctor_id' => $apt->doctor_id]) }}" class="btn btn-primary btn-sm" title="Book new appointment with this doctor">
                                            <i class="fa-solid fa-calendar-plus"></i> Re-Book
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->isAdmin() ? 7 : 6 }}" style="text-align: center; color: var(--text-muted); padding: 2.5rem;">
                            <i class="fa-regular fa-folder-open" style="font-size: 2rem; margin-bottom: 0.5rem; display: block;"></i>
                            No appointment records found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $appointments->withQueryString()->links() }}
    </div>
</div>
@endsection
