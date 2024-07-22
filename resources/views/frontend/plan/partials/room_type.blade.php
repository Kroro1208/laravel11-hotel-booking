<div class="mb-4">
    <label for="room-type" class="form-label">部屋タイプを選択:</label>
    <select id="room-type" class="form-select" onchange="updateCalendar(this.value)">
        @foreach($plan->planRooms as $planRoom)
            <option value="{{ $planRoom->roomType->id }}">{{ $planRoom->roomType->name }}</option>
        @endforeach
    </select>
</div>