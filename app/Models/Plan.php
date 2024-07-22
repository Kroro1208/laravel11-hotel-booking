<?php

namespace App\Models;

use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'image', 'price', 'start_date', 'end_date', 'is_reserved'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_reserved' => 'boolean',
    ];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function planRooms(): HasMany
    {
        return $this->hasMany(PlanRoom::class);
    }

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class);
    }

    public function reservationSlots(): HasMany
    {
        return $this->hasMany(ReservationSlot::class);
    }

    public function updateReservationStatus(): void
    {
        $this->is_reserved = !$this->reservationSlots()
            ->where('status', '!=', 'unavailable')
            ->exists();
        $this->save();
    }

    public function dateRange(): CarbonPeriod
    {
        return CarbonPeriod::create($this->start_date, $this->end_date);
    }

    public function getAvailabilityForDateRange($startDate = null, $endDate = null): array
    {
        $dateRange = $startDate && $endDate
            ? CarbonPeriod::create($startDate, $endDate)
            : $this->dateRange();

        $reservationSlots = $this->reservationSlots()
            ->with('roomType')
            ->whereBetween('date', [$dateRange->getStartDate(), $dateRange->getEndDate()])
            ->get()
            ->groupBy(['date', 'room_type_id']);

        $availability = [];
        foreach ($dateRange as $date) {
            $dateString = $date->format('Y-m-d');
            $dayAvailability = [];

            foreach ($this->planRooms as $planRoom) {
                $roomTypeName = $planRoom->roomType->name;
                $slot = $reservationSlots[$dateString][$planRoom->room_type_id] ?? null;

                $availableRooms = $slot
                    ? $slot->first()->total_rooms - $slot->first()->booked_rooms
                    : $planRoom->room_count;

                $dayAvailability[$roomTypeName] = $availableRooms;
            }

            $availability[$dateString] = $dayAvailability;
        }

        return $availability;
    }
}
