@extends('layouts.app')

@section('title', 'Patient Details - ' . $patient->name)
@section('page-header', 'Patient Overview & Booking History')

@section('content')
<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
    <!-- Left Column: Patient Profile Card & Stats -->
    <div>
        <div class="card" style="text-align: center; margin-bottom: 1.5rem;">
            <div style="width: 84px; height: 84px; background: linear-gradient(135deg, #6366f1, #a855f7); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; font-weight: 700; color: #fff; margin: 0 auto 1rem; box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);">
                {{ strtoupper(substr($patient->name, 0, 1)) }}
            </div>
            
            <h2 style="font-family: var(--font-title); font-size: 1.4rem;">{{ $patient->name }}</h2>
            <div style="color: var(--accent); font-weight: 600; font-size: 0.9rem; margin-top: 4px;">
                <i class="fa-solid fa-user-circle"></i> Registered Patient Profile
            </div>

            <div style="margin-top: 10px;">
                @if($patient->status === 'inactive')
                    <span class="badge badge-inactive"><i class="fa-solid fa-user-xmark"></i> Inactive Account</span>
                @else
                    <span class="badge badge-active"><i class="fa-solid fa-user-check"></i> Active Account</span>
                @endif
            </div>

            <div style="text-align: left; background: rgba(15,23,42,0.5); padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); font-size: 0.9rem; margin: 1.25rem 0;">
                <div style="margin-bottom: 8px;"><i class="fa-regular fa-envelope" style="width: 20px; color: var(--accent);"></i> {{ $patient->email }}</div>
                <div style="margin-bottom: 8px;"><i class="fa-solid fa-phone" style="width: 20px; color: #2dd4bf;"></i> {{ $patient->phone ?? 'No phone added' }}</div>
                <div><i class="fa-regular fa-calendar-check" style="width: 20px; color: #fbbf24;"></i> Member since {{ $patient->created_at ? $patient->created_at->format('M Y') : '2026' }}</div>
            </div>

            @if(auth()->user()->isAdmin() || auth()->id() === $patient->id)
                <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 8px;">
                    <a href="{{ route('patients.edit', $patient) }}" class="btn btn-secondary" style="width: 100%; background: rgba(56, 189, 248, 0.15); border: 1px solid rgba(56, 189, 248, 0.3); color: #38bdf8;">
                        <i class="fa-solid fa-user-pen"></i> Edit Patient Profile
                    </a>
                </div>
            @endif

            @if(auth()->user()->isAdmin())
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    <form action="{{ route('patients.toggleStatus', $patient) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        @if($patient->status === 'inactive')
                            <button type="submit" class="btn" style="width: 100%; background: rgba(34, 197, 94, 0.2); border: 1px solid rgba(34, 197, 94, 0.4); color: #4ade80;" onclick="return confirm('Activate {{ $patient->name }}\'s account?');">
                                <i class="fa-solid fa-user-check"></i> Activate Account
                            </button>
                        @else
                            <button type="submit" class="btn" style="width: 100%; background: rgba(245, 158, 11, 0.2); border: 1px solid rgba(245, 158, 11, 0.4); color: #fbbf24;" onclick="return confirm('Deactivate {{ $patient->name }}\'s account? They will not be able to log in.');">
                                <i class="fa-solid fa-user-xmark"></i> Deactivate Account
                            </button>
                        @endif
                    </form>

                    <form action="{{ route('patients.destroy', $patient) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete patient {{ $patient->name }}? All appointment records will be permanently removed.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="width: 100%;">
                            <i class="fa-solid fa-trash"></i> Delete Patient Account
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Activity Stats Card -->
        <div class="card">
            <div class="card-header" style="margin-bottom: 1rem;">
                <div class="card-title" style="font-size: 1.05rem;"><i class="fa-solid fa-chart-simple" style="color: var(--primary);"></i> Booking Activity Summary</div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 10px;">
                <a href="{{ route('patients.show', $patient) }}#patient-history" style="text-decoration: none; color: inherit;">
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0.75rem; border-bottom: 1px solid rgba(255,255,255,0.05); border-radius: var(--radius-sm); cursor: pointer; {{ !request('status') ? 'background: rgba(255,255,255,0.05);' : '' }}">
                        <span style="color: var(--text-muted); font-size: 0.9rem;">Total Appointments</span>
                        <strong style="color: var(--text-main);">{{ $stats['total'] }}</strong>
                    </div>
                </a>

                <a href="{{ route('patients.show', [$patient, 'status' => 'Pending']) }}#patient-history" style="text-decoration: none; color: inherit;">
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0.75rem; border-bottom: 1px solid rgba(255,255,255,0.05); border-radius: var(--radius-sm); cursor: pointer; {{ request('status') === 'Pending' ? 'background: rgba(245, 158, 11, 0.15);' : '' }}">
                        <span style="color: var(--text-muted); font-size: 0.9rem;">Pending Bookings</span>
                        <span class="badge badge-pending">{{ $stats['pending'] }}</span>
                    </div>
                </a>

                <a href="{{ route('patients.show', [$patient, 'status' => 'Confirmed']) }}#patient-history" style="text-decoration: none; color: inherit;">
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0.75rem; border-bottom: 1px solid rgba(255,255,255,0.05); border-radius: var(--radius-sm); cursor: pointer; {{ request('status') === 'Confirmed' ? 'background: rgba(59, 130, 246, 0.15);' : '' }}">
                        <span style="color: var(--text-muted); font-size: 0.9rem;">Confirmed Visits</span>
                        <span class="badge badge-confirmed">{{ $stats['confirmed'] }}</span>
                    </div>
                </a>

                <a href="{{ route('patients.show', [$patient, 'status' => 'Completed']) }}#patient-history" style="text-decoration: none; color: inherit;">
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0.75rem; border-bottom: 1px solid rgba(255,255,255,0.05); border-radius: var(--radius-sm); cursor: pointer; {{ request('status') === 'Completed' ? 'background: rgba(16, 185, 129, 0.15);' : '' }}">
                        <span style="color: var(--text-muted); font-size: 0.9rem;">Completed Visits</span>
                        <span class="badge badge-completed">{{ $stats['completed'] }}</span>
                    </div>
                </a>

                <a href="{{ route('patients.show', [$patient, 'status' => 'Cancelled']) }}#patient-history" style="text-decoration: none; color: inherit;">
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0.75rem; border-radius: var(--radius-sm); cursor: pointer; {{ request('status') === 'Cancelled' ? 'background: rgba(239, 68, 68, 0.15);' : '' }}">
                        <span style="color: var(--text-muted); font-size: 0.9rem;">Cancelled Visits</span>
                        <span class="badge badge-cancelled">{{ $stats['cancelled'] }}</span>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Right Column: Patient Appointments History Table -->
    <div>
        <div class="card" id="patient-history">
            <div class="card-header" style="flex-wrap: wrap; gap: 10px;">
                <div class="card-title"><i class="fa-solid fa-calendar-days" style="color: var(--primary);"></i> Appointment History</div>
                
                <form action="{{ route('patients.show', $patient) }}#patient-history" method="GET" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                    <div style="position: relative;">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search doctor, symptoms..." value="{{ request('search') }}" style="max-width: 170px; padding-left: 1.8rem;">
                        <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 8px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 0.8rem;"></i>
                    </div>

                    <select name="status" class="form-select form-select-sm" style="max-width: 140px;" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Confirmed" {{ request('status') == 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                        <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>

                    <button type="submit" class="btn btn-secondary btn-sm"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>

                    @if(request('status') || request('search'))
                        <a href="{{ route('patients.show', $patient) }}#patient-history" class="btn btn-danger btn-sm" style="background: rgba(239, 68, 68, 0.2); border-color: rgba(239, 68, 68, 0.3); color: #fca5a5;" title="Reset filter">
                            <i class="fa-solid fa-rotate-left"></i> Reset
                        </a>
                    @endif

                    @if(!auth()->user()->isPatient())
                        <a href="{{ route('patients.index') }}" class="btn btn-secondary btn-sm"><i class="fa-solid fa-arrow-left"></i> Patients List</a>
                    @endif
                </form>
            </div>

            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Doctor</th>
                            <th>Date & Time</th>
                            <th>Symptoms / Reason</th>
                            <th>Status</th>
                            <th style="text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($appointments as $apt)
                            <tr onclick="if (!event.target.closest('a, button, select, input')) window.location.href='{{ route('appointments.show', $apt) }}';" style="cursor: pointer;">
                                <td>
                                    <div style="font-weight: 700; color: var(--accent);">{{ $apt->doctor->name }}</div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted);">{{ $apt->doctor->specialization }}</div>
                                </td>
                                <td>
                                    <div><i class="fa-regular fa-calendar-days" style="color: var(--primary);"></i> {{ $apt->appointment_date->format('M d, Y') }}</div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted);"><i class="fa-regular fa-clock" style="color: var(--accent);"></i> {{ $apt->appointment_time }}</div>
                                </td>
                                <td>
                                    <div style="font-size: 0.85rem; max-width: 200px; color: var(--text-main);">
                                        {{ $apt->symptoms_notes ? Str::limit($apt->symptoms_notes, 60) : 'N/A' }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-{{ strtolower($apt->status) }}">
                                        {{ $apt->status }}
                                    </span>
                                </td>
                                <td style="text-align: right;">
                                    <a href="{{ route('appointments.show', $apt) }}" class="btn btn-secondary btn-sm" title="View Appointment Details">
                                        <i class="fa-solid fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                    This patient has no appointment records.
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
    </div>
</div>
@endsection
