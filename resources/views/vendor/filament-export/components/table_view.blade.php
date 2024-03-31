@php
    $uniqueActionId = $getUniqueActionId();

    $statePath = $getStatePath();

    $shouldRefresh = $shouldRefresh();

    $data = $this->mountedTableBulkAction
        ? $this->getMountedTableBulkActionForm()->getState()
        : $this->getMountedTableActionForm()->getState();

    $shouldPrint =
        is_array($data) && array_key_exists('table_view', $data) && $data['table_view'] == 'print-' . $uniqueActionId;

    $printContent = $shouldPrint ? $getPrintHTML() : '';
@endphp

<input
    id="{{ $statePath }}"
    type="hidden"
    {{ $applyStateBindingModifiers('wire:model') }}="{{ $statePath }}"
>

<x-filament::modal
    id="preview-modal"
    width="7xl"
    display-classes="block"
    :dark-mode="config('filament.dark_mode')"
    x-data="{
        shouldRefresh: {{ $shouldRefresh ? 'true' : 'false' }},
        shouldPrint: {{ $shouldPrint ? 'true' : 'false' }}
    }"
    x-init="$wire.$on('open-preview-modal-{{ $uniqueActionId }}', function() {
        triggerInputEvent('{{ $statePath }}', '{{ uniqid() }}');
        isOpen = true;
    });
    
    $wire.$on('close-preview-modal-{{ $uniqueActionId }}', () => { isOpen = false; });
    
    if (shouldRefresh) {
        $wire.dispatch('close-preview-modal-{{ $uniqueActionId }}');
    
        triggerInputEvent('{{ $statePath }}', '{{ uniqid() }}');
    
        $wire.dispatch('open-preview-modal-{{ $uniqueActionId }}');
    }
    
    
    if (shouldPrint) {
        window.printHTML(`{!! $printContent !!}`, '{{ $statePath }}', '{{ $uniqueActionId }}');
    }"
    :heading="$getPreviewModalHeading()"
>
    <div class="preview-table-wrapper space-y-4">

        <x-report.header />

        <div class="my-8 text-2xl font-nrt">
            {{ $this->getTitle() }}
        </div>


        <table
            class="my-8 preview-table dark:bg-gray-800 dark:text-white dark:border-gray-700"
            x-init="$wire.$on('print-table-{{ $uniqueActionId }}', function() {
                triggerInputEvent('{{ $statePath }}', 'print-{{ $uniqueActionId }}')
            })"
        >
            <tr class="text-center dark:border-gray-700">
                @foreach ($getAllColumns() as $column)
                    <th class="dark:border-gray-700">
                        {{ $column->getLabel() }}
                    </th>
                @endforeach
            </tr>
            @foreach ($getRows() as $row)
                @php
                    $isSummaryRow = false;
                @endphp
                <tr class="dark:border-gray-700">
                    @foreach ($getAllColumns() as $column)
                        @php
                            $isSummaryCell = $row[$column->getName()] === __('summary');

                            if ($isSummaryCell) {
                                $isSummaryRow = true;
                            }
                        @endphp
                        <td @class([
                            'text-center dark:border-gray-700',
                            'td-field-summary' => $isSummaryCell || $isSummaryRow,
                        ])>
                            {!! translate_column_value($column, $row) !!}
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </table>
        <div>
            <x-filament::pagination
                :paginator="$getRows()"
                :page-options="$this->getTable()->getPaginationPageOptions()"
                class="preview-table-pagination px-3 py-3"
            />
        </div>
    </div>
    <x-slot name="footer">
        @foreach ($getFooterActions() as $action)
            {{ $action }}
        @endforeach
    </x-slot>
</x-filament::modal>
