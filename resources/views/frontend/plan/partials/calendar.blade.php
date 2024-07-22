<div id="availability-calendar">
    @foreach($months as $year => $yearMonths)
        @foreach($yearMonths as $month)
            <h4 class="mt-4">{{ $year }}年{{ $month }}月</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>日</th>
                            <th>月</th>
                            <th>火</th>
                            <th>水</th>
                            <th>木</th>
                            <th>金</th>
                            <th>土</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($calendar[$year][$month] as $week)
                            <tr>
                                @foreach($week as $day)
                                    @include('frontend.plan.partials.calendar_cell', ['day' => $day, 'plan' => $plan, 'reservationSlots' => $reservationSlots])
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    @endforeach
</div>