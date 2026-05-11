<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link crossorigin href="https://fonts.gstatic.com/" rel="preconnect"/>
    <link as="style" href="https://fonts.googleapis.com/css2?display=swap&family=Work+Sans:wght@400;500;700;900" onload="this.rel='stylesheet'" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
    <title>{{ $title ?? config('app.name') }}</title>
    <link href="/favicon.ico" rel="icon" type="image/x-icon"/>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-GDT2SKSLK2"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag()
        {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());
        gtag('config', 'G-GDT2SKSLK2');
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

@include('partials.ajax-loader')
<body class="bg-slate-50 text-slate-900">
    <main>
        {{ $slot }}
    </main>


    @livewireScripts
</body>
</html>
