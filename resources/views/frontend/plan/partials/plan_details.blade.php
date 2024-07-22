<div class="card mb-4">
    <div class="row g-0">
        <div class="col-md-4">
            <img src="{{ asset('storage/' . $plan->image) }}" class="img-fluid rounded-start" alt="プラン画像">
        </div>
        <div class="col-md-8">
            <div class="card-body">
                <p class="card-text">{{ $plan->description }}</p>
                <h4 class="card-title text-primary">¥{{ number_format($plan->price) }} / 泊</h4>
                <p class="card-text"><strong>期間：</strong> {{ $plan->start_date->format('Y/m/d') }} 〜 {{ $plan->end_date->format('Y/m/d') }}</p>
                <h5 class="mt-3">利用可能な部屋タイプ</h5>
                <ul class="list-group list-group-flush">
                    @foreach($plan->planRooms as $planRoom)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $planRoom->roomType->name }}
                            <span class="badge bg-primary rounded-pill">{{ $planRoom->room_count }}室</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>