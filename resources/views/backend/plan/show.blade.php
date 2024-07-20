// 1. PlanController に show メソッドを追加
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;

class PlanController extends Controller
{
    // ... 他のメソッド ...

    public function show(Plan $plan)
    {
        // プランに関連する予約枠情報を取得
        $reservationSlots = $plan->reservationSlots()
            ->with('roomType')
            ->orderBy('date')
            ->get()
            ->groupBy('room_type_id');

        return view('admin.plans.show', compact('plan', 'reservationSlots'));
    }
}

// 2. ルートの追加 (routes/web.php)
Route::get('/plans/{plan}', [PlanController::class, 'show'])->name('plan.show');

// 3. プラン詳細ページのビュー (resources/views/admin/plans/show.blade.php)
@extends('admin.dashboard')

@section('content')
<div class="container">
    <h1>{{ $plan->title }}</h1>
    
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">プラン詳細</h5>
            <p><strong>説明:</strong> {{ $plan->description }}</p>
            <p><strong>価格:</strong> {{ number_format($plan->price) }}円</p>
            <p><strong>期間:</strong> {{ $plan->start_date->format('Y/m/d') }} 〜 {{ $plan->end_date->format('Y/m/d') }}</p>
        </div>
    </div>

    <h2>予約枠情報</h2>
    @foreach($reservationSlots as $roomTypeId => $slots)
        <h3>{{ $slots->first()->roomType->name }}</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>日付</th>
                    <th>総部屋数</th>
                    <th>予約済み</th>
                    <th>残り</th>
                </tr>
            </thead>
            <tbody>
                @foreach($slots as $slot)
                    <tr>
                        <td>{{ $slot->date->format('Y/m/d') }}</td>
                        <td>{{ $slot->total_rooms }}</td>
                        <td>{{ $slot->booked_rooms }}</td>
                        <td>{{ $slot->total_rooms - $slot->booked_rooms }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    <a href="{{ route('reservation_slots.edit', $plan->id) }}" class="btn btn-primary">予約枠管理</a>
    <a href="{{ route('plan.index') }}" class="btn btn-secondary">プラン一覧に戻る</a>
</div>
@endsection