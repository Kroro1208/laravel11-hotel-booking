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
    <div class="mb-5">
        <a href="{{ route('plan.index') }}" class="btn btn-secondary">プラン一覧に戻る</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('reservationSlot.update', $plan->id) }}" method="POST">
        @csrf
        @method('PATCH')
        @foreach($reservationSlots as $roomTypeId => $slots)
            <h3>{{ $slots->first()->roomType->name }}</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>日付</th>
                        <th>総部屋数</th>
                        <th>予約済み</th>
                        <th>残り</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($slots as $slot)
                        <tr>
                            <td>{{ $slot->date->format('Y/m/d') }}</td>
                            <td>
                                <input type="number" name="slots[{{ $slot->id }}][total_rooms]"
                                    value="{{ $slot->total_rooms }}" min="{{ $slot->booked_rooms }}" class="form-control">
                            </td>
                            <td>{{ $slot->booked_rooms }}</td>
                            <td>{{ $slot->total_rooms - $slot->booked_rooms }}</td>
                            <td>
                                <button type="submit" name="update_slot" value="{{ $slot->id }}" class="btn btn-primary btn-sm">更新</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    </form>
</div>
@endsection