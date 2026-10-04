@extends('layouts.app')

@section('title', 'Add New Doctor - MediCare')
@section('page-header', 'Create Doctor Profile')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-user-doctor" style="color: var(--primary)"></i> Doctor Information</div>
            <a href="{{ route('doctors.index') }}" class="btn btn-secondary btn-sm"><i class="fa-solid fa-arrow-left"></i> Back to List</a>
        </div>

        <form action="{{ route('doctors.store') }}" method="POST">
            @csrf
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Specialization *</label>
                    <input type="text" name="specialization" class="form-control" value="{{ old('specialization') }}" required>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Email Address *</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Phone Number *</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Login Password *</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Consultation Fee ($) *</label>
                    <input type="number" step="0.01" name="consultation_fee" class="form-control" value="{{ old('consultation_fee') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Room / Office Number</label>
                    <input type="text" name="room_number" class="form-control" value="{{ old('room_number') }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Professional Biography</label>
                <textarea name="bio" class="form-control" rows="3">{{ old('bio') }}</textarea>
            </div>

            <hr style="border: none; border-top: 1px solid var(--border-color); margin: 1.5rem 0;">

            <h3 style="font-family: var(--font-title); font-size: 1.1rem; margin-bottom: 1rem; color: var(--accent);"><i class="fa-regular fa-clock"></i> Practice Schedule & Availability</h3>

            <div class="form-group">
                <label class="form-label">Available Days *</label>
                <div style="display: flex; gap: 15px; flex-wrap: wrap; margin-top: 5px;">
                    @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                        <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 0.9rem;">
                            <input type="checkbox" name="available_days[]" value="{{ $day }}" {{ in_array($day, old('available_days', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'])) ? 'checked' : '' }}>
                            {{ $day }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Shift Start Time *</label>
                    <input type="time" name="start_time" class="form-control" value="{{ old('start_time', '09:00') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Shift End Time *</label>
                    <input type="time" name="end_time" class="form-control" value="{{ old('end_time', '17:00') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Slot Duration (Minutes) *</label>
                    <select name="slot_duration_minutes" class="form-select">
                        <option value="15" {{ old('slot_duration_minutes') == 15 ? 'selected' : '' }}>15 Minutes</option>
                        <option value="30" {{ old('slot_duration_minutes', 30) == 30 ? 'selected' : '' }}>30 Minutes</option>
                        <option value="45" {{ old('slot_duration_minutes') == 45 ? 'selected' : '' }}>45 Minutes</option>
                        <option value="60" {{ old('slot_duration_minutes') == 60 ? 'selected' : '' }}>60 Minutes</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Account Status *</label>
                    <select name="status" class="form-select">
                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 1.5rem;">
                <a href="{{ route('doctors.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-check"></i> Save Doctor Profile</button>
            </div>
        </form>
    </div>
</div>
@endsection
