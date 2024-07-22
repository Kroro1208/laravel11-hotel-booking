<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\ReservationSlot;
use Carbon\Carbon;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function show(Plan $plan): View
    {
        $startDate = max($plan->start_date, Carbon::now());
        $endDate = $plan->end_date;

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
            $current->subDays($current->dayOfWeek);
            while ($current <= $monthEnd) {
                for ($i = 0; $i < 7; $i++) {
                    $date = $current->copy();
                    $week[] = [
                        'date' => $date,
                        'in_range' => $date >= $startDate && $date <= $endDate
                    ];
                    $current->addDay();
                }
                $calendar[$year][$month][] = $week;
                $week = [];
            }
        }

        // 予約枠情報を取得し、room_type_idとdateでグループ化
        $reservationSlots = $plan->reservationSlots()
            ->with('roomType')
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy(['room_type_id', function ($item) {
                return $item->date->format('Y-m-d');
            }]);

        // ReservationSlotのステータスオプションを取得
        $statusOptions = ReservationSlot::getStatusOptions();

        return view('frontend.plan.show', compact('plan', 'months', 'calendar', 'reservationSlots', 'statusOptions'));
    }
}
