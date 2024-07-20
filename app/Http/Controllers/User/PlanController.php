<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function show(Plan $plan): View
    {
        return view('frontend.plan.show', [
            'plan' => $plan,
        ]);
    }
}
