<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    </head>
    <body class="min-h-screen bg-gray-50 antialiased dark:bg-neutral-900">
        <!-- Auth pages full-viewport container; inner views control their own layout -->
        {{ $slot }}
        @fluxScripts
    </body>
</html>
