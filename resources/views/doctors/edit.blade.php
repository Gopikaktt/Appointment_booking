@extends('layouts.app')

@section('title', 'Edit Doctor - ' . $doctor->name)
@section('page-header', 'Edit Doctor Profile')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-user-pen" style="color: var(--primary)"></i> Update Doctor Details</div>
            <a href="{{ auth()->user()->isDoctor() ? route('dashboard') : route('doctors.index') }}" class="btn btn-secondary btn-sm"><i class="fa-solid fa-arrow-left"></i> Back to Dashboard</a>
        </div>

        <form action="{{ route('doctors.update', $doctor) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $doctor->name) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Specialization *</label>
                    <input type="text" name="specialization" class="form-control" value="{{ old('specialization', $doctor->specialization) }}" required>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Email Address *</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $doctor->email) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Phone Number *</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $doctor->phone) }}" required>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Consultation Fee ($) *</label>
                    <input type="number" step="0.01" name="consultation_fee" class="form-control" value="{{ old('consultation_fee', $doctor->consultation_fee) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Room / Office Number</label>
                    <input type="text" name="room_number" class="form-control" value="{{ old('room_number', $doctor->room_number) }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Professional Biography</label>
                <textarea name="bio" class="form-control" rows="3">{{ old('bio', $doctor->bio) }}</textarea>
            </div>

            <hr style="border: none; border-top: 1px solid var(--border-color); margin: 1.5rem 0;">

            <h3 style="font-family: var(--font-title); font-size: 1.1rem; margin-bottom: 1rem; color: var(--accent);"><i class="fa-regular fa-clock"></i> Practice Schedule & Availability</h3>

            <div class="form-group">
                <label class="form-label">Available Days *</label>
                <div style="display: flex; gap: 15px; flex-wrap: wrap; margin-top: 5px;">
                    @php $days = old('available_days', $doctor->available_days ?? []); @endphp
                    @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 0.9rem;">
                            <input type="checkbox" name="available_days[]" value="{{ $day }}" {{ in_array($day, $days) ? 'checked' : '' }}>
                            {{ $day }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Shift Start Time *</label>
                    <input type="time" name="start_time" class="form-control" value="{{ old('start_time', $doctor->start_time) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Shift End Time *</label>
                    <input type="time" name="end_time" class="form-control" value="{{ old('end_time', $doctor->end_time) }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Slot Duration (Minutes) *</label>
                    <select name="slot_duration_minutes" class="form-select">
                        <option value="15" {{ old('slot_duration_minutes', $doctor->slot_duration_minutes) == 15 ? 'selected' : '' }}>15 Minutes</option>
                        <option value="30" {{ old('slot_duration_minutes', $doctor->slot_duration_minutes) == 30 ? 'selected' : '' }}>30 Minutes</option>
                        <option value="45" {{ old('slot_duration_minutes', $doctor->slot_duration_minutes) == 45 ? 'selected' : '' }}>45 Minutes</option>
                        <option value="60" {{ old('slot_duration_minutes', $doctor->slot_duration_minutes) == 60 ? 'selected' : '' }}>60 Minutes</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Account Status *</label>
                    <select name="status" class="form-select">
                        <option value="active" {{ old('status', $doctor->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $doctor->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <hr style="border: none; border-top: 1px solid var(--border-color); margin: 1.5rem 0;">

            <h3 style="font-family: var(--font-title); font-size: 1.1rem; margin-bottom: 0.5rem; color: var(--accent);"><i class="fa-solid fa-key"></i> Security Settings - Change Password</h3>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.25rem;">Leave password fields empty if you do not wish to change your password.</p>

            @if(auth()->user()->isDoctor())
                <div class="form-group">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-control">
                </div>
            @endif

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" name="new_password" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="new_password_confirmation" class="form-control">
                </div>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 1.5rem;">
                <a href="{{ auth()->user()->isDoctor() ? route('dashboard') : route('doctors.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Update Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
