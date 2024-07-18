<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlanStoreRequest;
use App\Models\Plan;
use App\Models\RoomType;
use App\Models\PlanRoom;
use App\Models\ReservationSlot;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(): View
    {
        $plans = Plan::with(['rooms.roomType', 'reservations'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('plans.index', [
            'plans' => $plans
        ]);
    }

    public function create(): View
    {
        return view('backend.plan.create');
    }

    public function store(PlanStoreRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                // Planの保存
                $plan = new Plan();
                $plan->title = $request->title;
                $plan->price = $request->price;
                $plan->description = $request->description;
                $plan->start_date = $request->start_date;
                $plan->end_date = $request->end_date;

                // 画像の保存
                if ($request->hasFile('image')) {
                    $imagePath = $request->file('image')->store('plan_images', 'public');
                    $plan->image = $imagePath;
                }
                $plan->save();

                // PlanRoomとReservationSlotの作成
                foreach ($request->room_types as $index => $roomType) {
                    $roomCount = $request->room_counts[$index];

                    // PlanRoomの作成
                    $planRoom = new PlanRoom();
                    $planRoom->plan_id = $plan->id;
                    $planRoom->room_type_id = $roomType;
                    $planRoom->room_count = $roomCount;
                    $planRoom->save();

                    // ReservationSlotの作成
                    $currentDate = Carbon::parse($plan->start_date);
                    $endDate = Carbon::parse($plan->end_date);

                    while ($currentDate <= $endDate) {
                        $reservationSlot = new ReservationSlot();
                        $reservationSlot->plan_id = $plan->id;
                        $reservationSlot->room_type_id = $roomType;
                        $reservationSlot->date = $currentDate->toDateString();
                        $reservationSlot->price = $plan->price; // 基本価格を使用
                        $reservationSlot->total_rooms = $roomCount;
                        $reservationSlot->booked_rooms = 0;
                        $reservationSlot->status = 'available';
                        $reservationSlot->save();

                        $currentDate->addDay();
                    }
                }
            });

            return redirect()->route('plan.index')->with('success', 'プランが正常に作成されました。');
        } catch (\Exception $e) {
            return back()->with('error', 'プランの作成中にエラーが発生しました: ' . $e->getMessage());
        }
    }


    public function edit(Plan $plan): View
    {
        $roomTypes = RoomType::all();
        $plan->load('planRooms.roomType');
        return view('backend.plan.edit', [
            'plan' => $plan,
            'roomTypes' => $roomTypes
        ]);
    }

    public function update(PlanStoreRequest $request, Plan $plan): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request, $plan) {
                // プラン情報を更新
                $plan->fill($request->validated());

                // 画像を更新
                if ($request->hasFile('image')) {
                    $imagePath = $request->file('image')->store('plans', 'public');
                    $plan->image = $imagePath;
                }

                $plan->save();

                // planRoomを更新または作成
                $planRoom = $plan->planRooms()->firstOrNew([]);
                $planRoom->room_type_id = $request->input('room_type');
                $planRoom->room_count = $request->input('room_count');
                $planRoom->save();
            });

            return to_route('plan.index')->with('success', 'プランの更新に成功しました');
        } catch (\Exception $e) {
            return back()->with('error', 'プランの更新中にエラーが発生しました: ' . $e->getMessage());
        }
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        $plan->delete();
        return to_route('plan.index')->with('success', 'プランの削除に成功しました');
    }
}
