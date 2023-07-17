<input
    id="{{ $getStatePath() }}"
    type="hidden"
    {{ $applyStateBindingModifiers('wire:model') }}="{{ $getStatePath() }}"
>

<x-filament::modal
    id="preview-modal"
    width="7xl"
    display-classes="block"
    x-init="$wire.on('open-preview-modal-{{ $getUniqueActionId() }}', function() {
        triggerInputEvent('{{ $getStatePath() }}', '{{ $shouldRefresh() ? 'refresh' : '' }}');
        isOpen = true;
    });
    $wire.on('close-preview-modal-{{ $getUniqueActionId() }}', () => { isOpen = false; });"
    :heading="$getPreviewModalHeading()"
>
    <div class="space-y-4 preview-table-wrapper">
        <x-report.header />

        <div class="my-8 text-2xl font-nrt">
            {{ $this->getTitle() }}
        </div>

        <table
            class="my-8 preview-table"
            x-init="$wire.on('print-table-{{ $getUniqueActionId() }}', function() {
                triggerInputEvent('{{ $getStatePath() }}', 'print-{{ $getUniqueActionId() }}')
            })"
        >
            <tr class="text-center bg-[#395623] ">
                @foreach ($getAllColumns() as $column)
                    <th class="!font-bold text-center text-white">
                        {{ $column->getLabel() }}
                    </th>
                @endforeach
            </tr>
            @foreach ($getRows() as $row)
                @php
                    $isSummaryRow = false;
                @endphp
                <tr>
                    @foreach ($getAllColumns() as $column)
                        @php
                            $isSummaryCell = $row[$column->getName()] === __('summary');
                            
                            if ($isSummaryCell) {
                                $isSummaryRow = true;
                            }
                        @endphp
                        <td @class([
                            'text-center',
                            'td-field-summary' => $isSummaryCell || $isSummaryRow,
                        ])>
                            {!! translate_column_value($column, $row) !!}
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </table>

        <x-report.footer />

        @if ($getExport()->getPaginator() !== null)
            <div>
                <x-tables::pagination
                    :paginator="$getRows()"
                    :records-per-page-select-options="$this->getTable()->getRecordsPerPageSelectOptions()"
                />
            </div>
        @endif
    </div>
    <x-slot name="footer">
        @foreach ($getFooterActions() as $action)
            {{ $action }}
        @endforeach
    </x-slot>
    @php
        $data = $this->mountedTableBulkAction ? $this->mountedTableBulkActionData : $this->mountedTableActionData;
    @endphp
    @if (is_array($data) && array_key_exists('table_view', $data) && $data['table_view'] == 'print-' . $getUniqueActionId())
        <script>
            printHTML(`{!! $this->printHTML !!}`, '{{ $getStatePath() }}', '{{ $getUniqueActionId() }}');
        </script>
    @endif
    @if ($shouldRefresh())
        <script>
            window.Livewire.emit("close-preview-modal-{{ $getUniqueActionId() }}");

            triggerInputEvent('{{ $getStatePath() }}', 'refresh');

            window.Livewire.emit("open-preview-modal-{{ $getUniqueActionId() }}");
        </script>
    @endif
</x-filament::modal>
