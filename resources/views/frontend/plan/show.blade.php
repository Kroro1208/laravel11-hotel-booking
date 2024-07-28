@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <h2 class="mb-4 text-center">{{ $plan->title }}</h2>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    @if($plan->images && count($plan->images) > 0)
                        <img src="{{ asset('storage/' . $plan->images[0]) }}" class="card-img-top" alt="プラン画像">
                    @else
                        <img src="{{ asset('images/no-image.jpg') }}" class="card-img-top" alt="画像なし">
                    @endif
                    <div class="card-body">
                        <h4 class="card-title">プラン詳細</h4>
                        <p class="card-text">{{ $plan->description }}</p>

                        <h5 class="mt-4">適用期間</h5>
                        <p class="card-text">
                            <i class="bi bi-calendar-range"></i>
                            {{ $plan->start_date->format('Y年m月d日') }} 〜 {{ $plan->end_date->format('Y年m月d日') }}
                        </p>

                        <h5 class="mt-4">適用部屋タイプ</h5>
                        <form action="{{ route('plans.show', $plan) }}" method="GET" id="roomTypeForm">
                            <select name="room_type_id" class="form-select mb-3" onchange="this.form.submit()">
                                @foreach($plan->roomTypes as $roomType)
                                    <option value="{{ $roomType->id }}" {{ request()->query('room_type_id') == $roomType->id ? 'selected' : '' }}>
                                        {{ $roomType->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title">予約カレンダー</h4>
                        @php
                            $currentDate = now()->startOfMonth();
                            $endDate = $currentDate->copy()->addMonths(2)->endOfMonth();
                        @endphp

                        @while($currentDate <= $endDate)
                            <h5 class="mt-4">{{ $currentDate->format('Y年n月') }}</h5>
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        @foreach(['日', '月', '火', '水', '木', '金', '土'] as $dayOfWeek)
                                            <th class="text-center">{{ $dayOfWeek }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @while($currentDate->month == $currentDate->copy()->endOfMonth()->month)
                                        <tr>
                                            @for($i = 0; $i < 7; $i++)
                                                @if($currentDate->dayOfWeek == $i && $currentDate->month == $currentDate->copy()->endOfMonth()->month)
                                                    <td class="text-center">
                                                        {{ $currentDate->day }}
                                                        @if($reservationSlots->has($currentDate->format('Y-m-d')))
                                                            <br>
                                                            <small class="text-primary">{{ number_format($reservationSlots[$currentDate->format('Y-m-d')]->price) }}円</small>
                                                        @endif
                                                    </td>
                                                    @php $currentDate->addDay() @endphp
                                                @else
                                                    <td></td>
                                                @endif
                                            @endfor
                                        </tr>
                                    @endwhile
                                </tbody>
                            </table>
                        @endwhile
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .table th, .table td {
            padding: 0.5rem;
        }
        .card {
            transition: box-shadow 0.3s ease-in-out;
        }
        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
    </style>
@endpush