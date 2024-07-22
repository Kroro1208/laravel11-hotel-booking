@extends('frontend.main')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">{{ $plan->title }}</h2>
    @include('frontend.plan.partials.plan_details')
    @include('frontend.plan.partials.room_type')
    @include('frontend.plan.partials.calendar')
</div>

@push('scripts')
<script>
function updateCalendar(roomTypeId) {
    document.querySelectorAll('.room-availability').forEach(function(el) {
        el.style.display = el.dataset.roomType === roomTypeId ? 'block' : 'none';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    updateCalendar(document.getElementById('room-type').value);
});
</script>
@endpush

@endsection