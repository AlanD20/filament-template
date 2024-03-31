@props([
    'columns' => [],
    'rows' => [],
])

<table>
    <thead>
        <tr>
            @foreach ($columns as $column)
                <th>
                    {{ $column->getLabel() }}
                </th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $row)
            @php
                $isSummaryRow = false;
            @endphp
            <tr>
                @foreach ($columns as $column)
                    @php
                        $isSummaryCell = $row[$column->getName()] === __('summary');

                        if ($isSummaryCell) {
                            $isSummaryRow = true;
                        }
                    @endphp
                    <td
                        {{ $attributes->class([
                            'td-field-summary' => $isSummaryCell || $isSummaryRow,
                        ]) }}>
                        {!! translate_column_value($column, $row) !!}
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
