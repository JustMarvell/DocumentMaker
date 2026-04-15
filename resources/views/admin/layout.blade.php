<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 font-sans">

    {{-- Navbar --}}
    <nav class="bg-white shadow px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-6">
            <span class="font-bold text-gray-800">DINAS PUPRD — Admin</span>
            <a href="{{ route('admin.dashboard') }}"
                class="text-sm {{ request()->routeIs('admin.dashboard') ? 'text-blue-600 font-semibold' : 'text-gray-600 hover:text-blue-600' }}">
                Dashboard
            </a>
            <a href="{{ route('admin.logs') }}"
                class="text-sm {{ request()->routeIs('admin.logs') ? 'text-blue-600 font-semibold' : 'text-gray-600 hover:text-blue-600' }}">
                Riwayat Dokumen
            </a>
            <a href="{{ route('admin.users') }}"
                class="text-sm {{ request()->routeIs('admin.users') ? 'text-blue-600 font-semibold' : 'text-gray-600 hover:text-blue-600' }}">
                Pengguna
            </a>
            <a href="{{ route('admin.document-types') }}"
                class="text-sm {{ request()->routeIs('admin.document-types') ? 'text-blue-600 font-semibold' : 'text-gray-600 hover:text-blue-600' }}">
                Jenis Dokumen
            </a>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-red-500 hover:underline">Logout</button>
            </form>
        </div>
    </nav>

    {{-- Flash messages --}}
    <div class="max-w-7xl mx-auto px-6 mt-4">
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif
    </div>

    {{-- Page content --}}
    <main class="max-w-7xl mx-auto px-6 py-6">
        @yield('content')
    </main>

</body>

</html>