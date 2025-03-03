<div class="room-availability" data-room-type="{{ $planRoom->roomType->id }}">
    @if($slot && $slot->first())
        @php
            $slotData = $slot->first();
        @endphp
        <button type="button"
                class="btn btn-sm btn-{{ $slotData->status === 'available' ? 'success' : ($slotData->status === 'few' ? 'warning' : 'danger') }} mt-1"
                onclick="showReservationModal('{{ $plan->id }}', '{{ $day['date']->format('Y-m-d') }}', '{{ $planRoom->roomType->id }}', '{{ $slotData->getAvailableRooms() }}', '{{ $plan->price }}')">
                {{ $statusOptions[$slotData->status] }}
                ({{ $slotData->getAvailableRooms() }})
                <br>
                ¥{{ number_format($plan->price) }}
        </button>
    @else
        <span class="badge bg-secondary mt-1">-</span>
    @endif
</div>

<!-- モーダルの追加 -->
<div class="modal fade" id="reservationModal" tabindex="-1" aria-labelledby="reservationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reservationModalLabel">予約確認</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>選択された日付: <span id="selectedDate"></span></p>
                <p>部屋タイプ: <span id="selectedRoomType"></span></p>
                <p>空室数: <span id="availableRooms"></span></p>
                <p>料金: ¥<span id="roomPrice"></span></p>
                <form id="reservationForm" action="{{ route('user.reservation.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="plan_id" id="planId">
                    <input type="hidden" name="date" id="reservationDate">
                    <input type="hidden" name="room_type_id" id="roomTypeId">
                    <div class="mb-3">
                        <label for="roomCount" class="form-label">予約する部屋数:</label>
                        <input type="number" class="form-control" id="roomCount" name="room_count" min="1" value="1">
                    </div>
                    <div class="mb-3">
                        <label for="guestName" class="form-label">お名前:</label>
                        <input type="text" class="form-control" id="guestName" name="guest_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="guestEmail" class="form-label">メールアドレス:</label>
                        <input type="email" class="form-control" id="guestEmail" name="guest_email" required>
                    </div>
                    <div class="mb-3">
                        <label for="numberOfGuests" class="form-label">宿泊人数:</label>
                        <input type="number" class="form-control" id="numberOfGuests" name="number_of_guests" min="1" value="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">メッセージ（任意）:</label>
                        <textarea class="form-control" id="message" name="message" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                @auth
                    <button type="button" class="btn btn-primary" onclick="submitReservation()">予約する</button>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">予約する（ログインが必要です）</a>
                @endauth
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showReservationModal(planId, date, roomTypeId, availableRooms, price) {
        document.getElementById('planId').value = planId;
        document.getElementById('reservationDate').value = date;
        document.getElementById('roomTypeId').value = roomTypeId;
        document.getElementById('selectedDate').textContent = date;
        document.getElementById('selectedRoomType').textContent = document.querySelector(`option[value="${roomTypeId}"]`).textContent;
        document.getElementById('availableRooms').textContent = availableRooms;
        document.getElementById('roomPrice').textContent = new Intl.NumberFormat('ja-JP').format(price);
        document.getElementById('roomCount').max = availableRooms;

        let modal = new bootstrap.Modal(document.getElementById('reservationModal'));
        modal.show();
    }

    function submitReservation() {
        document.getElementById('reservationForm').submit();
    }
</script>
@endpush