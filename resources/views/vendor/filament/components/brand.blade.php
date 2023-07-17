{{-- ! Changed by AlanD20 --}}

@if (filled($brand = config('filament.brand')) && !config('filament.brand_image'))
    <div @class([
        'filament-brand text-xl font-bold leading-5 tracking-tight',
        'dark:text-white' => config('filament.dark_mode'),
    ])>
        {{ $brand }}
    </div>
@else
    <img
        class="w-32"
        src="{{ asset('/images/dark-logo.svg') }}"
        alt="logo"
    >
@endif
