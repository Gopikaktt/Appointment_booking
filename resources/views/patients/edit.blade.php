@extends('layouts.app')

@section('title', 'Edit Patient - ' . $patient->name)
@section('page-header', 'Edit Patient Profile')

@section('content')
<div style="max-width: 700px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-user-pen" style="color: var(--primary);"></i> Edit Patient Information
            </div>
            <a href="{{ route('patients.show', $patient) }}" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-arrow-left"></i> Back to Patient
            </a>
        </div>

        <form action="{{ route('patients.update', $patient) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Full Name -->
            <div class="form-group">
                <label for="name" class="form-label"><i class="fa-solid fa-user" style="color: var(--accent);"></i> Patient Full Name</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $patient->name) }}" required placeholder="e.g. John Doe">
            </div>

            <!-- Email Address -->
            <div class="form-group">
                <label for="email" class="form-label"><i class="fa-regular fa-envelope" style="color: var(--accent);"></i> Email Address</label>
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $patient->email) }}" required placeholder="e.g. john@example.com">
            </div>

            <!-- Phone Number -->
            <div class="form-group">
                <label for="phone" class="form-label"><i class="fa-solid fa-phone" style="color: #2dd4bf;"></i> Phone Number</label>
                <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $patient->phone) }}" placeholder="e.g. +1 (555) 234-5678">
            </div>

            <!-- Status (Admin Only) -->
            @if(auth()->user()->isAdmin())
            <div class="form-group">
                <label for="status" class="form-label"><i class="fa-solid fa-shield-halved" style="color: #fbbf24;"></i> Account Status</label>
                <select name="status" id="status" class="form-select" required>
                    <option value="active" {{ old('status', $patient->status) === 'active' ? 'selected' : '' }}>Active (Can log in & book appointments)</option>
                    <option value="inactive" {{ old('status', $patient->status) === 'inactive' ? 'selected' : '' }}>Inactive (Deactivated - login blocked)</option>
                </select>
            </div>
            @endif

            <!-- Reset Password Option -->
            <div class="form-group" style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border-color);">
                <label for="password" class="form-label">
                    <i class="fa-solid fa-lock" style="color: var(--danger);"></i> Reset Password 
                    <span style="font-weight: normal; color: var(--text-muted); font-size: 0.8rem;">(Leave blank to keep current password)</span>
                </label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Enter new password (min 6 characters)">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 2rem;">
                <a href="{{ route('patients.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-xmark"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-check"></i> Save Patient Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
