@extends('admin.dashboard')

@section('content')
<div class="container py-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ $plan->title }}</h2>
            <a href="{{ route('plan.index') }}" class="btn btn-light">戻る</a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-4">
                        <h4 class="text-primary">詳細</h4>
                        <p class="text-muted">{{ $plan->description }}</p>
                    </div>
                    <div class="mb-4">
                        <h4 class="text-primary">期間</h4>
                        <p class="text-muted">{{ $plan->start_date->format('Y/m/d') }} 〜 {{ $plan->end_date->format('Y/m/d') }}</p>
                    </div>
                    <div>
                        <h4 class="text-primary mb-3">部屋タイプと予約枠</h4>
                        @foreach($plan->roomTypes as $roomType)
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">{{ $roomType->name }} ({{ $roomType->pivot->room_count }}部屋)</h5>
                                </div>
                                <div class="card-body">
                                    @if(isset($reservationSlots[$roomType->id]))
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>日付</th>
                                                        <th>利用可能な部屋数</th>
                                                        <th>料金 (1泊)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($reservationSlots[$roomType->id] as $slot)
                                                        <tr>
                                                            <td>{{ \Carbon\Carbon::parse($slot->date)->format('Y/m/d') }}</td>
                                                            <td>{{ $slot->available_rooms }}</td>
                                                            <td class="text-end">{{ number_format($slot->price) }}円</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted fst-italic">この部屋タイプの予約枠情報はありません。</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-6">
                    <h4 class="text-primary mb-3">画像</h4>
                    <div id="planCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @foreach($plan->images as $index => $image)
                                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                    <img src="{{ asset('storage/' . $image) }}" class="d-block w-100 rounded" alt="プラン画像">
                                </div>
                            @endforeach
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#planCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#planCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('plan.edit', $plan) }}" class="btn btn-primary">編集</a>
        </div>
    </div>
</div>
@endsection