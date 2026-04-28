<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 font-sans min-h-screen flex flex-col">

    {{-- Navbar --}}
    <nav class="bg-white shadow px-4 sm:px-8 py-4 flex items-center justify-between">
        <div>
            <p class="font-bold text-gray-800 text-sm sm:text-base">DINAS PUPRD</p>
            <p class="text-xs text-gray-500 hidden sm:block">Kota Tomohon</p>
        </div>
        <div class="flex items-center gap-3">
            @auth
                <a href="{{ route('home') }}" class="text-sm text-blue-600 hover:underline font-medium">
                    Buka Aplikasi
                </a>
            @else
                <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:underline">Login</a>
                <a href="{{ route('register') }}" class="text-sm text-gray-600 hover:underline">Daftar</a>
            @endauth
        </div>
    </nav>

    {{-- Hero section --}}
    <main class="flex-1 flex items-center justify-center px-4 py-12 sm:py-20">
        <div class="w-full max-w-lg text-center">

            {{-- Logo / icon --}}
            <div class="mx-auto mb-6 w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586
                             a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>

            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">
                Sistem Automatisasi Surat
            </h1>
            <p class="text-sm sm:text-base font-semibold text-blue-700 mb-2">
                SIPADU
            </p>
            <p class="text-sm text-gray-500 mb-1">
                DINAS PEKERJAAN UMUM DAN PENATAAN RUANG DAERAH
            </p>
            <p class="text-sm text-gray-500 mb-8">
                KOTA TOMOHON
            </p>

            <p class="text-sm text-gray-600 mb-8 leading-relaxed px-4 sm:px-0">
                Platform pembuatan surat dan dokumen resmi secara otomatis.
                Login untuk mulai membuat dokumen.
            </p>

            {{-- CTA buttons --}}
            @auth
                <a href="{{ route('home') }}"
                    class="inline-flex items-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold text-sm hover:bg-blue-700 transition shadow">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                    Buka Aplikasi
                </a>
            @else
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center justify-center gap-2 bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold text-sm hover:bg-blue-700 transition shadow">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        Login
                    </a>
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center justify-center gap-2 bg-white text-gray-700 border border-gray-300 px-6 py-3 rounded-lg font-semibold text-sm hover:bg-gray-50 transition shadow-sm">
                        Daftar Akun Baru
                    </a>
                </div>
            @endauth

        </div>
    </main>

    {{-- Footer --}}
    <footer class="text-center py-4 text-xs text-gray-400 border-t border-gray-200 bg-white">
        SIPADU © {{ date('Y') }} — DINAS PUPRD Kota Tomohon
    </footer>

</body>

</html>