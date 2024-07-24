@extends('admin.dashboard')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4 mb-4">予約枠の編集</h1>

    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('reservationSlot.update', $reservationSlot) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="mb-3">
                    <label for="room_type_id" class="form-label">部屋タイプ</label>
                    <select name="room_type_id" id="room_type_id" class="form-select" required>
                        @foreach($roomTypes as $roomType)
                            <option value="{{ $roomType->id }}" {{ $reservationSlot->room_type_id == $roomType->id ? 'selected' : '' }}>
                                {{ $roomType->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="date" class="form-label">日付</label>
                    <input type="date" name="date" id="date" class="form-control"
                        value="{{ $reservationSlot->date->format('Y-m-d') }}" required>
                </div>

                <div class="mb-3">
                    <label for="available_rooms" class="form-label">利用可能な部屋数</label>
                    <input type="number" name="available_rooms" id="available_rooms" class="form-control"
                        value="{{ $reservationSlot->available_rooms }}" min="0" required>
                </div>

                <div class="mb-3">
                    <label for="booked_rooms" class="form-label">予約済み部屋数</label>
                    <input type="number" name="booked_rooms" id="booked_rooms" class="form-control"
                        value="{{ $reservationSlot->booked_rooms }}" min="0" required>
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">料金</label>
                    <div class="input-group">
                        <span class="input-group-text">¥</span>
                        <input type="number" name="price" id="price" class="form-control"
                            value="{{ $reservationSlot->price }}" step="0.01" min="0" required>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('reservationSlot.index') }}" class="btn btn-secondary me-md-2">キャンセル</a>
                    <button type="submit" class="btn btn-primary">更新</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const availableRoomsInput = document.getElementById('available_rooms');
        const bookedRoomsInput = document.getElementById('booked_rooms');

        function validateRooms() {
            const available = parseInt(availableRoomsInput.value) || 0;
            const booked = parseInt(bookedRoomsInput.value) || 0;
            if (booked > available) {
                alert('予約済み部屋数は利用可能な部屋数を超えることはできません。');
                bookedRoomsInput.value = available;
            }
        }

        availableRoomsInput.addEventListener('change', validateRooms);
        bookedRoomsInput.addEventListener('change', validateRooms);
    });
</script>
@endpush