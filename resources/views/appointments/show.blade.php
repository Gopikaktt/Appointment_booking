@extends('layouts.app')

@section('title', 'Appointment Details #' . $appointment->id . ' - MediCare')
@section('page-header', 'Appointment Overview')

@section('content')
<div style="max-width: 850px; margin: 0 auto;">
    <div style="margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
        <a href="{{ auth()->user()->isDoctor() ? route('dashboard') : (auth()->user()->isPatient() ? route('dashboard') : route('appointments.history')) }}" class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
        </a>

        <span class="badge badge-{{ strtolower($appointment->status) }}" style="font-size: 0.95rem; padding: 0.5rem 1rem;">
            Status: {{ $appointment->status }}
        </span>
    </div>

    <!-- Main Appointment Card -->
    <div class="card" style="border-color: rgba(13, 148, 136, 0.4); padding: 2rem;">
        <div class="card-header" style="padding-bottom: 1rem; margin-bottom: 1.5rem;">
            <div class="card-title" style="display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-calendar-check" style="color: var(--primary); font-size: 1.4rem;"></i>
                <span>Appointment Details</span>
            </div>
            <div style="font-size: 0.85rem; color: var(--text-muted);">
                Booking Ref ID: <strong>#{{ str_pad($appointment->id, 5, '0', STR_PAD_LEFT) }}</strong>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
            <!-- Patient Information Box -->
            <div style="background: rgba(15, 23, 42, 0.6); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                <div style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: var(--accent); letter-spacing: 0.8px; margin-bottom: 1rem;">
                    <i class="fa-solid fa-user-circle"></i> Patient Profile
                </div>

                <div style="margin-bottom: 0.85rem;">
                    <div style="font-size: 0.8rem; color: var(--text-muted);">PATIENT NAME</div>
                    <div style="font-weight: 700; font-size: 1.1rem; color: var(--text-main); margin-top: 2px;">
                        {{ $appointment->patient->name }}
                    </div>
                </div>

                <div style="margin-bottom: 0.85rem;">
                    <div style="font-size: 0.8rem; color: var(--text-muted);">PHONE NUMBER</div>
                    <div style="font-weight: 600; font-size: 1rem; color: #2dd4bf; margin-top: 2px;">
                        <i class="fa-solid fa-phone" style="margin-right: 6px;"></i> {{ $appointment->patient->phone ?? 'Not provided' }}
                    </div>
                </div>

                <div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);">EMAIL ADDRESS</div>
                    <div style="font-weight: 500; font-size: 0.95rem; color: var(--text-main); margin-top: 2px;">
                        <i class="fa-regular fa-envelope" style="margin-right: 6px; color: var(--accent);"></i> {{ $appointment->patient->email }}
                    </div>
                </div>
            </div>

            <!-- Doctor & Schedule Box -->
            <div style="background: rgba(15, 23, 42, 0.6); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                <div style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: var(--accent); letter-spacing: 0.8px; margin-bottom: 1rem;">
                    <i class="fa-solid fa-stethoscope"></i> Doctor & Schedule Info
                </div>

                <div style="margin-bottom: 0.85rem;">
                    <div style="font-size: 0.8rem; color: var(--text-muted);">ASSIGNED DOCTOR</div>
                    <div style="font-weight: 700; font-size: 1.1rem; color: var(--text-main); margin-top: 2px;">
                        {{ $appointment->doctor->name }}
                    </div>
                    <div style="font-size: 0.8rem; color: var(--accent);">
                        {{ $appointment->doctor->specialization }} ({{ $appointment->doctor->room_number ?? 'Consultation Room' }})
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">APPOINTMENT DATE</div>
                        <div style="font-weight: 700; font-size: 0.95rem; color: var(--text-main); margin-top: 2px;">
                            <i class="fa-regular fa-calendar-days" style="color: var(--primary);"></i> {{ $appointment->appointment_date->format('M d, Y') }}
                        </div>
                    </div>

                    <div>
                        <div style="font-size: 0.8rem; color: var(--text-muted);">TIME SLOT</div>
                        <div style="font-weight: 700; font-size: 0.95rem; color: #38bdf8; margin-top: 2px;">
                            <i class="fa-regular fa-clock"></i> {{ $appointment->appointment_time }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Symptoms & Reason for Visit -->
        <div style="background: rgba(15, 23, 42, 0.4); padding: 1.25rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); margin-bottom: 2rem;">
            <div style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: #fbbf24; letter-spacing: 0.8px; margin-bottom: 0.5rem;">
                <i class="fa-solid fa-notes-medical"></i> Symptoms & Reason for Visit
            </div>
            <p style="color: var(--text-main); font-size: 0.95rem; line-height: 1.6;">
                {{ $appointment->symptoms_notes ?? 'No specific symptoms or notes recorded for this appointment.' }}
            </p>
        </div>

        <!-- Status Management / Quick Actions -->
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
            <div style="font-size: 0.9rem; color: var(--text-muted);">
                Consultation Fee: <strong style="color: #34d399; font-size: 1.1rem;">${{ number_format($appointment->doctor->consultation_fee, 2) }}</strong>
            </div>

            @if(auth()->user()->isDoctor() || auth()->user()->isAdmin())
                <form action="{{ route('appointments.updateStatus', $appointment) }}" method="POST" style="display: flex; gap: 8px;">
                    @csrf
                    @method('PATCH')
                    
                    @if($appointment->status === 'Pending')
                        <input type="hidden" name="status" value="Confirmed">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Confirm Appointment</button>
                    @elseif($appointment->status === 'Confirmed')
                        <input type="hidden" name="status" value="Completed">
                        <button type="submit" class="btn btn-primary" style="background: var(--success);"><i class="fa-solid fa-check-double"></i> Mark Completed</button>
                    @endif

                    @if($appointment->status === 'Cancelled')
                        <button type="submit" name="status" value="Pending" class="btn btn-secondary" style="background: rgba(245, 158, 11, 0.2); border-color: rgba(245, 158, 11, 0.4); color: #fbbf24;" onclick="return confirm('Re-activate this cancelled appointment?')">
                            <i class="fa-solid fa-rotate-left"></i> Re-activate Appointment
                        </button>
                    @endif

                    @if($appointment->status !== 'Cancelled' && $appointment->status !== 'Completed')
                        <button type="submit" name="status" value="Cancelled" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this appointment?')">
                            <i class="fa-solid fa-xmark"></i> Cancel Appointment
                        </button>
                    @endif
                </form>
            @elseif(auth()->user()->isPatient())
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    @if(in_array($appointment->status, ['Pending', 'Confirmed']))
                        <form action="{{ route('appointments.updateStatus', $appointment) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="Cancelled">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                <i class="fa-solid fa-xmark"></i> Cancel Appointment
                            </button>
                        </form>
                    @elseif(in_array($appointment->status, ['Cancelled', 'Completed']))
                        <a href="{{ route('appointments.create', ['doctor_id' => $appointment->doctor_id]) }}" class="btn btn-primary">
                            <i class="fa-solid fa-calendar-plus"></i> Re-Book Session
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
