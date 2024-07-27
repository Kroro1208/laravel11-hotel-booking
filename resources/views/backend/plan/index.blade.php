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
                    <div class="plan-image-container">
                        @if($plan->images && count($plan->images) > 0)
                            <img src="{{ asset('storage/' . $plan->images[0]) }}" class="card-img-top" alt="プラン画像">
                        @else
                            <img src="{{ asset('images/no-image.jpg') }}" class="card-img-top" alt="画像なし">
                        @endif
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $plan->title }}</h5>
                        <p class="card-text">{{ Str::limit($plan->description, 100) }}</p>
                        <div class="mt-2">
                            <strong>適用部屋タイプ：</strong><br>
                            @foreach($plan->roomTypes as $roomType)
                                <span class="badge bg-secondary room-type-badge">{{ $roomType->name }}</span>
                            @endforeach
                        </div>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <i class="bi bi-calendar-range"></i> 
                            <strong>期間：</strong> {{ $plan->start_date->format('Y/m/d') }} 〜 {{ $plan->end_date->format('Y/m/d') }}
                        </li>
                    </ul>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('plan.show', $plan) }}" class="btn btn-sm btn-outline-secondary">
                                詳細
                            </a>
                            <div>
                                <a href="{{ route('plan.edit', $plan) }}" class="btn btn-sm btn-outline-primary">
                                    編集
                                </a>
                                <form method="POST" action="{{ route('plan.destroy', $plan) }}" class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        削除
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $plans->links() }}
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<style>
    .plan-image-container {
        position: relative;
        padding-bottom: 56.25%; /* 16:9 アスペクト比 */
        height: 0;
        overflow: hidden;
    }
    .plan-image-container img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .room-type-badge {
        font-size: 0.8rem;
        margin-right: 0.3rem;
        margin-bottom: 0.3rem;
    }
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