@props([
    'title' => config('app.name'),
    'description' => 'ReadyCars offers premium chauffeur-driven car rentals in Nigeria for airport transfers, corporate rides, and daily bookings.',
    'keywords' => 'car rental Nigeria, chauffeur service, airport transfer, corporate rides, ReadyCars',
    'robots' => 'index,follow',
    'canonical' => url()->current(),
    'ogType' => 'website',
    'ogImage' => asset('favicon.ico'),
])

@php
    $metaTitle = trim((string) $title);
    $metaDescription = trim((string) $description);
    $metaKeywords = trim((string) $keywords);
    $metaRobots = trim((string) $robots);
    $canonicalUrl = trim((string) $canonical) !== '' ? $canonical : url()->current();
    $metaOgType = trim((string) $ogType) !== '' ? $ogType : 'website';
    $metaImage = trim((string) $ogImage) !== '' ? $ogImage : asset('favicon.ico');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>{{ $metaTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}" />
    <meta name="keywords" content="{{ $metaKeywords }}" />
    <meta name="robots" content="{{ $metaRobots }}" />
    <link rel="canonical" href="{{ $canonicalUrl }}" />
    <meta name="theme-color" content="#0e1133" />

    <meta property="og:locale" content="en_NG" />
    <meta property="og:type" content="{{ $metaOgType }}" />
    <meta property="og:site_name" content="{{ config('app.name') }}" />
    <meta property="og:title" content="{{ $metaTitle }}" />
    <meta property="og:description" content="{{ $metaDescription }}" />
    <meta property="og:url" content="{{ $canonicalUrl }}" />
    <meta property="og:image" content="{{ $metaImage }}" />

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $metaTitle }}" />
    <meta name="twitter:description" content="{{ $metaDescription }}" />
    <meta name="twitter:image" content="{{ $metaImage }}" />

    <link crossorigin href="https://fonts.gstatic.com/" rel="preconnect"/>
    <link as="style" href="https://fonts.googleapis.com/css2?display=swap&family=Work+Sans:wght@400;500;700;900" onload="this.rel='stylesheet'" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
    <link href="/favicon.ico" rel="icon" type="image/x-icon"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{ $meta ?? '' }}
    @stack('meta')
</head>

@include('partials.ajax-loader')
<body class="bg-slate-50 text-slate-900">
    @include('partials.header')

    <main>
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
