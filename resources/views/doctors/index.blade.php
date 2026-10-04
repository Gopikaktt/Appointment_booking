@extends('layouts.app')

@section('title', 'Doctors Directory - MediCare')
@section('page-header', 'Doctors Directory')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
        <!-- Search & Filter Form -->
        <form action="{{ route('doctors.index') }}" method="GET" style="display: flex; gap: 10px; flex: 1; min-width: 300px; flex-wrap: wrap;">
            <input type="text" name="search" class="form-control" style="flex: 1; min-width: 200px;" placeholder="Search by name, specialization..." value="{{ request('search') }}">
            <select name="specialization" class="form-select" style="max-width: 200px;" onchange="this.form.submit()">
                <option value="">All Specializations</option>
                @foreach($specializations as $spec)
                    <option value="{{ $spec }}" {{ request('specialization') == $spec ? 'selected' : '' }}>{{ $spec }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-secondary"><i class="fa-solid fa-magnifying-glass"></i> Search</button>

            @if(request('search') || request('specialization'))
                <a href="{{ route('doctors.index') }}" class="btn btn-danger" style="background: rgba(239, 68, 68, 0.2); border-color: rgba(239, 68, 68, 0.4); color: #fca5a5;" title="Reset filters">
                    <i class="fa-solid fa-rotate-left"></i> Reset
                </a>
            @endif
        </form>

        @if(auth()->user()->isAdmin())
            <a href="{{ route('doctors.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Add New Doctor
            </a>
        @endif
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Doctor</th>
                    <th>Specialization</th>
                    <th>Contact</th>
                    <th>Consultation Fee</th>
                    <th>Available Days</th>
                    <th>Status</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($doctors as $doctor)
                    <tr>
                        <td>
                            <div style="font-weight: 700; color: var(--text-main);">
                                <a href="{{ route('doctors.show', $doctor) }}" style="color: inherit; text-decoration: none;">
                                    {{ $doctor->name }}
                                </a>
                            </div>
                            <div style="font-size: 0.8rem; color: var(--text-muted)">{{ $doctor->room_number ?? 'Room N/A' }}</div>
                        </td>
                        <td>
                            <span style="color: var(--accent); font-weight: 600;">{{ $doctor->specialization }}</span>
                        </td>
                        <td>
                            <div><i class="fa-regular fa-envelope" style="width: 16px;"></i> {{ $doctor->email }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);"><i class="fa-solid fa-phone" style="width: 16px;"></i> {{ $doctor->phone }}</div>
                        </td>
                        <td>
                            <span style="font-weight: 700; color: #34d399;">${{ number_format($doctor->consultation_fee, 2) }}</span>
                        </td>
                        <td>
                            <div style="font-size: 0.8rem; max-width: 180px; color: var(--text-muted);">
                                {{ implode(', ', $doctor->available_days ?? []) }}
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-{{ $doctor->status }}">
                                {{ ucfirst($doctor->status) }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: flex; justify-content: flex-end; gap: 6px;">
                                <a href="{{ route('doctors.show', $doctor) }}" class="btn btn-secondary btn-sm" title="View Profile">
                                    <i class="fa-solid fa-eye"></i>
                                </a>

                                @if(auth()->user()->isPatient())
                                    <a href="{{ route('appointments.create', ['doctor_id' => $doctor->id]) }}" class="btn btn-primary btn-sm">
                                        <i class="fa-solid fa-calendar-check"></i> Book
                                    </a>
                                @endif

                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('doctors.edit', $doctor) }}" class="btn btn-secondary btn-sm" title="Edit Doctor">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('doctors.destroy', $doctor) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this doctor?');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete Doctor">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2rem;">No doctors found matching criteria.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $doctors->withQueryString()->links() }}
    </div>
</div>
@endsection
