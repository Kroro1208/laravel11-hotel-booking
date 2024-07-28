@extends('frontend.main')

@section('content')
<div class="container py-5">
    <h2 class="mb-4 text-center">{{ $plan->title }}</h2>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                @if($plan->images && count($plan->images) > 0)
                    <img src="{{ asset('storage/' . $plan->images[0]) }}" class="card-img-top" alt="プラン画像">
                @else
                    <img src="{{ asset('images/no-image.jpg') }}" class="card-img-top" alt="画像なし">
                @endif
                <div class="card-body">
                    <h4 class="card-title mb-3">プラン詳細</h4>
                    <p class="card-text">{{ $plan->description }}</p>

                    <h5 class="mt-4">適用期間</h5>
                    <p class="card-text">
                        <i class="flaticon-calendar me-2"></i>
                        {{ $plan->start_date->format('Y年m月d日') }} 〜 {{ $plan->end_date->format('Y年m月d日') }}
                    </p>

                    <h5 class="mt-4">適用部屋タイプ</h5>
                    <form action="{{ route('plan.show', $plan) }}" method="GET" id="roomTypeForm">
                        <select name="room_type_id" class="form-select mb-3" id="roomTypeSelect">
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

        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-body">
                    <h4 class="card-title mb-3">予約カレンダー</h4>
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/main.min.css' rel='stylesheet' />
<style>
    #calendar {
        height: 600px;
    }
</style>
@endpush

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar-scheduler@6.1.15/index.global.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,resourceTimelineMonth'
        },
        resources: [
            @foreach($plan->roomTypes as $roomType)
                { id: '{{ $roomType->id }}', title: '{{ $roomType->name }}' },
            @endforeach
        ],
        events: [
            // ここにイベントデータを追加します。例:
            // { resourceId: '1', title: '予約済み', start: '2023-07-01', end: '2023-07-03' },
        ]
    });
    calendar.render();

    // 部屋タイプ選択の変更イベント
    document.getElementById('roomTypeSelect').addEventListener('change', function() {
        document.getElementById('roomTypeForm').submit();
    });
});
</script>
@endpush