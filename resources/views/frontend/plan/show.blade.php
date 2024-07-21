<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $plan->title }} - プラン詳細</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.css' rel='stylesheet' />
    <style>
        body {
            background-color: #f8f9fa;
        }
        .plan-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #343a40;
        }
        .plan-image {
            height: 250px;
            object-fit: cover;
            border-radius: 15px 15px 0 0;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.15);
        }
        .list-group-item {
            border: none;
            padding: 0.5rem 0;
        }
        .badge {
            font-size: 0.9rem;
            padding: 0.4em 0.6em;
        }
        #calendar {
            height: 450px;
        }
        .info-icon {
            width: 24px;
            text-align: center;
        }
        .room-type-list {
            max-height: 150px;
            overflow-y: auto;
        }
        .fc-event-title {
            font-size: 0.8em;
            white-space: normal;
        }
        #roomTypeSelector {
            margin-bottom: 1rem;
        }
        .fc-day-today {
            background-color: inherit !important;
        }
        .fc-day-disabled {
            background-color: #f8f9fa !important;
            color: #6c757d !important;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-3">
        <div class="row g-3">
            <div class="col-lg-6">
                <div class="card h-100 p-5">
                    <img src="{{ asset('storage/' . $plan->image) }}" class="plan-image w-100" alt="{{ $plan->title }}">
                    <div class="card-body p-2">
                        <h1 class="plan-title mb-3">{{ $plan->title }}</h1>
                        <p class="card-text mb-3">{{ $plan->description }}</p>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex align-items-center">
                                <span class="info-icon me-3"><i class="bi bi-calendar-range fs-5"></i></span>
                                <span>{{ $plan->start_date->format('Y/m/d') }} 〜 {{ $plan->end_date->format('Y/m/d') }}</span>
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <span class="info-icon me-3"><i class="bi bi-currency-yen fs-5"></i></span>
                                <span class="fs-4 fw-bold text-primary">{{ number_format($plan->price) }}円 / 泊</span>
                            </li>
                            <li class="list-group-item">
                                <div class="d-flex align-items-center mb-2">
                                    <span class="info-icon me-3"><i class="bi bi-door-open fs-5"></i></span>
                                    <span>利用可能な部屋タイプ：</span>
                                </div>
                                <ul class="list-unstyled ms-4 room-type-list">
                                    @foreach($plan->planRooms as $planRoom)
                                    <li class="mb-2 d-flex justify-content-between align-items-center bg-info-subtle rounded p-2">
                                        <span>{{ $planRoom->roomType->name }}</span>
                                        <span class="badge bg-primary rounded-pill">残り{{ $planRoom->room_count }}室</span>
                                    </li>
                                    @endforeach
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4">予約状況カレンダー</h5>
                            <select id="roomTypeSelector" class="form-select mb-3">
                                <option value="all">全ての部屋タイプ</option>
                                <option value="洋室のFamily">洋室のFamily</option>
                                <option value="洋室のSingle">洋室のSingle</option>
                                <option value="洋室のDouble">洋室のDouble</option>
                                <option value="和室のFamily">和室のFamily</option>
                                <option value="和室のSingle">和室のSingle</option>
                                <option value="和室のDouble">和室のDouble</option>
                            </select>
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.10.2/main.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var roomTypeSelector = document.getElementById('roomTypeSelector');
            var calendar;

            function initCalendar(roomType = 'all') {
                if (calendar) {
                    calendar.destroy();
                }

                calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: ''
                    },
                    validRange: {
                        start: '{{ $plan->start_date }}',
                        end: '{{ $plan->end_date->addDay() }}'
                    },
                    events: function(fetchInfo, successCallback, failureCallback) {
                        fetch('{{ route("plan.availability", $plan->id) }}')
                            .then(response => response.json())
                            .then(data => {
                                var events = [];
                                Object.entries(data).forEach(([date, info]) => {
                                    var title = '';
                                    var color = 'green';

                                    if (roomType === 'all') {
                                        title = Object.entries(info.roomTypes)
                                            .map(([type, count]) => `${type}: ${count}`)
                                            .join('\n');
                                        color = info.available ? 'green' : 'red';
                                    } else {
                                        var count = info.roomTypes[roomType] || 0;
                                        title = `${roomType}: ${count}`;
                                        color = count > 0 ? 'green' : 'red';
                                    }

                                    events.push({
                                        start: date,
                                        title: title,
                                        color: color
                                    });
                                });
                                successCallback(events);
                            })
                            .catch(error => {
                                console.error('Error fetching availability data:', error);
                                failureCallback(error);
                            });
                    },
                    eventContent: function(arg) {
                        return {
                            html: `<div style="font-size: 0.8em; white-space: pre-line;">${arg.event.title}</div>`
                        };
                    },
                    dayCellDidMount: function(arg) {
                        if (arg.date < new Date('{{ $plan->start_date }}') || arg.date >= new Date('{{ $plan->end_date->addDay() }}')) {
                            arg.el.classList.add('fc-day-disabled');
                        }
                    }
                });
                calendar.render();
            }

            initCalendar();

            roomTypeSelector.addEventListener('change', function() {
                initCalendar(this.value);
            });
        });
    </script>
</body>
</html>