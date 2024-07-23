<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\ReservationSlot;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function show(Plan $plan): View
    {
        $startDate = Carbon::parse($plan->start_date);
        $endDate = Carbon::parse($plan->end_date);

        $months = [];
        $calendar = [];
        $current = $startDate->copy()->startOfMonth();

        while ($current <= $endDate) {
            $year = $current->year;
            $month = $current->month;

            if (!isset($months[$year])) {
                $months[$year] = [];
            }
            $months[$year][] = $month;

            $calendar[$year][$month] = [];
            $monthStart = $current->copy()->startOfMonth();
            $monthEnd = $current->copy()->endOfMonth();

            $week = [];
            $weekStart = $monthStart->copy()->startOfWeek(CarbonInterface::SUNDAY);
            while ($weekStart <= $monthEnd) {
                for ($i = 0; $i < 7; $i++) {
                    $date = $weekStart->copy();
                    $inRange = $date->between($startDate, $endDate);
                    $week[] = [
                        'date' => $date,
                        'in_range' => $inRange
                    ];
                    $weekStart->addDay();
                }
                $calendar[$year][$month][] = $week;
                $week = [];
            }

            $current->addMonth();
        }

        // 予約枠情報を取得し、room_type_idとdateでグループ化
        $reservationSlots = $plan->reservationSlots()
            ->with('roomType')
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy(['room_type_id', function ($item) {
                return Carbon::parse($item->date)->format('Y-m-d');
            }]);

        Log::info("Number of reservation slots: " . $reservationSlots->count());

        // ReservationSlotのステータスオプションを取得
        $statusOptions = ReservationSlot::getStatusOptions();

        return view('frontend.plan.show', compact('plan', 'months', 'calendar', 'reservationSlots', 'statusOptions'));
    }
}
