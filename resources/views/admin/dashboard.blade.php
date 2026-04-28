@extends('layouts.admin')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-5">

    {{-- ── Header ──────────────────────────────────────────────── --}}
    <div class="fade-up flex items-end justify-between">
        <div>
            <div class="section-label mb-1">Selamat datang,</div>
            <h1 class="display-heading" style="font-size:1.55rem;">{{ auth()->user()->name }}</h1>
        </div>
        <div style="font-size:0.73rem;color:var(--slate-400);">
            {{ now()->locale('id')->translatedFormat('l, d F Y · H:i') }} WIB
        </div>
    </div>

    {{-- ── Stat Cards ──────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            [
                'label'  => 'Total Dokumen',
                'value'  => $totalDocuments ?? 0,
                'sub'    => 'Semua waktu',
                'color'  => 'var(--navy-600)',
                'icon'   => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                'delay'  => 'fade-up-1',
            ],
            [
                'label'  => 'Dokumen Bulan Ini',
                'value'  => $monthlyDocuments ?? 0,
                'sub'    => now()->locale('id')->translatedFormat('F Y'),
                'color'  => 'var(--gold-500)',
                'icon'   => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                'delay'  => 'fade-up-2',
            ],
            [
                'label'  => 'Total Pengguna',
                'value'  => $totalUsers ?? 0,
                'sub'    => 'Terdaftar',
                'color'  => '#0891b2',
                'icon'   => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197',
                'delay'  => 'fade-up-3',
            ],
            [
                'label'  => 'Jenis Dokumen',
                'value'  => $totalDocumentTypes ?? 0,
                'sub'    => 'Template aktif',
                'color'  => '#7c3aed',
                'icon'   => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z',
                'delay'  => 'fade-up-4',
            ],
        ] as $stat)
        <div class="stat-card {{ $stat['delay'] }}">
            <div class="flex items-start justify-between mb-3">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0"
                     style="background:{{ $stat['color'] }}18; border:1px solid {{ $stat['color'] }}28;">
                    <svg class="w-4 h-4" fill="none" stroke="{{ $stat['color'] }}" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $stat['icon'] }}"/>
                    </svg>
                </div>
                <span class="stat-label">{{ $stat['label'] }}</span>
            </div>
            <div class="stat-value" style="color:{{ $stat['color'] }};">{{ $stat['value'] }}</div>
            <div class="stat-label mt-1" style="color:var(--slate-300);font-size:0.68rem;">{{ $stat['sub'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- ── Main grid ───────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Recent logs --}}
        <div class="lg:col-span-2 glass-card rounded-2xl overflow-hidden fade-up fade-up-2">
            <div class="flex items-center justify-between px-5 py-3.5"
                 style="border-bottom:1px solid rgba(0,0,0,0.06);">
                <div class="flex items-center gap-2">
                    <div style="width:3px;height:16px;background:linear-gradient(180deg,var(--gold-500),var(--gold-300));border-radius:2px;"></div>
                    <span style="font-size:0.82rem;font-weight:700;color:var(--navy-800);">Dokumen Terbaru</span>
                </div>
                <a href="{{ route('admin.logs') }}" class="badge badge-navy" style="font-size:0.65rem;">
                    Lihat semua →
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="sipadu-table">
                    <thead>
                        <tr>
                            <th>Dokumen</th>
                            <th>Dibuat oleh</th>
                            <th>Waktu</th>
                            <th>Tipe</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentLogs ?? [] as $log)
                        <tr>
                            <td>
                                <span style="font-weight:600;color:var(--slate-800);font-size:0.82rem;">
                                    {{ $log->document_type_name ?? '—' }}
                                </span>
                            </td>
                            <td style="color:var(--slate-500);font-size:0.8rem;">
                                {{ $log->user->name ?? 'Sistem' }}
                            </td>
                            <td style="color:var(--slate-400);font-size:0.75rem;white-space:nowrap;">
                                {{ $log->created_at->diffForHumans() }}
                            </td>
                            <td>
                                <span class="badge badge-navy" style="font-size:0.65rem;text-transform:uppercase;">
                                    {{ strtoupper($log->file_type ?? 'docx') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-8" style="color:var(--slate-300);">
                                Belum ada dokumen yang dibuat.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Quick links --}}
        <div class="space-y-4 fade-up fade-up-3">

            {{-- Quick actions --}}
            <div class="glass-card rounded-2xl p-4">
                <div class="flex items-center gap-2 mb-3">
                    <div style="width:3px;height:16px;background:linear-gradient(180deg,var(--gold-500),var(--gold-300));border-radius:2px;"></div>
                    <span style="font-size:0.8rem;font-weight:700;color:var(--navy-800);">Aksi Cepat</span>
                </div>
                <div class="space-y-2">
                    @foreach([
                        [route('admin.document-types.create'), 'Tambah Jenis Dokumen',
                         'M12 4v16m8-8H4', 'var(--navy-600)'],
                        [route('admin.users'),                 'Kelola Pengguna',
                         'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0', 'var(--navy-500)'],
                        [route('admin.staff-data'),            'Upload Data Staff',
                         'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12', '#0891b2'],
                        [route('admin.official-data'),         'Upload Data Pejabat',
                         'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', '#7c3aed'],
                        [route('home'),                        'Buka Aplikasi',
                         'M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14', 'var(--gold-500)'],
                    ] as [$url, $label, $icon, $color])
                    <a href="{{ $url }}"
                       style="display:flex;align-items:center;gap:0.65rem;padding:0.55rem 0.75rem;border-radius:8px;text-decoration:none;transition:all 0.2s;border:1px solid transparent;"
                       onmouseover="this.style.background='rgba(42,82,152,0.06)';this.style.borderColor='rgba(42,82,152,0.12)'"
                       onmouseout="this.style.background='transparent';this.style.borderColor='transparent'">
                        <div style="width:28px;height:28px;border-radius:7px;background:{{ $color }}18;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg style="width:13px;height:13px;" fill="none" stroke="{{ $color }}" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                            </svg>
                        </div>
                        <span style="font-size:0.8rem;font-weight:500;color:var(--slate-700);">{{ $label }}</span>
                        <svg style="width:13px;height:13px;color:var(--slate-300);margin-left:auto;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- System status --}}
            <div class="glass-card rounded-2xl p-4">
                <div class="flex items-center gap-2 mb-3">
                    <div style="width:3px;height:16px;background:linear-gradient(180deg,var(--gold-500),var(--gold-300));border-radius:2px;"></div>
                    <span style="font-size:0.8rem;font-weight:700;color:var(--navy-800);">Status Sistem</span>
                </div>
                <div class="space-y-2">
                    @foreach([
                        ['Database', 'Terhubung',   true],
                        ['Storage',  'Aktif',        true],
                        ['Queue',    'Berjalan',     true],
                    ] as [$name, $status, $ok])
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <span style="font-size:0.78rem;color:var(--slate-600);">{{ $name }}</span>
                        <span class="badge {{ $ok ? 'badge-green' : 'badge-red' }}"
                              style="font-size:0.65rem;display:flex;align-items:center;gap:0.3rem;">
                            <span style="width:5px;height:5px;border-radius:50%;background:{{ $ok ? '#16a34a' : '#dc2626' }};"></span>
                            {{ $status }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

</div>
@endsection