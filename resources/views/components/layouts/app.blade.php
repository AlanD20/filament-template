@props([
    'title' => null,
])

<!DOCTYPE html>
<html
    class="filament js-focus-visible bg-gray-100 antialiased"
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ __('filament::layout.direction') ?? 'ltr' }}"
>

<head>

    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    @if ($favicon = config('filament.favicon'))
        <link
            href="{{ $favicon }}"
            rel="icon"
        >
    @endif

    <title>{{ $title ? "{$title} - " : null }} {{ config('filament.brand') }}</title>

    <style>
        [x-cloak=""],
        [x-cloak="x-cloak"],
        [x-cloak="1"] {
            display: none !important;
        }

        @media (max-width: 1023px) {
            [x-cloak="-lg"] {
                display: none !important;
            }
        }

        @media (min-width: 1024px) {
            [x-cloak="lg"] {
                display: none !important;
            }
        }
    </style>

    @livewireStyles

    @foreach (\Filament\Facades\Filament::getStyles() as $name => $path)
        @if (\Illuminate\Support\Str::of($path)->startsWith(['http://', 'https://']))
            <link
                href="{{ $path }}"
                rel="stylesheet"
            />
        @elseif (\Illuminate\Support\Str::of($path)->startsWith('<'))
            {!! $path !!}
        @else
            <link
                href="{{ route('filament.asset', [
                    'file' => "{$name}.css",
                ]) }}"
                rel="stylesheet"
            />
        @endif
    @endforeach

    {{ \Filament\Facades\Filament::getThemeLink() }}

    @if (config('filament.dark_mode'))
        <script>
            const theme = localStorage.getItem('theme')

            if ((theme === 'dark') || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark')
            }
        </script>
    @endif

</head>

<body @class([
    'filament-body bg-gray-100 text-gray-900 min-h-screen flex flex-col items-center justify-center',
    'dark:text-gray-100 dark:bg-gray-900' => config('filament.dark_mode'),
])>

    {{ $slot }}

    @livewireScripts

    <script>
        window.filamentData = @json(\Filament\Facades\Filament::getScriptData());
    </script>

    @foreach (\Filament\Facades\Filament::getBeforeCoreScripts() as $name => $path)
        @if (\Illuminate\Support\Str::of($path)->startsWith(['http://', 'https://']))
            <script
                defer
                src="{{ $path }}"
            ></script>
        @elseif (\Illuminate\Support\Str::of($path)->startsWith('<'))
            {!! $path !!}
        @else
            <script
                defer
                src="{{ route('filament.asset', [
                    'file' => "{$name}.js",
                ]) }}"
            ></script>
        @endif
    @endforeach

    @vite('resources/js/app.js')

    @foreach (\Filament\Facades\Filament::getScripts() as $name => $path)
        @if (\Illuminate\Support\Str::of($path)->startsWith(['http://', 'https://']))
            <script
                defer
                src="{{ $path }}"
            ></script>
        @elseif (\Illuminate\Support\Str::of($path)->startsWith('<'))
            {!! $path !!}
        @else
            <script
                defer
                src="{{ route('filament.asset', [
                    'file' => "{$name}.js",
                ]) }}"
            ></script>
        @endif
    @endforeach

</body>

</html>
