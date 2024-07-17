<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlanStoreRequest;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(): View
    {
        // 全てのプランを取得してビューに渡す
        $plans = Plan::all();
        return view('backend.plan.index', [
            'plans' => $plans
        ]);
    }

    public function create(): View
    {
        return view('backend.plan.create');
    }

    public function store(PlanStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $plan = new Plan();
        // 画像を保存
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('plans', 'public');
            $validated['image'] = $imagePath;
        }
        $plan->fill($validated);
        $plan->save();

        return to_route('plan.index')->with('success', 'プランの作成に成功しました');
    }

   
}
