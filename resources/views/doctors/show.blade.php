@extends('layouts.app')

@section('title', $doctor->name . ' - Doctor Profile')
@section('page-header', 'Doctor Profile')

@section('content')
<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
    <!-- Left Column: Doctor Card -->
    <div class="card" style="text-align: center;">
        <div style="width: 90px; height: 90px; background: linear-gradient(135deg, var(--primary), var(--accent)); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: #fff; margin: 0 auto 1rem; box-shadow: 0 8px 25px rgba(13, 148, 136, 0.4);">
            <i class="fa-solid fa-user-doctor"></i>
        </div>
        
        <h2 style="font-family: var(--font-title); font-size: 1.4rem;">{{ $doctor->name }}</h2>
        <div style="color: var(--accent); font-weight: 600; font-size: 0.95rem; margin-bottom: 0.5rem;">{{ $doctor->specialization }}</div>
        
        <div style="margin: 1rem 0;">
            <span class="badge badge-{{ $doctor->status }}">{{ ucfirst($doctor->status) }}</span>
        </div>

        <div style="text-align: left; background: rgba(15,23,42,0.5); padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); font-size: 0.9rem; margin-bottom: 1.5rem;">
            <div style="margin-bottom: 8px;"><i class="fa-solid fa-envelope" style="width: 20px; color: var(--accent);"></i> {{ $doctor->email }}</div>
            <div style="margin-bottom: 8px;"><i class="fa-solid fa-phone" style="width: 20px; color: var(--accent);"></i> {{ $doctor->phone }}</div>
            <div style="margin-bottom: 8px;"><i class="fa-solid fa-building" style="width: 20px; color: var(--accent);"></i> {{ $doctor->room_number ?? 'Consultation Room' }}</div>
            <div><i class="fa-solid fa-file-invoice-dollar" style="width: 20px; color: #34d399;"></i> <strong>Fee:</strong> ${{ number_format($doctor->consultation_fee, 2) }}</div>
        </div>

        @if(auth()->user()->isPatient())
            <a href="{{ route('appointments.create', ['doctor_id' => $doctor->id]) }}" class="btn btn-primary" style="width: 100%;">
                <i class="fa-solid fa-calendar-check"></i> Book Appointment
            </a>
        @endif

        @if(auth()->user()->isAdmin() || (auth()->user()->isDoctor() && ($doctor->user_id === auth()->id() || $doctor->email === auth()->user()->email)))
            <div style="margin-top: 12px; display: flex; flex-direction: column; gap: 8px;">
                <a href="{{ route('doctors.edit', $doctor) }}" class="btn btn-primary" style="width: 100%;">
                    <i class="fa-solid fa-pen-to-square"></i> Edit Profile & Schedule
                </a>
                <a href="#security-settings" class="btn btn-secondary" style="width: 100%;">
                    <i class="fa-solid fa-key"></i> Change Password
                </a>
            </div>
        @endif
    </div>

    <!-- Right Column: Biography, Schedule & Change Password -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fa-solid fa-address-card" style="color: var(--primary)"></i> Biography</div>
            </div>
            <p style="color: var(--text-muted); line-height: 1.6; font-size: 0.95rem;">
                {{ $doctor->bio ?? 'No biography provided.' }}
            </p>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fa-regular fa-clock" style="color: var(--accent)"></i> Practice Hours & Working Days</div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                <div style="background: rgba(255,255,255,0.03); padding: 1rem; border-radius: var(--radius-md);">
                    <div style="font-size: 0.8rem; color: var(--text-muted);">WORKING HOURS</div>
                    <div style="font-weight: 700; font-size: 1.1rem; color: var(--text-main); margin-top: 4px;">
                        {{ \Carbon\Carbon::parse($doctor->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($doctor->end_time)->format('h:i A') }}
                    </div>
                </div>

                <div style="background: rgba(255,255,255,0.03); padding: 1rem; border-radius: var(--radius-md);">
                    <div style="font-size: 0.8rem; color: var(--text-muted);">SLOT DURATION</div>
                    <div style="font-weight: 700; font-size: 1.1rem; color: var(--text-main); margin-top: 4px;">
                        {{ $doctor->slot_duration_minutes }} Minutes / session
                    </div>
                </div>
            </div>

            <div style="font-size: 0.9rem; font-weight: 600; margin-bottom: 0.5rem;">Available Days:</div>
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                    @php $isAvailable = in_array($day, $doctor->available_days ?? []); @endphp
                    <span style="padding: 0.4rem 0.8rem; border-radius: 8px; font-size: 0.85rem; font-weight: 600; {{ $isAvailable ? 'background: rgba(13, 148, 136, 0.2); color: #2dd4bf; border: 1px solid rgba(13, 148, 136, 0.4);' : 'background: rgba(255,255,255,0.03); color: var(--text-muted); opacity: 0.5;' }}">
                        {{ $day }}
                    </span>
                @endforeach
            </div>
        </div>

        @if(auth()->user()->isAdmin() || (auth()->user()->isDoctor() && ($doctor->user_id === auth()->id() || $doctor->email === auth()->user()->email)))
            <!-- Direct Change Password Card -->
            <div class="card" id="security-settings" style="border-color: rgba(56, 189, 248, 0.3);">
                <div class="card-header">
                    <div class="card-title"><i class="fa-solid fa-key" style="color: var(--accent)"></i> Security Settings - Change Password</div>
                </div>

                <form action="{{ route('doctors.update', $doctor) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- Hidden profile & schedule fields to pass validation -->
                    <input type="hidden" name="name" value="{{ $doctor->name }}">
                    <input type="hidden" name="specialization" value="{{ $doctor->specialization }}">
                    <input type="hidden" name="email" value="{{ $doctor->email }}">
                    <input type="hidden" name="phone" value="{{ $doctor->phone }}">
                    <input type="hidden" name="consultation_fee" value="{{ $doctor->consultation_fee }}">
                    <input type="hidden" name="room_number" value="{{ $doctor->room_number }}">
                    <input type="hidden" name="bio" value="{{ $doctor->bio }}">
                    <input type="hidden" name="start_time" value="{{ $doctor->start_time }}">
                    <input type="hidden" name="end_time" value="{{ $doctor->end_time }}">
                    <input type="hidden" name="slot_duration_minutes" value="{{ $doctor->slot_duration_minutes }}">
                    <input type="hidden" name="status" value="{{ $doctor->status }}">
                    @foreach($doctor->available_days ?? [] as $day)
                        <input type="hidden" name="available_days[]" value="{{ $day }}">
                    @endforeach

                    @if(auth()->user()->isDoctor())
                        <div class="form-group">
                            <label class="form-label">Current Password *</label>
                            <input type="password" name="current_password" class="form-control" placeholder="Enter current password" required>
                        </div>
                    @endif

                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">New Password *</label>
                            <input type="password" name="new_password" class="form-control" placeholder="Minimum 6 characters" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Confirm New Password *</label>
                            <input type="password" name="new_password_confirmation" class="form-control" placeholder="Re-enter new password" required>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; margin-top: 1rem;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-lock"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>
        @endif

        @if(auth()->user()->isAdmin())
            <div class="card">
                <div class="card-header">
                    <div class="card-title"><i class="fa-solid fa-calendar-days" style="color: #fbbf24"></i> Upcoming Consultations</div>
                </div>
                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Date & Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($upcomingAppointments as $apt)
                                <tr>
                                    <td>{{ $apt->patient->name }}</td>
                                    <td>{{ $apt->appointment_date->format('M d, Y') }} at {{ $apt->appointment_time }}</td>
                                    <td><span class="badge badge-{{ strtolower($apt->status) }}">{{ $apt->status }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="text-align: center; color: var(--text-muted);">No upcoming appointments scheduled.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
