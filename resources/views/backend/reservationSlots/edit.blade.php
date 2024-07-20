@extends('admin.dashboard')

@section('content')
<div class="container">
    <h1>{{ $plan->title }} の予約枠管理</h1>
    
    <form action="{{ route('reservation_slots.update', $plan->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        @foreach($plan->roomTypes as $roomType)
            <h2>{{ $roomType->name }}</h2>
            @foreach($plan->dateRange() as $date)
                @php
                    $slot = $reservationSlots->firstWhere(function($slot) use ($date, $roomType) {
                        return $slot->date->eq($date) && $slot->room_type_id == $roomType->id;
                    });
                @endphp
                <div class="form-group">
                    <label for="slot_{{ $date->format('Y-m-d') }}_{{ $roomType->id }}">{{ $date->format('Y-m-d') }}</label>
                    <input type="number" 
                           name="slots[{{ $date->format('Y-m-d') }}][{{ $roomType->id }}][total_rooms]" 
                           id="slot_{{ $date->format('Y-m-d') }}_{{ $roomType->id }}"
                           value="{{ old('slots.'.$date->format('Y-m-d').'.'.$roomType->id.'.total_rooms', $slot ? $slot->total_rooms : 0) }}"
                           class="form-control">
                    <input type="hidden" name="slots[{{ $date->format('Y-m-d') }}][{{ $roomType->id }}][date]" value="{{ $date->format('Y-m-d') }}">
                    <input type="hidden" name="slots[{{ $date->format('Y-m-d') }}][{{ $roomType->id }}][room_type_id]" value="{{ $roomType->id }}">
                </div>
            @endforeach
        @endforeach
        
        <button type="submit" class="btn btn-primary">予約枠を更新</button>
    </form>
</div>
@endsection