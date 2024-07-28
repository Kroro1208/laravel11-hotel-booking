@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h2 class="mb-4 text-center">宿泊プラン一覧</h2>

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
        @foreach($plans as $plan)
            <div class="col">
                <div class="card h-100 shadow-sm hover-card">
                    <div class="position-relative">
                        @if($plan->images && count($plan->images) > 0)
                            <img src="{{ asset('storage/' . $plan->images[0]) }}" class="card-img-top" alt="プラン画像">
                        @else
                            <img src="{{ asset('images/no-image.jpg') }}" class="card-img-top" alt="画像なし">
                        @endif
                        <div class="card-img-overlay d-flex flex-column justify-content-end">
                            <h5 class="card-title bg-dark bg-opacity-75 text-white p-2 rounded">{{ $plan->title }}</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="card-text">{{ Str::limit($plan->description, 100) }}</p>
                        <div class="mt-2">
                            <strong>適用部屋タイプ：</strong><br>
                            @foreach($plan->roomTypes as $roomType)
                                <span class="badge bg-secondary me-1">{{ $roomType->name }}</span>
                            @endforeach
                        </div>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <i class="bi bi-calendar-range"></i>
                            <strong>期間：</strong> {{ $plan->start_date->format('Y/m/d') }} 〜 {{ $plan->end_date->format('Y/m/d') }}
                        </li>
                        <li class="list-group-item">
                            <i class="bi bi-currency-yen"></i>
                            <strong>料金：</strong> <span class="text-primary">{{ number_format($plan->price) }}円〜</span>
                        </li>
                    </ul>
                    <div class="card-footer bg-transparent border-top-0">
                        <a href="{{ route('plan.show', $plan) }}" class="btn btn-outline-primary w-100">詳細を見る</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $plans->links() }}
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<style>
    .hover-card {
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    }
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    .card-img-overlay {
        background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0) 100%);
    }
</style>
@endpush