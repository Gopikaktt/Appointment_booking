@extends('layouts.app')

@section('title', 'My Profile - MediCare')
@section('page-header', 'My Profile')

@section('content')
<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem; max-width: 1000px; margin: 0 auto;">
    <!-- Left Column: Patient Profile Card -->
    <div>
        <div class="card" style="text-align: center; margin-bottom: 1.5rem;">
            <div style="width: 84px; height: 84px; background: linear-gradient(135deg, #6366f1, #a855f7); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; font-weight: 700; color: #fff; margin: 0 auto 1rem; box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            
            <h2 style="font-family: var(--font-title); font-size: 1.4rem;">{{ $user->name }}</h2>
            <div style="color: var(--accent); font-weight: 600; font-size: 0.9rem; margin-top: 4px;">
                <i class="fa-solid fa-shield-halved"></i> {{ ucfirst($user->role) }} Account
            </div>

            <div style="text-align: left; background: rgba(15,23,42,0.5); padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); font-size: 0.9rem; margin: 1.25rem 0;">
                <div style="margin-bottom: 8px;"><i class="fa-regular fa-envelope" style="width: 20px; color: var(--accent);"></i> {{ $user->email }}</div>
                <div style="margin-bottom: 8px;"><i class="fa-solid fa-phone" style="width: 20px; color: #2dd4bf;"></i> {{ $user->phone ?? 'No phone added' }}</div>
                <div><i class="fa-regular fa-calendar-check" style="width: 20px; color: #fbbf24;"></i> Member since {{ $user->created_at ? $user->created_at->format('M Y') : '2026' }}</div>
            </div>
        </div>

        @if($user->isPatient())
            <!-- Stats Summary Card -->
            <div class="card">
                <div class="card-header" style="margin-bottom: 1rem;">
                    <div class="card-title" style="font-size: 1.05rem;"><i class="fa-solid fa-chart-simple" style="color: var(--primary);"></i> Booking Activity</div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <span style="color: var(--text-muted); font-size: 0.9rem;">Total Appointments</span>
                        <strong style="color: var(--text-main);">{{ $stats['total'] }}</strong>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <span style="color: var(--text-muted); font-size: 0.9rem;">Pending Bookings</span>
                        <span class="badge badge-pending">{{ $stats['pending'] }}</span>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <span style="color: var(--text-muted); font-size: 0.9rem;">Confirmed Visits</span>
                        <span class="badge badge-confirmed">{{ $stats['confirmed'] }}</span>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <span style="color: var(--text-muted); font-size: 0.9rem;">Completed Visits</span>
                        <span class="badge badge-completed">{{ $stats['completed'] }}</span>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0;">
                        <span style="color: var(--text-muted); font-size: 0.9rem;">Cancelled Visits</span>
                        <span class="badge badge-cancelled">{{ $stats['cancelled'] }}</span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Right Column: Profile Edit & Security Forms -->
    <div>
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fa-solid fa-user-pen" style="color: var(--primary);"></i> Edit Personal Details</div>
            </div>

            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Email Address *</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="e.g. +1 234 567 890">
                    </div>
                </div>

                <hr style="border: none; border-top: 1px solid var(--border-color); margin: 1.5rem 0;">

                <h3 style="font-family: var(--font-title); font-size: 1.1rem; margin-bottom: 0.5rem; color: var(--accent);"><i class="fa-solid fa-key"></i> Security Settings - Change Password</h3>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.25rem;">Leave password fields empty if you do not wish to change your password.</p>

                <div class="form-group">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-control" placeholder="Enter current password">
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-control" placeholder="Minimum 6 characters">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" class="form-control" placeholder="Re-enter new password">
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; margin-top: 1.5rem;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save"></i> Save Profile Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
