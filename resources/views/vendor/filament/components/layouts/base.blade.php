@props([
    'title' => null,
])

<!DOCTYPE html>
<html
    class="min-h-screen antialiased filament js-focus-visible"
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ __('filament::layout.direction') ?? 'ltr' }}"
>

<head>
    {{ \Filament\Facades\Filament::renderHook('head.start') }}

    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >
    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    @foreach (\Filament\Facades\Filament::getMeta() as $tag)
        {{ $tag }}
    @endforeach

    @if ($favicon = config('filament.favicon'))
        <link
            href="{{ asset($favicon) }}"
            rel="icon"
        >
    @endif

    <title>{{ $title ? "{$title} - " : null }} {{ config('filament.brand') }}</title>

    {{ \Filament\Facades\Filament::renderHook('styles.start') }}

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

        :root {
            --sidebar-width: {{ config('filament.layout.sidebar.width') ?? '20rem' }};
            --collapsed-sidebar-width: {{ config('filament.layout.sidebar.collapsed_width') ?? '5.4rem' }};
        }
    </style>

    @livewireStyles

    @if (filled($fontsUrl = config('filament.google_fonts')))
        <link
            href="https://fonts.googleapis.com"
            rel="preconnect"
        >
        <link
            href="https://fonts.gstatic.com"
            rel="preconnect"
            crossorigin
        >
        <link
            href="{{ $fontsUrl }}"
            rel="stylesheet"
        />
    @endif

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

    {{ \Filament\Facades\Filament::renderHook('styles.end') }}

    @if (config('filament.dark_mode'))
        <script>
            const theme = localStorage.getItem('theme')

            if ((theme === 'dark') || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark')
            }
        </script>
    @endif

    {{ \Filament\Facades\Filament::renderHook('head.end') }}
</head>

<body @class([
    'filament-body min-h-screen bg-gray-100 text-gray-900 overflow-y-auto',
    'dark:text-gray-100 dark:bg-gray-900' => config('filament.dark_mode'),
])>
    {{ \Filament\Facades\Filament::renderHook('body.start') }}

    {{ $slot }}

    {{ \Filament\Facades\Filament::renderHook('scripts.start') }}

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

    @stack('beforeCoreScripts')

    {{-- ! Changed by AlanD20 --}}
    @vite('resources/js/app.js')
    {{-- <script defer src="{{ route('filament.asset', [
                'id' => Filament\get_asset_id('app.js'),
                'file' => 'app.js',
            ]) }}"></script> --}}

    @if (config('filament.broadcasting.echo'))
        <script
            defer
            src="{{ route('filament.asset', [
                'id' => Filament\get_asset_id('echo.js'),
                'file' => 'echo.js',
            ]) }}"
        ></script>

        <script>
            window.addEventListener('DOMContentLoaded', () => {
                window.Echo = new window.EchoFactory(@js(config('filament.broadcasting.echo')))

                window.dispatchEvent(new CustomEvent('EchoLoaded'))
            })
        </script>
    @endif

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

    @stack('scripts')

    {{ \Filament\Facades\Filament::renderHook('scripts.end') }}

    {{ \Filament\Facades\Filament::renderHook('body.end') }}
</body>

</html>
