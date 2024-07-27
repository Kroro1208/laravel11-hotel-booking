<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlanStoreRequest;
use App\Http\Requests\PlanUpdateRequest;
use App\Models\Plan;
use App\Models\ReservationSlot;
use App\Models\RoomType;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Exception;
use Illuminate\Support\Facades\Log;

class PlanController extends Controller
{
    public function index(): View
    {
        $plans = Plan::with('roomTypes')->paginate(10);
        return view('backend.plan.index', compact('plans'));
    }

    public function create(): View
    {
        $roomTypes = RoomType::all();
        return view('backend.plan.create', compact('roomTypes'));
    }

    public function store(PlanStoreRequest $request): RedirectResponse
    {
        $maxRetries = 3;
        $retryCount = 0;

        while ($retryCount < $maxRetries) {
            DB::beginTransaction();

            try {
                $plan = new Plan($request->validated());

                // 画像の保存
                if ($request->hasFile('images')) {
                    $images = [];
                    foreach ($request->file('images') as $image) {
                        $path = $image->store('plan-images', 'public');
                        $images[] = $path;
                    }
                    $plan->images = $images;
                }

                $plan->save();

                // 部屋タイプの関連付け
                foreach ($request->input('room_types') as $roomTypeId) {
                    $plan->roomTypes()->attach($roomTypeId, ['room_count' => $request->input('room_count')[$roomTypeId] ?? 1]);
                }

                DB::commit();
                Log::info('プランが正常に作成されました。', ['plan_id' => $plan->id]);
                return redirect()->route('plan.index')->with('success', 'プランが正常に作成されました。');
            } catch (QueryException $e) {
                DB::rollBack();
                Log::error('データベースエラーが発生しました。', ['error' => $e->getMessage(), 'retry_count' => $retryCount]);

                if ($retryCount < $maxRetries - 1) {
                    $retryCount++;
                    continue;
                }

                return back()->with('error', 'データベースエラーが発生しました。しばらくしてからもう一度お試しください。')->withInput();
            } catch (Exception $e) {
                DB::rollBack();
                Log::error('予期せぬエラーが発生しました。', ['error' => $e->getMessage()]);
                return back()->with('error', '予期せぬエラーが発生しました。管理者にお問い合わせください。')->withInput();
            }
        }
        return back()->with('error', '処理が完了できませんでした。しばらくしてからもう一度お試しください。')->withInput();
    }

    public function show(Plan $plan)
    {
        $plan->load('roomTypes');

        $reservationSlots = ReservationSlot::whereIn('room_type_id', $plan->roomTypes->pluck('id'))
            ->where('date', '>=', $plan->start_date)
            ->where('date', '<=', $plan->end_date)
            ->orderBy('date')
            ->get()
            ->groupBy('room_type_id');

        return view('backend.plan.show', compact('plan', 'reservationSlots'));
    }

    public function edit(Plan $plan): View
    {
        $roomTypes = RoomType::all();
        $plan->load('roomTypes');
        return view('backend.plan.edit', compact('plan', 'roomTypes'));
    }

    public function update(PlanUpdateRequest $request, Plan $plan): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $plan->fill($request->validated());

            // 画像の更新
            if ($request->hasFile('images')) {
                // 古い画像の削除
                foreach ($plan->images as $oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }

                $images = [];
                foreach ($request->file('images') as $image) {
                    $path = $image->store('plan-images', 'public');
                    $images[] = $path;
                }
                $plan->images = $images;
            }

            $plan->save();

            // 部屋タイプの関連付けを更新
            $plan->roomTypes()->detach();
            foreach ($request->input('room_types') as $roomTypeId) {
                $plan->roomTypes()->attach($roomTypeId, ['room_count' => $request->input('room_count')[$roomTypeId]]);
            }

            DB::commit();

            return redirect()->route('plan.index')->with('success', 'プランが正常に更新されました。');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'プランの更新に失敗しました。' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        DB::beginTransaction();

        try {
            // 画像の削除
            foreach ($plan->images as $image) {
                Storage::disk('public')->delete($image);
            }

            // プランの削除
            $plan->delete();

            DB::commit();

            return redirect()->route('plan.index')->with('success', 'プランが正常に削除されました。');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'プランの削除に失敗しました。' . $e->getMessage());
        }
    }
}
