@extends('layouts.app')

@section('title', 'Patient Management - MediCare')
@section('page-header', 'Registered Patients Management')

@section('content')
<!-- Search & Filter Card -->
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
        <form action="{{ route('patients.index') }}" method="GET" style="display: flex; gap: 10px; flex: 1; min-width: 300px; flex-wrap: wrap;">
            <div style="position: relative; flex: 1; min-width: 200px;">
                <input type="text" name="search" id="patientSearchInput" class="form-control" placeholder="Search patient name, email, phone..." value="{{ request('search') }}" style="padding-left: 2.2rem;">
                <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
            </div>
            <button type="submit" class="btn btn-secondary"><i class="fa-solid fa-magnifying-glass"></i> Search</button>

            @if(request('search'))
                <a href="{{ route('patients.index') }}" class="btn btn-danger" style="background: rgba(239, 68, 68, 0.2); border-color: rgba(239, 68, 68, 0.4); color: #fca5a5;" title="Reset search">
                    <i class="fa-solid fa-rotate-left"></i> Reset
                </a>
            @endif
        </form>
    </div>
</div>

<!-- Patients List Table -->
<div class="card">
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Patient Name</th>
                    <th>Email Address</th>
                    <th>Phone Number</th>
                    <th>Registered Date</th>
                    <th>Account Status</th>
                    <th>Total Bookings</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patients as $patient)
                    <tr class="patient-row" data-name="{{ strtolower($patient->name) }}" data-email="{{ strtolower($patient->email) }}" data-phone="{{ strtolower($patient->phone ?? '') }}" onclick="if (!event.target.closest('a, button, select, input')) window.location.href='{{ route('patients.show', $patient) }}';" style="cursor: pointer;">
                        <td>
                            <a href="{{ route('patients.show', $patient) }}" style="text-decoration: none; color: inherit;">
                                <div style="font-weight: 700; color: var(--text-main);">{{ $patient->name }}</div>
                            </a>
                        </td>
                        <td>
                            <div><i class="fa-regular fa-envelope" style="width: 16px; color: var(--accent);"></i> {{ $patient->email }}</div>
                        </td>
                        <td>
                            <span style="color: #2dd4bf;"><i class="fa-solid fa-phone" style="width: 16px;"></i> {{ $patient->phone ?? 'Not provided' }}</span>
                        </td>
                        <td>
                            <div style="font-size: 0.85rem; color: var(--text-muted);">
                                {{ $patient->created_at ? $patient->created_at->format('M d, Y') : 'N/A' }}
                            </div>
                        </td>
                        <td>
                            @if($patient->status === 'inactive')
                                <span class="badge badge-inactive"><i class="fa-solid fa-user-xmark"></i> Inactive</span>
                            @else
                                <span class="badge badge-active"><i class="fa-solid fa-user-check"></i> Active</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-active" style="background: rgba(56, 189, 248, 0.15); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.3);">{{ $patient->appointments_count }} Bookings</span>
                        </td>
                        <td style="text-align: right;">
                            <div style="display: flex; justify-content: flex-end; gap: 6px; align-items: center;">
                                <a href="{{ route('patients.show', $patient) }}" class="btn btn-secondary btn-sm" title="View Patient Profile">
                                    <i class="fa-solid fa-eye"></i>
                                </a>

                                <a href="{{ route('patients.edit', $patient) }}" class="btn btn-secondary btn-sm" title="Edit Patient Info" style="background: rgba(56, 189, 248, 0.15); border: 1px solid rgba(56, 189, 248, 0.3); color: #38bdf8;">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>

                                @if(auth()->user()->isAdmin())
                                    <form action="{{ route('patients.toggleStatus', $patient) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('PATCH')
                                        @if($patient->status === 'inactive')
                                            <button type="submit" class="btn btn-sm" style="background: rgba(34, 197, 94, 0.2); border: 1px solid rgba(34, 197, 94, 0.4); color: #4ade80;" title="Activate Patient Account" onclick="return confirm('Activate {{ $patient->name }}\'s account?');">
                                                <i class="fa-solid fa-user-check"></i> Activate
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-sm" style="background: rgba(245, 158, 11, 0.2); border: 1px solid rgba(245, 158, 11, 0.4); color: #fbbf24;" title="Deactivate Patient Account" onclick="return confirm('Deactivate {{ $patient->name }}\'s account? They will not be able to log into the system.');">
                                                <i class="fa-solid fa-user-xmark"></i> Deactivate
                                            </button>
                                        @endif
                                    </form>

                                    <form action="{{ route('patients.destroy', $patient) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete patient {{ $patient->name }}? All their appointment records will also be deleted.');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete Patient">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2.5rem;">
                            <i class="fa-solid fa-users-slash" style="font-size: 2rem; margin-bottom: 0.5rem; display: block;"></i>
                            No registered patients found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.5rem;">
        {{ $patients->withQueryString()->links() }}
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('patientSearchInput');
    const rows = document.querySelectorAll('.patient-row');

    if (searchInput && rows.length > 0) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            rows.forEach(row => {
                const name = row.getAttribute('data-name') || '';
                const email = row.getAttribute('data-email') || '';
                const phone = row.getAttribute('data-phone') || '';
                if (name.includes(query) || email.includes(query) || phone.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});
</script>
@endpush
@endsection
