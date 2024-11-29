@props(['page'])
@inject('settings', 'App\Settings\GeneralSettings')
@inject('media', 'Awcodes\Curator\Models\Media')
@php
    $siteIcon = $media->find($settings->site_icon);
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {!! seo($page ?? null) !!}
    @stack('after-head')
    <link rel="icon" type="image/x-icon" href="{{ $siteIcon->url ?? '' }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {!! $page->head_code ?? '' !!}
    @stack('before-head')
    {!! $settings->head_code !!}
</head>

<body @class([
    'min-h-full font-sans antialiased',
    $attributes->get('class'),
])>
    @stack('after-body')
    {{ $slot }}
    {!! $page->body_code ?? '' !!}
    @stack('before-body')
    {!! $settings->body_code !!}
</body>

</html>
