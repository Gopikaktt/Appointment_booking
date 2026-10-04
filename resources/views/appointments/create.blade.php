@extends('layouts.app')

@section('title', 'Book Appointment - MediCare')
@section('page-header', 'Schedule Doctor Appointment')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-calendar-plus" style="color: var(--primary)"></i> Book Appointment</div>
        </div>

        <form action="{{ route('appointments.store') }}" method="POST" id="bookingForm">
            @csrf

            <!-- 1. Select Doctor -->
            <div class="form-group">
                <label class="form-label">Select Specialist / Doctor *</label>
                <select name="doctor_id" id="doctorId" class="form-select" required>
                    <option value="">-- Choose a Doctor --</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" 
                            data-fee="{{ number_format($doctor->consultation_fee, 2) }}"
                            data-spec="{{ $doctor->specialization }}"
                            data-room="{{ $doctor->room_number ?? 'Consultation Room' }}"
                            {{ old('doctor_id', $selectedDoctorId) == $doctor->id ? 'selected' : '' }}>
                            {{ $doctor->name }} ({{ $doctor->specialization }}) - ${{ number_format($doctor->consultation_fee, 2) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Doctor Summary Banner -->
            <div id="doctorBanner" style="display: none; background: rgba(13, 148, 136, 0.1); border: 1px solid var(--primary); border-radius: var(--radius-md); padding: 1rem; margin-bottom: 1.25rem;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <div id="bannerName" style="font-weight: 700; font-size: 1.1rem; color: var(--text-main);"></div>
                        <div id="bannerSpec" style="color: var(--accent); font-size: 0.85rem;"></div>
                    </div>
                    <div>
                        <span id="bannerFee" class="badge badge-active" style="font-size: 0.9rem;"></span>
                    </div>
                </div>
            </div>

            <!-- 2. Select Date -->
            <div class="form-group">
                <label class="form-label">Appointment Date *</label>
                <input type="date" name="appointment_date" id="appointmentDate" class="form-control" 
                       min="{{ date('Y-m-d') }}" 
                       value="{{ old('appointment_date', date('Y-m-d')) }}" required>
            </div>

            <!-- 3. Available Time Slots Picker -->
            <div class="form-group">
                <label class="form-label" style="display: flex; justify-content: space-between; align-items: center;">
                    <span>Available Time Slots *</span>
                    <span id="slotStatus" style="font-size: 0.8rem; color: var(--text-muted);">Please select doctor & date</span>
                </label>

                <input type="hidden" name="appointment_time" id="selectedTime" value="{{ old('appointment_time') }}" required>

                <div id="slotsContainer" class="slots-picker-grid">
                    <div style="color: var(--text-muted); font-size: 0.875rem; grid-column: 1 / -1; padding: 1rem; text-align: center; border: 1px dashed var(--border-color); border-radius: var(--radius-md);">
                        Select a doctor and date above to load live available time slots.
                    </div>
                </div>
            </div>

            <!-- 4. Symptoms / Notes -->
            <div class="form-group" style="margin-top: 1.5rem;">
                <label class="form-label">Symptoms & Reason for Visit</label>
                <textarea name="symptoms_notes" class="form-control" rows="3" placeholder="Briefly describe your symptoms or reason for booking...">{{ old('symptoms_notes') }}</textarea>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 1.5rem;">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" id="submitBtn" class="btn btn-primary" disabled>
                    <i class="fa-solid fa-lock"></i> Confirm & Book Appointment
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const doctorSelect = document.getElementById('doctorId');
    const dateInput = document.getElementById('appointmentDate');
    const slotsContainer = document.getElementById('slotsContainer');
    const slotStatus = document.getElementById('slotStatus');
    const selectedTimeInput = document.getElementById('selectedTime');
    const submitBtn = document.getElementById('submitBtn');
    const doctorBanner = document.getElementById('doctorBanner');

    function updateDoctorBanner() {
        const option = doctorSelect.options[doctorSelect.selectedIndex];
        if (option && option.value) {
            document.getElementById('bannerName').textContent = option.text.split(' - ')[0];
            document.getElementById('bannerSpec').textContent = option.getAttribute('data-spec');
            document.getElementById('bannerFee').textContent = 'Consultation Fee: $' + option.getAttribute('data-fee');
            doctorBanner.style.display = 'block';
        } else {
            doctorBanner.style.display = 'none';
        }
    }

    function fetchAvailableSlots() {
        const doctorId = doctorSelect.value;
        const date = dateInput.value;

        updateDoctorBanner();

        if (!doctorId || !date) {
            slotsContainer.innerHTML = '<div style="color: var(--text-muted); font-size: 0.875rem; grid-column: 1 / -1; padding: 1rem; text-align: center; border: 1px dashed var(--border-color); border-radius: var(--radius-md);">Select a doctor and date above to load live available time slots.</div>';
            slotStatus.textContent = 'Please select doctor & date';
            selectedTimeInput.value = '';
            submitBtn.disabled = true;
            return;
        }

        slotStatus.textContent = 'Fetching slots...';
        slotsContainer.innerHTML = '<div style="color: var(--accent); grid-column: 1 / -1; text-align: center; padding: 1rem;"><i class="fa-solid fa-spinner fa-spin"></i> Loading slots...</div>';

        fetch(`/api/doctors/${doctorId}/slots?date=${date}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.slots.length > 0) {
                    slotStatus.textContent = `${data.slots.length} available slots found`;
                    slotsContainer.innerHTML = '';
                    
                    data.slots.forEach(slot => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'slot-btn';
                        btn.textContent = slot.time_formatted;

                        if (selectedTimeInput.value === slot.time_formatted) {
                            btn.classList.add('selected');
                            submitBtn.disabled = false;
                        }

                        btn.addEventListener('click', function () {
                            document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('selected'));
                            btn.classList.add('selected');
                            selectedTimeInput.value = slot.time_formatted;
                            submitBtn.disabled = false;
                        });

                        slotsContainer.appendChild(btn);
                    });
                } else {
                    slotStatus.textContent = 'No available slots';
                    slotsContainer.innerHTML = '<div style="color: var(--danger); grid-column: 1 / -1; text-align: center; padding: 1rem; background: rgba(239, 68, 68, 0.1); border-radius: var(--radius-md);"><i class="fa-solid fa-calendar-xmark"></i> No available slots for this date. The doctor may be off-duty or fully booked.</div>';
                    selectedTimeInput.value = '';
                    submitBtn.disabled = true;
                }
            })
            .catch(err => {
                console.error(err);
                slotStatus.textContent = 'Error loading slots';
                slotsContainer.innerHTML = '<div style="color: var(--danger); grid-column: 1 / -1; text-align: center; padding: 1rem;">Failed to retrieve slots. Please try again.</div>';
            });
    }

    doctorSelect.addEventListener('change', fetchAvailableSlots);
    dateInput.addEventListener('change', fetchAvailableSlots);

    // Initial trigger if values are preselected
    if (doctorSelect.value && dateInput.value) {
        fetchAvailableSlots();
    }
});
</script>
@endpush
@endsection
