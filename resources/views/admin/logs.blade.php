@extends('admin.layout')
@section('page-title', 'Riwayat Dokumen')

@section('content')
<div class="space-y-4 fade-up">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <div class="section-label mb-1">Admin Panel</div>
            <h1 class="display-heading" style="font-size:1.35rem;">Riwayat Dokumen</h1>
        </div>
        <span class="badge badge-navy">{{ $logs->total() ?? count($logs) }} entri</span>
    </div>

    {{-- Filter bar --}}
    <div class="glass-card rounded-xl px-4 py-3 fade-up fade-up-1">
        <form method="GET" action="{{ route('admin.logs') }}" class="flex flex-wrap gap-2 items-center">
            <div class="flex-1 min-w-48 relative">
                <svg style="position:absolute;left:0.65rem;top:50%;transform:translateY(-50%);width:14px;height:14px;color:var(--slate-300);"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-input" style="padding-left:2rem;"
                       placeholder="Cari nama dokumen atau pengguna...">
            </div>
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="form-input" style="width:auto;" title="Dari tanggal">
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="form-input" style="width:auto;" title="Sampai tanggal">
            <select name="file_type" class="form-input" style="width:auto;cursor:pointer;">
                <option value="">Semua Tipe</option>
                @foreach(['docx','xlsx','pdf'] as $ft)
                    <option value="{{ $ft }}" {{ request('file_type') === $ft ? 'selected' : '' }}>
                        {{ strtoupper($ft) }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary" style="padding:0.6rem 1rem;">Filter</button>
            @if(request()->hasAny(['search','date_from','date_to','file_type']))
                <a href="{{ route('admin.logs') }}" class="btn-outline-navy" style="padding:0.6rem 1rem;">Reset</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="glass-card rounded-2xl overflow-hidden fade-up fade-up-2">
        <div class="overflow-x-auto">
            <table class="sipadu-table">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Jenis Dokumen</th>
                        <th>Dibuat Oleh</th>
                        <th>Tipe File</th>
                        <th>Waktu</th>
                        <th>Detail</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td style="color:var(--slate-300);font-size:0.75rem;font-family:var(--font-mono);">
                            {{ $logs->firstItem() + $loop->index }}
                        </td>
                        <td>
                            <span style="font-weight:600;color:var(--slate-800);font-size:0.82rem;">
                                {{ $log->document_type_name ?? '—' }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.5rem;">
                                <div style="width:22px;height:22px;border-radius:50%;background:linear-gradient(135deg,var(--navy-600),var(--navy-400));display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <span style="color:#fff;font-size:0.6rem;font-weight:700;">
                                        {{ strtoupper(substr($log->user->name ?? 'S', 0, 1)) }}
                                    </span>
                                </div>
                                <span style="font-size:0.8rem;color:var(--slate-600);">{{ $log->user->name ?? 'Sistem' }}</span>
                            </div>
                        </td>
                        <td>
                            @php
                                $fileType = strtolower($log->file_type ?? 'docx');
                                $typeBadge = ['docx' => 'badge-navy', 'xlsx' => 'badge-green', 'pdf' => 'badge-red'][$fileType] ?? 'badge-gray';
                            @endphp
                            <span class="badge {{ $typeBadge }}" style="font-size:0.65rem;text-transform:uppercase;">
                                {{ strtoupper($fileType) }}
                            </span>
                        </td>
                        <td style="font-size:0.75rem;white-space:nowrap;">
                            <div style="color:var(--slate-700);">{{ $log->created_at->format('d M Y') }}</div>
                            <div style="color:var(--slate-400);font-family:var(--font-mono);font-size:0.7rem;">{{ $log->created_at->format('H:i') }}</div>
                        </td>
                        <td>
                            @if($log->form_data)
                                <button type="button"
                                    onclick="showDetail({{ $loop->index }})"
                                    style="padding:0.25rem 0.65rem;border-radius:6px;font-size:0.72rem;font-weight:500;border:1px solid var(--navy-100);background:transparent;color:var(--navy-600);cursor:pointer;transition:all 0.15s;font-family:var(--font-body);"
                                    onmouseover="this.style.background='var(--navy-100)'"
                                    onmouseout="this.style.background='transparent'">
                                    Data →
                                </button>
                                <div id="detail-{{ $loop->index }}" class="hidden">
                                    {{ json_encode($log->form_data) }}
                                </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-10" style="color:var(--slate-300);">
                            Tidak ada riwayat dokumen.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($logs instanceof \Illuminate\Pagination\LengthAwarePaginator && $logs->hasPages())
        <div style="padding:0.75rem 1.25rem;border-top:1px solid var(--slate-100);display:flex;align-items:center;justify-content:space-between;">
            <span style="font-size:0.75rem;color:var(--slate-400);">
                Menampilkan {{ $logs->firstItem() }}–{{ $logs->lastItem() }} dari {{ $logs->total() }}
            </span>
            {{ $logs->withQueryString()->links('vendor.pagination.simple-sipadu') }}
        </div>
        @endif
    </div>

</div>

{{-- Detail modal --}}
<div id="detail-modal" class="sipadu-modal-bg hidden" onclick="if(event.target===this) closeDetail()">
    <div class="sipadu-modal w-full max-w-lg mx-4">
        <div style="padding:1rem 1.25rem;border-bottom:1px solid var(--slate-200);display:flex;align-items:center;justify-content:space-between;">
            <h3 style="font-size:0.9rem;font-weight:700;color:var(--navy-800);">Data Form Dokumen</h3>
            <button onclick="closeDetail()"
                style="width:26px;height:26px;border-radius:6px;border:1px solid var(--slate-200);background:transparent;cursor:pointer;color:var(--slate-400);font-size:0.9rem;transition:all 0.2s;"
                onmouseover="this.style.background='var(--slate-100)'"
                onmouseout="this.style.background='transparent'">✕</button>
        </div>
        <div id="detail-modal-body" style="padding:1.25rem;max-height:60vh;overflow-y:auto;">
        </div>
    </div>
</div>

<script>
function showDetail(index) {
    const raw = document.getElementById('detail-' + index)?.textContent;
    if (!raw) return;
    let data;
    try { data = JSON.parse(raw); } catch (e) { return; }
    const body = document.getElementById('detail-modal-body');
    body.innerHTML = Object.entries(data).map(([k, v]) => `
        <div style="display:flex;gap:0.75rem;padding:0.45rem 0;border-bottom:1px solid var(--slate-100);">
            <span style="font-size:0.75rem;font-weight:600;color:var(--slate-500);min-width:120px;flex-shrink:0;">${k}</span>
            <span style="font-size:0.78rem;color:var(--slate-800);word-break:break-word;">${Array.isArray(v) ? v.join(', ') : v ?? '—'}</span>
        </div>
    `).join('');
    document.getElementById('detail-modal').classList.remove('hidden');
}
function closeDetail() { document.getElementById('detail-modal').classList.add('hidden'); }
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeDetail(); });
</script>
@endsection