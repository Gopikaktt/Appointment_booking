<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'specialization',
        'email',
        'phone',
        'bio',
        'consultation_fee',
        'room_number',
        'available_days',
        'start_time',
        'end_time',
        'slot_duration_minutes',
        'status',
    ];

    protected $casts = [
        'available_days' => 'array',
        'consultation_fee' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get available time slots for a given Y-m-d date
     */
    public function getAvailableSlots(string $date): array
    {
        if ($this->status !== 'active') {
            return [];
        }

        $carbonDate = \Carbon\Carbon::parse($date);
        $dayOfWeek = $carbonDate->format('l'); // e.g. "Monday"

        $days = $this->available_days ?? ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"];
        if (!in_array($dayOfWeek, $days)) {
            return [];
        }

        // Get booked times on this date (excluding cancelled)
        $bookedTimes = $this->appointments()
            ->whereDate('appointment_date', $date)
            ->where('status', '!=', 'Cancelled')
            ->pluck('appointment_time')
            ->map(function ($time) {
                // Normalize to H:i format
                return \Carbon\Carbon::parse($time)->format('H:i');
            })
            ->toArray();

        $startTime = \Carbon\Carbon::parse($this->start_time ?? '09:00');
        $endTime = \Carbon\Carbon::parse($this->end_time ?? '17:00');
        $duration = $this->slot_duration_minutes ?? 30;

        $slots = [];
        $current = $startTime->copy();

        $now = \Carbon\Carbon::now();
        $isToday = $carbonDate->isToday();

        while ($current->lt($endTime)) {
            $slotTime24 = $current->format('H:i');
            $slotTimeFormatted = $current->format('h:i A');

            // Check if slot is in the past for today
            $isPast = false;
            if ($isToday) {
                $slotDateTime = \Carbon\Carbon::parse($date . ' ' . $slotTime24);
                if ($slotDateTime->isPast()) {
                    $isPast = true;
                }
            }

            if (!in_array($slotTime24, $bookedTimes) && !$isPast) {
                $slots[] = [
                    'time_24' => $slotTime24,
                    'time_formatted' => $slotTimeFormatted,
                ];
            }

            $current->addMinutes($duration);
        }

        return $slots;
    }
}
