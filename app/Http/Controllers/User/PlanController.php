<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\ReservationSlot;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(): View
    {
        $plans = Plan::with('roomTypes')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->paginate(12);

        return view('frontend.index', ['plans' => $plans]);
    }

    public function show(Request $request, Plan $plan): View
    {
        $plan->load('roomTypes');

        $roomTypeId = $request->query('room_type_id', $plan->roomTypes->first()->id);
        $roomType = $plan->roomTypes->find($roomTypeId);

        $startDate = $plan->start_date;
        $endDate = $plan->end_date;

        $reservationSlots = ReservationSlot::where('room_type_id', $roomTypeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->keyBy(function ($item) {
                return $item->date->format('Y-m-d');
            })
            ->map(function ($item) {
                return [
                    'available_rooms' => $item->available_rooms - $item->booked_rooms,
                    'price' => $item->price
                ];
            });

        if ($request->ajax()) {
            return response()->json($reservationSlots);
        }

        return view('frontend.plan.show', compact('plan', 'roomType', 'reservationSlots', 'startDate', 'endDate'));
    }
}
