@props([
    'header' => null,
    'title' => null,
    'pageHeader' => null,
])

<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ __('filament::layout.direction') ?? 'ltr' }}"
>

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <meta
        http-equiv="X-UA-Compatible"
        content="ie=edge"
    >
    <meta
        http-equiv="Content-Type"
        content="text/html; charset=utf-8"
    />
    <link
        href="{{ asset('css/print.css') }}"
        rel="stylesheet"
    >
    <title>{{ $title }}</title>

    {{ $header }}
</head>

<body>
    <x-report.header />

    <div class="my-4 text-2xl">
        {{ $pageHeader }}
    </div>

    {{ $slot }}

    <x-report.footer />

</body>

</html>
