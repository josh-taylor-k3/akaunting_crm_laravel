<tfoot>
    <tr>
        <th class="long-texts report-column">{{ trans_choice('general.totals', 1) }}</th>
        @php $total_total = 0; @endphp
        @foreach($class->totals[$table] as $total)
            @php $total_total += $total; @endphp
            <th class="long-texts report-column text-right">@money($total, setting('default.currency'), true)</th>
        @endforeach
        <th class="long-texts report-column text-right">@money($total_total, setting('default.currency'), true)</th>
    </tr>
</tfoot>
