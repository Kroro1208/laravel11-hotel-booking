@extends('admin.dashboard')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">ホテル予約プラン一覧</h2>
        <a href="{{ route('plan.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> 新規プラン作成
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
        @foreach($plans as $plan)
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="position-relative">
                        <img src="{{ asset('storage/' . $plan->image) }}" class="card-img-top" alt="プラン画像" style="height: 200px; object-fit: cover;">
                        <div class="position-absolute top-0 end-0 m-2">
                            <span class="badge {{ $plan->is_reserved ? 'bg-danger' : 'bg-success' }} fs-6">
                                {{ $plan->is_reserved ? '満室' : '予約可能' }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $plan->title }}</h5>
                        <p class="card-text">{{ Str::limit($plan->description, 100) }}</p>
                        <h6 class="card-subtitle mb-2 text-primary">¥{{ number_format($plan->price) }} / 泊</h6>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><i class="bi bi-calendar-range"></i> <strong>期間：</strong> {{ $plan->start_date->format('Y/m/d') }} 〜 {{ $plan->end_date->format('Y/m/d') }}</li>
                        <li class="list-group-item"><i class="bi bi-door-open"></i> <strong>総空き枠：</strong> 残り{{ $plan->planRooms->sum('room_count') }}枠</li>
                    </ul>
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">利用可能な部屋タイプ</h6>
                        <ul class="list-group list-group-flush mb-3">
                            @foreach($plan->planRooms as $planRoom)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $planRoom->roomType->name }}
                                    <span class="badge bg-primary rounded-pill">{{ $planRoom->room_count }}室</span>
                                </li>
                            @endforeach
                        </ul>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('plan.show', $plan) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-eye"></i> 詳細
                            </a>
                            <div>
                                <a href="{{ route('plan.edit', $plan) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i> 編集
                                </a>
                                <form method="POST" action="{{ route('plan.destroy', $plan) }}" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i> 削除
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<style>
    .card {
        transition: transform 0.2s;
    }
    .card:hover {
        transform: translateY(-5px);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteForms = document.querySelectorAll('.delete-form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if(confirm('本当にこのプランを削除しますか？この操作は取り消せません。')) {
                    this.submit();
                }
            });
        });
    });
</script>
@endpush