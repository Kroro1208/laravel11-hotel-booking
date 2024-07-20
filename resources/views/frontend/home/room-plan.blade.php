
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">ホテル予約プラン一覧</h2>
    </div>

    <div class="row row-cols-1 row-cols-md-3 row-cols-xl-4 g-3">
        @foreach($plans as $plan)
            <div class="col">
                <div class="card h-100 shadow-sm">
                    <div class="position-relative">
                        <img src="{{ asset('storage/' . $plan->image) }}" class="card-img-top" alt="プラン画像" style="height: 150px; object-fit: cover;">
                        <div class="position-absolute top-0 end-0 m-2">
                            <span class="badge {{ $plan->is_reserved ? 'bg-danger' : 'bg-success' }} fs-6">
                                {{ $plan->is_reserved ? '満室' : '予約可能' }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <h5 class="card-title fs-6">{{ Str::limit($plan->title, 30) }}</h5>
                        <p class="card-text small">{{ Str::limit($plan->description, 50) }}</p>
                        <h6 class="card-subtitle mb-2 text-primary">¥{{ number_format($plan->price) }} / 泊</h6>
                    </div>
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item py-2"><i class="bi bi-calendar-range"></i> {{ $plan->start_date->format('Y/m/d') }} 〜 {{ $plan->end_date->format('Y/m/d') }}</li>
                        <li class="list-group-item py-2"><i class="bi bi-door-open"></i> 残り{{ $plan->planRooms->sum('room_count') }}枠</li>
                    </ul>
                    <div class="card-body p-3">
                        <h6 class="card-subtitle mb-2 text-muted small">利用可能な部屋タイプ</h6>
                        <ul class="list-unstyled mb-3 small">
                            @foreach($plan->planRooms as $planRoom)
                                <li class="d-flex justify-content-between align-items-center">
                                    {{ Str::limit($planRoom->roomType->name, 15) }}
                                    <span class="badge bg-primary rounded-pill">{{ $planRoom->room_count }}室</span>
                                </li>
                            @endforeach
                        </ul>
                        <div class="d-flex justify-content-center align-items-center">
                            <a href="{{ route('plan.show', $plan->id) }}" class="btn btn-sm btn-outline-secondary w-100">
                                <i class="bi bi-eye"></i> 詳細
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="d-flex justify-content-center mt-4">
        {{ $plans->links() }}
    </div>
</div>