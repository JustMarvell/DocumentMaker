<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SIPADU') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans bg-gray-100 min-h-screen">

    <div class="min-h-screen flex flex-col sm:justify-center items-center px-4 py-8 sm:py-12">

        {{-- Logo / brand --}}
        <div class="mb-6 text-center">
            <div class="mx-auto mb-3 w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center shadow">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586
                             a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <p class="text-sm font-bold text-blue-700">SIPADU</p>
            <p class="text-xs text-gray-500">DINAS PUPRD Kota Tomohon</p>
        </div>

        {{-- Auth card --}}
        <div class="w-full sm:max-w-md bg-white shadow-md rounded-xl px-6 py-6 sm:px-8 sm:py-8">
            {{ $slot }}
        </div>

        {{-- Back to home --}}
        <a href="{{ url('/') }}" class="mt-4 text-xs text-gray-400 hover:text-gray-600 hover:underline">
            ← Kembali ke halaman utama
        </a>

    </div>

</body>

</html>