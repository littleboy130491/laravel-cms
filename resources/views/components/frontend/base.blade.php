@props(['page'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    @stack('before-head')

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $page->title ?? config('app.name') }}</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {!! $page->head_code ?? '' !!}

    @stack('after-head')
</head>

<body @class([
    'min-h-full font-sans antialiased',
    $attributes->get('class'),
])>
    @stack('after-body')


    {{ $slot }}


    {!! $page->body_code ?? '' !!}

    @stack('before-body')
</body>

</html>
