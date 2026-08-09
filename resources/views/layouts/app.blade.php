<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="description" content="@yield('meta_description', 'Trackio: pantau lokasi kendaraan Anda secara real-time, lihat riwayat perjalanan, dan kelola geofence dalam satu platform.')">

    <title>
        @yield('title', config('app.name'))
    </title>

    <link
        rel="preconnect"
        href="https://fonts.bunny.net">

    <link
        href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap"
        rel="stylesheet">

    @vite([
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/js/leaflet-setup.js'
    ])

    @stack('styles')

</head>

<body class="font-sans antialiased bg-[#F8FAFC]">

    @yield('content')

    {{-- ===================================================== --}}
    {{-- Global Toast --}}
    {{-- ===================================================== --}}

    <x-toast />

    @include('components.scripts.toast')
    @stack('scripts')

</body>

</html>