<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link crossorigin href="https://fonts.gstatic.com/" rel="preconnect"/>
    <link as="style" href="https://fonts.googleapis.com/css2?display=swap&family=Noto+Sans:wght@400;500;700;900&family=Work+Sans:wght@400;500;700;900" onload="this.rel='stylesheet'" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
    <title>Home - {{ config('app.name') }}</title>
    <link href="/favicon.ico" rel="icon" type="image/x-icon"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    @livewireStyles
</head>
<body class="bg-slate-50" style='font-family: "Work Sans", "Noto Sans", sans-serif;'>
    <livewire:cars-home />

    @livewireScripts
</body>
</html>
