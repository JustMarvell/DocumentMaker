<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/home.css'])
</head>

<body>
    <div class="main-container">
        <div class="form-wrapper" style="text-align:center; padding: 3rem 2rem;">
            <h1 class="form-title">Sistem Automatisasi Surat</h1>
            <p class="form-description" style="margin-bottom: 0.5rem;">
                DINAS PEKERJAAN UMUM DAN PENATAAN RUANG DAERAH KOTA TOMOHON
            </p>
            <p class="form-description" style="margin-bottom: 2rem;">
                Silakan login untuk mengakses sistem pembuatan surat dan dokumen.
            </p>

            @auth
                <a href="{{ route('home') }}" style="display:inline-block; background:#2563eb; color:white;
                              padding: 0.75rem 2rem; border-radius: 0.5rem; text-decoration:none;
                              font-weight:600; margin-right: 1rem;">
                    Buka Aplikasi
                </a>
            @else
                <a href="{{ route('login') }}" style="display:inline-block; background:#2563eb; color:white;
                              padding: 0.75rem 2rem; border-radius: 0.5rem; text-decoration:none;
                              font-weight:600; margin-right: 1rem;">
                    Login
                </a>
                <a href="{{ route('register') }}" style="display:inline-block; background:#e5e7eb; color:#374151;
                              padding: 0.75rem 2rem; border-radius: 0.5rem; text-decoration:none;
                              font-weight:600;">
                    Daftar
                </a>
            @endauth
        </div>
    </div>
</body>

</html>