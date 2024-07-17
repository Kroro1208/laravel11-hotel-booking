<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(): View
    {
        $plans = Plan::all();

        return view('frontend.plan.index', [
            'plans' => $plans,
        ]);
    }

    public function show(Plan $plan): View
    {
        return view('frontend.plan.show', [
            'plan' => $plan,
        ]);
    }
}
