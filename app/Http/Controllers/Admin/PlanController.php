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
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index(): View
    {
        $plans = Plan::with(['planRooms.roomType', 'reservations'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('backend.plan.index', [
            'plans' => $plans
        ]);
    }

    public function create(): View
    {
        return view('backend.plan.create');
    }

    public function store(PlanStoreRequest $request)
    {
        DB::beginTransaction();

        try {
            Log::info('Attempting to create a new plan', $request->all());

            $plan = Plan::create([
                'image' => $request->file('image')->store('plan_images', 'public'),
                'title' => $request->title,
                'description' => $request->description,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'price' => $request->price,
                'is_reserved' => false,
            ]);

            Log::info('Plan created', ['plan_id' => $plan->id]);

            foreach ($request->room_types as $index => $roomTypeName) {
                $roomType = RoomType::firstOrCreate(
                    ['name' => $roomTypeName],
                    ['description' => '']
                );

                Log::info('Room type processed', ['room_type' => $roomTypeName, 'room_type_id' => $roomType->id]);

                PlanRoom::create([
                    'plan_id' => $plan->id,
                    'room_type_id' => $roomType->id,
                    'room_count' => $request->room_counts[$index],
                ]);

                Log::info('PlanRoom created', ['plan_id' => $plan->id, 'room_type_id' => $roomType->id, 'room_count' => $request->room_counts[$index]]);

                $startDate = Carbon::parse($request->start_date);
                $endDate = Carbon::parse($request->end_date);

                while ($startDate <= $endDate) {
                    ReservationSlot::create([
                        'plan_id' => $plan->id,
                        'room_type_id' => $roomType->id,
                        'date' => $startDate,
                        'total_rooms' => $request->room_counts[$index],
                        'booked_rooms' => 0,
                        'status' => 'available',
                    ]);

                    $startDate->addDay();
                }

                Log::info('ReservationSlots created for room type', ['room_type' => $roomTypeName]);
            }

            DB::commit();
            Log::info('Plan creation completed successfully');

            return to_route('plan.index')->with('success', 'プランが正常に作成されました。');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error occurred while creating plan', ['error' => $e->getMessage()]);
            return back()->withInput()->with('error', 'プランの作成中にエラーが発生しました: ' . $e->getMessage());
        }
    }
    public function updateReservationStatus($planId)
    {
        $plan = Plan::findOrFail($planId);
        $reservationSlots = ReservationSlot::where('plan_id', $planId)->get();

        $allUnavailable = true;

        foreach ($reservationSlots as $slot) {
            if ($slot->total_rooms == $slot->booked_rooms) {
                $slot->status = 'unavailable';
            } elseif ($slot->total_rooms - $slot->booked_rooms <= 3) {
                $slot->status = 'few';
                $allUnavailable = false;
            } else {
                $slot->status = 'available';
                $allUnavailable = false;
            }
            $slot->save();
        }

        if ($allUnavailable) {
            $plan->is_reserved = true;
            $plan->save();
        }

        return back()->with('success', '予約状況が更新されました。');
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
