<td class="text-center" style="width: 14.28%; height: 80px;">
    @if($day['date'])
        <div class="fw-bold">{{ $day['date']->day }}</div>
        @if($day['in_range'])
            @foreach($plan->planRooms as $planRoom)
                @php
                    $slot = $reservationSlots[$planRoom->roomType->id][$day['date']->format('Y-m-d')] ?? null;
                @endphp
                @include('frontend.plan.partials.room_availability', ['slot' => $slot, 'planRoom' => $planRoom, 'day' => $day, 'plan' => $plan])
            @endforeach
        @endif
    @endif
</td>