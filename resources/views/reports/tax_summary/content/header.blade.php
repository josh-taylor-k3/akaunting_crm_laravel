<div class="table-responsive overflow-auto">
    <table class="table align-items-center">
        <thead class="border-top-style">
            <tr>
                <th class="report-column text-right"></th>
                @foreach($class->dates as $date)
                    <th class="report-column text-right px-0">{{ $date }}</th>
                @endforeach
                <th class="report-column text-right">
                    <h5>{{ trans_choice('general.totals', 1) }}</h5>
                </th>
            </tr>
        </thead>
    </table>
</div>
