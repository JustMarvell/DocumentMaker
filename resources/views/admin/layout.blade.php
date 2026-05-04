<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — {{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            background: linear-gradient(150deg, #eef2f8 0%, #f5f7fc 60%, #edf1f8 100%);
            min-height: 100vh;
            font-family: var(--font-body);
        }

        /* Sidebar */
        .admin-sidebar {
            width: 220px;
            min-height: 100vh;
            background: linear-gradient(170deg, var(--navy-900) 0%, var(--navy-800) 70%, #1a2a50 100%);
            border-right: 1px solid rgba(201,168,76,0.12);
            position: fixed;
            top: 0; left: 0;
            display: flex;
            flex-direction: column;
            z-index: 50;
        }

        .sidebar-brand {
            padding: 1.25rem 1.1rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }

        .sidebar-nav {
            flex: 1;
            padding: 0.75rem 0.65rem;
            overflow-y: auto;
        }

        .sidebar-nav-group {
            font-size: 0.58rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.2);
            padding: 0.75rem 0.6rem 0.35rem;
        }

        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid rgba(255,255,255,0.07);
        }

        /* Main content area */
        .admin-main {
            margin-left: 220px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top bar */
        .admin-topbar {
            background: rgba(255,255,255,0.82);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(0,0,0,0.07);
            padding: 0.75rem 1.75rem;
            display: flex;
            align-items: center;
            justify-content: between;
            position: sticky;
            top: 0;
            z-index: 40;
            animation: slideDown 0.35s ease;
        }

        /* Page area */
        .admin-page {
            padding: 1.75rem;
            flex: 1;
        }

        /* Sidebar link icons placeholder */
        .nav-icon {
            width: 16px; height: 16px;
            flex-shrink: 0;
            opacity: 0.7;
        }

        /* Active sidebar indicator */
        .admin-nav-link.active .nav-icon { opacity: 1; }

        /* Gold dot active indicator */
        .active-dot {
            width: 5px; height: 5px;
            background: var(--gold-400);
            border-radius: 50%;
            margin-left: auto;
            flex-shrink: 0;
        }

        /* Flash messages */
        .flash-wrap { animation: fadeUp 0.4s ease; }
    </style>
</head>
<body>

<!-- Sidebar -->
<aside class="admin-sidebar">

    <!-- Brand -->
    <div class="sidebar-brand">
        <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:0.5rem;">
            <div style="width:30px;height:30px;background:linear-gradient(135deg,var(--gold-500),var(--gold-300));border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg style="width:14px;height:14px;color:#0d1526;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <div style="font-family:var(--font-display);color:#fff;font-size:0.95rem;line-height:1;">eDokPUPRD</div>
                <div style="color:var(--gold-400);font-size:0.55rem;letter-spacing:0.08em;text-transform:uppercase;font-weight:600;">Admin Panel</div>
            </div>
        </div>
        <div style="height:1px;background:linear-gradient(90deg,var(--gold-500),transparent);opacity:0.4;margin-top:0.5rem;"></div>
    </div>

    <!-- Nav -->
    <nav class="sidebar-nav">
        <div class="sidebar-nav-group">Utama</div>

        <a href="{{ route('admin.dashboard') }}"
           class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
            </svg>
            Dashboard
            @if(request()->routeIs('admin.dashboard'))<span class="active-dot"></span>@endif
        </a>

        <a href="{{ route('admin.logs') }}"
           class="admin-nav-link {{ request()->routeIs('admin.logs') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Riwayat Dokumen
            @if(request()->routeIs('admin.logs'))<span class="active-dot"></span>@endif
        </a>

        <div class="sidebar-nav-group">Manajemen</div>

        <a href="{{ route('admin.users') }}"
           class="admin-nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/>
            </svg>
            Pengguna
            @if(request()->routeIs('admin.users'))<span class="active-dot"></span>@endif
        </a>

        <a href="{{ route('admin.document-types') }}"
           class="admin-nav-link {{ request()->routeIs('admin.document-types*') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            Jenis Dokumen
            @if(request()->routeIs('admin.document-types*'))<span class="active-dot"></span>@endif
        </a>

        <a href="{{ route('admin.signatures') }}"
            class="admin-nav-link {{ request()->routeIs('admin.signatures*') ? 'active' : '' }}">
            <i class="fa-solid fa-list-check"></i>
            Antrian TTD
            @if(request()->routeIs('admin.signatures'))<span class="active-dot"></span>@endif
        </a>

        <div class="sidebar-nav-group">Data</div>

        <a href="{{ route('admin.staff-data') }}"
           class="admin-nav-link {{ request()->routeIs('admin.staff-data') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
            </svg>
            Data Staff
            @if(request()->routeIs('admin.staff-data'))<span class="active-dot"></span>@endif
        </a>

        <a href="{{ route('admin.official-data') }}"
           class="admin-nav-link {{ request()->routeIs('admin.official-data') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Data Pejabat
            @if(request()->routeIs('admin.official-data'))<span class="active-dot"></span>@endif
        </a>

        <div class="sidebar-nav-group">Lainnya</div>

        <a href="{{ route('admin.guide') }}"
           class="admin-nav-link {{ request()->routeIs('admin.guide*') ? 'active' : '' }}">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            Panduan
            @if(request()->routeIs('admin.guide*'))<span class="active-dot"></span>@endif
        </a>

        <a href="{{ route('home') }}" class="admin-nav-link" style="margin-top:0.5rem;">
            <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
            Lihat Aplikasi
        </a>
    </nav>

    <!-- Footer: user info + logout -->
    <div class="sidebar-footer">
        <div style="margin-bottom:0.6rem;padding:0.6rem 0.5rem;background:rgba(255,255,255,0.05);border-radius:8px;border:1px solid rgba(255,255,255,0.07);">
            <div style="font-size:0.78rem;font-weight:600;color:rgba(255,255,255,0.85);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                {{ auth()->user()->name }}
            </div>
            <div style="font-size:0.65rem;color:var(--gold-400);letter-spacing:0.04em;margin-top:0.1rem;">Administrator</div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                style="width:100%;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);color:rgba(239,68,68,0.75);border-radius:7px;padding:0.45rem;font-size:0.75rem;font-weight:500;cursor:pointer;transition:all 0.2s;font-family:var(--font-body);"
                onmouseover="this.style.background='rgba(239,68,68,0.18)';this.style.color='rgb(239,68,68)'"
                onmouseout="this.style.background='rgba(239,68,68,0.1)';this.style.color='rgba(239,68,68,0.75)'">
                Keluar
            </button>
        </form>
    </div>
</aside>

<!-- Main -->
<div class="admin-main">

    <!-- Topbar -->
    <header class="admin-topbar">
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center gap-2">
                <div style="width:3px;height:18px;background:linear-gradient(180deg,var(--gold-500),var(--gold-300));border-radius:2px;"></div>
                <span style="font-size:0.78rem;color:var(--slate-600);font-weight:500;">
                    @yield('page-title', 'Admin Panel')
                </span>
            </div>
            <div style="font-size:0.72rem;color:var(--slate-300);">
                {{ now()->locale('id')->translatedFormat('l, d F Y') }}
            </div>
        </div>
    </header>

    <!-- Flash messages -->
    @if (session('success') || session('error'))
    <div class="flash-wrap" style="padding:0.75rem 1.75rem 0;">
        @if (session('success'))
            <div class="alert alert-success">
                <div class="flex items-center gap-2">
                    <svg style="width:15px;height:15px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-error" style="margin-top:0.5rem;">
                <div class="flex items-center gap-2">
                    <svg style="width:15px;height:15px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('error') }}
                </div>
            </div>
        @endif
    </div>
    @endif

    <!-- Content -->
    <main class="admin-page">
        @yield('content')
    </main>

</div>

</body>
</html>