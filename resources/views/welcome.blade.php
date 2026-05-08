<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Selamat Datang</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/welcome.css'])
</head>
<body class="hero-bg">

    <!-- Grid overlay -->
    <div class="grid-overlay pointer-events-none"></div>

    <!-- Decorative circles -->
    <div class="geo-circle" style="width:500px;height:500px;top:-150px;left:-150px;"></div>
    <div class="geo-circle" style="width:300px;height:300px;bottom:10%;right:-80px;animation-delay:-4s;border-color:rgba(201,168,76,0.06);"></div>

    <!-- Navbar -->
    <nav class="sipadu-nav">
        <div class="max-w-6xl mx-auto px-5 sm:px-8 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="emblem" style="width:34px;height:34px;box-shadow:none;animation:none;">
                    <svg class="w-4 h-4 text-navy-900" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#0d1526;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="nav-brand-title text-sm">eDokPUPRD</div>
                    <div class="nav-brand-sub" style="font-size:0.55rem;">DINAS PUPRD · Kota Tomohon</div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @auth
                    <a href="{{ route('home') }}" class="btn-gold" style="padding:0.4rem 1rem;font-size:0.78rem;">
                        Buka Aplikasi
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn-ghost" style="padding:0.4rem 1rem;font-size:0.78rem;">Login</a>
                    <a href="{{ route('register') }}" class="btn-gold" style="padding:0.4rem 1rem;font-size:0.78rem;">Daftar</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <main class="max-w-6xl mx-auto px-5 sm:px-8 py-16 sm:py-24 relative z-10">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

            <!-- Left: Hero copy -->
            <div>
                <div class="fade-up fade-up-1 flex items-center gap-3 mb-6">
                    <div class="gold-accent-line"></div>
                    <span class="section-label" style="color:var(--gold-400);font-size:0.65rem;">Sistem Otomasi Persuratan Resmi</span>
                </div>

                <div class="fade-up fade-up-2 emblem mb-6">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#0d1526;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>

                <h1 class="fade-up fade-up-2 display-heading text-white mb-3" style="font-size:clamp(2.2rem,5vw,3.4rem);">
                    eDokPUPRD
                </h1>
                <p class="fade-up fade-up-3 mb-2" style="font-size:0.95rem;color:rgba(255,255,255,0.5);letter-spacing:0.05em;text-transform:uppercase;font-weight:500;">
                    Sistem Pembuatan Dokumen Digital
                </p>
                <p class="fade-up fade-up-3 mb-8" style="font-size:0.82rem;color:var(--gold-400);letter-spacing:0.06em;text-transform:uppercase;">
                    Dinas Pekerjaan Umum dan Penataan Ruang Daerah
                </p>

                <p class="fade-up fade-up-4 mb-8 leading-relaxed" style="color:rgba(255,255,255,0.55);font-size:0.9rem;max-width:440px;">
                    Platform pembuatan surat dan dokumen resmi secara otomatis.
                    Efisiensi administrasi dengan teknologi modern untuk pelayanan publik yang lebih baik.
                </p>

                <div class="fade-up fade-up-4 flex flex-wrap gap-3">
                    @auth
                        <a href="{{ route('home') }}" class="btn-gold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                            Buka Aplikasi
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-gold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14"/></svg>
                            Masuk ke Sistem
                        </a>
                        <a href="{{ route('register') }}" class="btn-ghost">
                            Daftarkan Akun
                        </a>
                    @endauth
                </div>

                <!-- Scroll hint -->
                <div class="scroll-hint mt-12 flex items-center gap-2" style="color:rgba(255,255,255,0.25);font-size:0.72rem;letter-spacing:0.06em;">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    Fitur Unggulan
                </div>
            </div>

            <!-- Right: Login / CTA panel -->
            <div class="fade-up fade-up-3">
                <div class="login-panel">
                    <div class="flex items-center gap-2 mb-5">
                        <div class="gold-accent-line"></div>
                        <span style="font-size:0.7rem;color:var(--gold-400);letter-spacing:0.08em;text-transform:uppercase;font-weight:600;">Akses Sistem</span>
                    </div>

                    <!-- Feature list -->
                    <div class="space-y-3 mb-6">
                        @foreach([
                            ['Pembuatan Surat Otomatis', 'Isi form, sistem mengisi template — selesai dalam hitungan detik.'],
                            ['Autofill Data Pegawai', 'Data nama, NIP, jabatan terisi otomatis dari database dinas.'],
                            ['Multi-format Dokumen', 'Dukungan template Word (.docx) dan Excel (.xlsx).'],
                            ['Riwayat & Audit', 'Setiap dokumen tercatat dengan lengkap untuk keperluan audit.'],
                        ] as $f)
                        <div class="flex items-start gap-3 py-2 border-b" style="border-color:rgba(255,255,255,0.06);">
                            <div class="flex-shrink-0 w-5 h-5 rounded-full flex items-center justify-center mt-0.5" style="background:rgba(201,168,76,0.15);">
                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--gold-400);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <p style="color:rgba(255,255,255,0.85);font-size:0.82rem;font-weight:600;">{{ $f[0] }}</p>
                                <p style="color:rgba(255,255,255,0.4);font-size:0.75rem;margin-top:0.15rem;line-height:1.4;">{{ $f[1] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Role info -->
                    <div class="rounded-10 p-3 mb-5" style="background:rgba(42,82,152,0.15);border:1px solid rgba(42,82,152,0.2);border-radius:8px;">
                        <p style="font-size:0.72rem;color:rgba(255,255,255,0.5);margin-bottom:0.5rem;letter-spacing:0.04em;text-transform:uppercase;font-weight:600;">Level Akses</p>
                        <div class="flex gap-3">
                            @foreach([['Guest','Dokumen Publik'],['Staff','Semua Dokumen'],['Admin','Kelola Sistem']] as $r)
                            <div class="text-center flex-1">
                                <div style="color:var(--gold-400);font-size:0.78rem;font-weight:700;">{{ $r[0] }}</div>
                                <div style="color:rgba(255,255,255,0.35);font-size:0.65rem;margin-top:0.1rem;">{{ $r[1] }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    @auth
                        <a href="{{ route('home') }}" class="btn-gold w-full justify-center" style="width:100%;">
                            Buka Aplikasi →
                        </a>
                    @else
                        <div class="flex flex-col gap-2">
                            <a href="{{ route('login') }}" class="btn-gold justify-center" style="width:100%;">
                                Masuk ke Sistem
                            </a>
                            <a href="{{ route('register') }}" class="btn-ghost justify-center" style="width:100%;border-color:rgba(255,255,255,0.15);">
                                Daftarkan Akun Baru
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Stats strip -->
        <div class="stats-strip mt-20 rounded-2xl fade-up fade-up-5">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-0 divide-x" style="divide-color:rgba(255,255,255,0.06);">
                @foreach([['3','Jenis Dokumen'],['Auto','Pengisian Data'],['PDF','Preview Instan'],['Aman','Berbasis Akun']] as $s)
                <div class="text-center py-5 px-4">
                    <div class="stat-num">{{ $s[0] }}</div>
                    <div class="stat-desc">{{ $s[1] }}</div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Features -->
        <div class="mt-16 fade-up fade-up-5">
            <div class="flex items-center gap-3 mb-8">
                <div class="gold-accent-line"></div>
                <span class="section-label" style="color:rgba(255,255,255,0.4);">Kemampuan Sistem</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach([
                    ['M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z','Template Fleksibel','Dukung format Word dan Excel dengan placeholder Jinja2 yang powerful.'],
                    ['M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0','Data Staff & Pejabat','Database terpusat untuk pegawai dan pejabat, mengisi form secara otomatis.'],
                    ['M15 12a3 3 0 11-6 0 3 3 0 016 0z M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z','Preview PDF','Pratinjau dokumen langsung di browser sebelum mengunduh.'],
                    ['M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2','Daftar Peserta','Pilih dan urutkan peserta dari daftar dengan drag-and-drop.'],
                    ['M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z','Kontrol Akses','Tiga tingkat akses: Guest, Staff, dan Admin dengan hak yang berbeda.'],
                    ['M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z','Hapus Otomatis','File temporer dibersihkan otomatis, menjaga server tetap efisien.'],
                ] as $feat)
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $feat[0] }}"/></svg>
                    </div>
                    <div class="feature-title">{{ $feat[1] }}</div>
                    <div class="feature-desc">{{ $feat[2] }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="welcome-footer mt-10 py-5 text-center" style="color:rgba(255,255,255,0.25);font-size:0.73rem;letter-spacing:0.04em;">
        eDokPUPRD © {{ date('Y') }} — Dinas Pekerjaan Umum dan Penataan Ruang Daerah · Kota Tomohon
    </footer>

</body>
</html>