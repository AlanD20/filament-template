@php
    $config = array_merge($getConfig(), [
        'altInput' => $isAltInput(),
        'enableTime' => $isEnableTime(),
        'dateFormat' => $getDateFormat(),
    ]);
    $attribs = [
        'disabled' => $isDisabled(),
        'theme' => $getTheme(),
        'monthSelect' => $isMonthSelect(),
        'weekSelect' => $isWeekSelect(),
        'mode' => $isRangePicker() ? 'range' : ($isMultiplePicker() ? 'multiple' : 'single'),
    ];
@endphp
@once
    <script></script>
@endonce
<x-forms::field-wrapper
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div
        wire:ignore
        x-data="datepicker(@entangle($getStatePath()), @js($config), @js($attribs))"
    >
        <template x-if="attribs.theme!=='default'">
            <link
                type="text/css"
                x-else
                rel="stylesheet"
                :href="`https://npmcdn.com/flatpickr/dist/themes/${attribs.theme}.css`"
            >
        </template>
        {{-- ! Changed by AlanD20 --}}
        {{-- <template x-if="mode ==='dark'">
            <link
                type="text/css"
                rel="stylesheet"
                :href="`https://npmcdn.com/flatpickr/dist/themes/dark.css`"
            >
        </template> --}}
        <!-- Interact with the `state` property in Alpine.js -->
        <div class="relative flex items-center justify-start">
            <x-heroicon-o-calendar
                class="absolute flex items-center justify-center w-6 h-6 pl-1 text-gray-400 pointer-events-none group-focus-within:text-primary-500"
            />
            <input
                class="block w-full h-10 pl-10 placeholder-gray-400 transition duration-75 border-gray-300 rounded-lg shadow-sm focus:border-primary-600 focus:ring-1 focus:ring-inset focus:ring-primary-600"
                id="picker"
                {{-- ! Changed by AlanD20 --}}
                {{-- class="block w-full h-10 pl-10 placeholder-gray-400 transition duration-75 border-gray-300 rounded-lg shadow-sm focus:border-primary-600 focus:ring-1 focus:ring-inset focus:ring-primary-600 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400" --}}
                {{ $isDisabled() ? 'disabled' : '' }}
                x-ref="picker"
                x-model="state"
            >
        </div>
    </div>
</x-forms::field-wrapper>
