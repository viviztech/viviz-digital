<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        {{ $title ?? \App\Models\Setting::get('site_name', 'AuraAssets') . ' - ' . \App\Models\Setting::get('site_tagline', 'Premium Digital Marketplace') }}
    </title>
    <meta name="description"
        content="Discover stunning photos, videos, and templates from talented creators worldwide. Instant downloads, lifetime licenses.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|space-grotesk:500,600,700" rel="stylesheet" />

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="antialiased">
    <livewire:marketing.navbar />
    {{ $slot }}
    <x-marketing.footer />

    @livewireScripts
</body>

</html>