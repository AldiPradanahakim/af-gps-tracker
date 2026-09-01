<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Trackio membantu Anda masuk atau mengaktivasi perangkat GPS untuk memantau lokasi kendaraan secara real-time, riwayat perjalanan, dan notifikasi keamanan.">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite([
            'resources/css/app.css',
            'resources/js/app.js'
        ])
    </head>
    <body class="bg-[#F8FAFC] antialiased text-slate-900">
        <main class="min-h-screen flex items-start lg:items-center justify-center px-4 py-6 sm:px-6 lg:py-8">
            {{ $slot }}
        </main>
    </body>
</html>
