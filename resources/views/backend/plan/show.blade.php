@extends('admin.dashboard')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h2 class="mb-0">{{ $plan->title }}</h2>
            <a href="{{ route('plan.index') }}" class="btn btn-light">戻る</a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h4>詳細</h4>
                    <p>{{ $plan->description }}</p>
                    <h4>期間</h4>
                    <p>{{ $plan->start_date->format('Y/m/d') }} 〜 {{ $plan->end_date->format('Y/m/d') }}</p>
                    <h4>部屋タイプ</h4>
                    <ul>
                        @foreach($plan->roomTypes as $roomType)
                            <li>{{ $roomType->name }} ({{ $roomType->pivot->room_count }}部屋)</li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-md-6">
                    <h4>画像</h4>
                    <div id="planCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @foreach($plan->images as $index => $image)
                                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                    <img src="{{ asset('storage/' . $image) }}" class="d-block w-100" alt="プラン画像">
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
        <div class="card-footer">
            <a href="{{ route('plan.edit', $plan) }}" class="btn btn-primary">編集</a>
            <form action="{{ route('plan.destroy', $plan) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('本当にこのプランを削除しますか？この操作は取り消せません。')">
                    削除
                </button>
            </form>
        </div>
    </div>
</div>
@endsection