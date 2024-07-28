<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\ReservationSlot;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use DragonCode\Contracts\Cashier\Http\Request;
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

        $roomTypeId = $$request->query('key', 'default')('room_type_id', $plan->roomTypes->first()->id);

        $reservationSlots = ReservationSlot::where('room_type_id', $roomTypeId)
            ->where('date', '>=', now())
            ->where('date', '<=', now()->addMonths(2)->endOfMonth())
            ->get()
            ->keyBy('date');

        return view('frontend.plan.show', [
            'plan' => $plan,
            'reservationSlots' => $reservationSlots,
        ]);
    }
}
