<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <title>{{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            background:
                radial-gradient(ellipse 80% 60% at 50% -5%, rgba(42,82,152,0.3) 0%, transparent 60%),
                linear-gradient(160deg, #0a0f1e 0%, #0d1526 60%, #101c38 100%);
            min-height: 100vh;
            font-family: var(--font-body);
        }

        /* Subtle grid */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(42,82,152,0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(42,82,152,0.05) 1px, transparent 1px);
            background-size: 64px 64px;
            pointer-events: none;
            z-index: 0;
        }

        .auth-card {
            background: rgba(255,255,255,0.06);
            backdrop-filter: blur(24px) saturate(1.3);
            -webkit-backdrop-filter: blur(24px) saturate(1.3);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            box-shadow:
                0 24px 80px rgba(0,0,0,0.45),
                inset 0 1px 0 rgba(255,255,255,0.08);
            animation: fadeUp 0.5s cubic-bezier(0.22,1,0.36,1) forwards;
        }

        /* Labels and inputs inside auth forms */
        .auth-card label {
            color: rgba(255,255,255,0.65);
            font-size: 0.78rem;
            font-weight: 500;
            letter-spacing: 0.02em;
        }

        .auth-card input[type="text"],
        .auth-card input[type="email"],
        .auth-card input[type="password"] {
            background: rgba(255,255,255,0.07) !important;
            border: 1.5px solid rgba(255,255,255,0.12) !important;
            border-radius: 8px !important;
            color: rgba(255,255,255,0.9) !important;
            font-family: var(--font-body) !important;
            font-size: 0.875rem !important;
            padding: 0.65rem 0.9rem !important;
            width: 100% !important;
            transition: all 0.2s ease !important;
            outline: none !important;
        }
        .auth-card input:focus {
            background: rgba(255,255,255,0.1) !important;
            border-color: var(--gold-400) !important;
            box-shadow: 0 0 0 3px rgba(201,168,76,0.12) !important;
        }
        .auth-card input::placeholder {
            color: rgba(255,255,255,0.25) !important;
        }

        /* Override primary button inside auth */
        .auth-card button[type="submit"],
        .auth-card .primary-btn {
            background: linear-gradient(135deg, var(--gold-500), var(--gold-400)) !important;
            color: #0d1526 !important;
            font-weight: 700 !important;
            border-radius: 9px !important;
            border: none !important;
            font-size: 0.85rem !important;
            letter-spacing: 0.02em !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 4px 16px rgba(201,168,76,0.25) !important;
        }
        .auth-card button[type="submit"]:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 6px 20px rgba(201,168,76,0.35) !important;
        }

        /* Links inside auth */
        .auth-card a {
            color: var(--gold-300);
            font-size: 0.8rem;
        }
        .auth-card a:hover { color: var(--gold-400); text-decoration: underline; }

        /* Checkbox */
        .auth-card input[type="checkbox"] {
            accent-color: var(--gold-400);
        }

        /* Error messages */
        .auth-card ul.text-red-600,
        .auth-card p.text-red-600,
        .auth-card .text-red-600 {
            color: #fca5a5 !important;
            font-size: 0.75rem !important;
        }

        /* Status message */
        .auth-card .text-green-600 {
            color: #86efac !important;
            font-size: 0.78rem !important;
        }

        /* Divider between elements */
        .auth-card .border-t,
        .auth-card .mt-4 { border-color: rgba(255,255,255,0.08) !important; }

        .brand-emblem {
            width: 52px; height: 52px;
            background: linear-gradient(135deg, var(--gold-500), var(--gold-300));
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            box-shadow:
                0 0 0 8px rgba(201,168,76,0.08),
                0 8px 28px rgba(201,168,76,0.22);
            animation: pulse-gold 3s ease-in-out infinite;
            margin: 0 auto;
        }
    </style>
</head>
<body>

<div class="relative z-10 min-h-screen flex flex-col sm:justify-center items-center px-4 py-10">

    <!-- Brand -->
    <div class="mb-7 text-center fade-up">
        <div class="brand-emblem mb-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#0d1526;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <div style="font-family:var(--font-display);color:#fff;font-size:1.4rem;letter-spacing:0.02em;line-height:1;">{{ config('app.name') }}</div>
        <div style="color:var(--gold-400);font-size:0.62rem;letter-spacing:0.1em;text-transform:uppercase;font-weight:600;margin-top:0.3rem;">
            Sistem Pembuatan Dokumen Digital
        </div>
        <div style="color:rgba(255,255,255,0.35);font-size:0.65rem;letter-spacing:0.06em;text-transform:uppercase;margin-top:0.2rem;">
            Dinas PUPRD · Kota Tomohon
        </div>
    </div>

    <!-- Auth Card -->
    <div class="auth-card w-full sm:max-w-md px-7 py-8 fade-up fade-up-2">
        {{ $slot }}
    </div>

    <!-- Back link -->
    <a href="{{ url('/') }}"
       class="mt-5 fade-up fade-up-3"
       style="color:rgba(255,255,255,0.3);font-size:0.73rem;letter-spacing:0.04em;text-decoration:none;transition:color 0.2s;"
       onmouseover="this.style.color='rgba(255,255,255,0.6)'"
       onmouseout="this.style.color='rgba(255,255,255,0.3)'">
        ← Kembali ke Halaman Utama
    </a>

</div>

</body>
</html>