@extends('admin.layout')
@section('page-title', 'Data Staff')

@section('content')
<div class="space-y-4 fade-up">

    <div class="flex items-center justify-between">
        <div>
            <div class="section-label mb-1">Admin Panel</div>
            <h1 class="display-heading" style="font-size:1.35rem;">Data Staff</h1>
        </div>
        <span class="badge badge-navy">{{ count($staffData) }} entri</span>
    </div>

    {{-- Upload section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Upload form --}}
        <div class="glass-card rounded-2xl p-5 fade-up fade-up-1">
            <div class="flex items-center gap-2 mb-4">
                <div style="width:3px;height:16px;background:linear-gradient(180deg,var(--gold-500),var(--gold-300));border-radius:2px;"></div>
                <h2 style="font-size:0.88rem;font-weight:700;color:var(--navy-800);">Upload / Perbarui Data</h2>
            </div>
            <form method="POST" action="{{ route('admin.staff-data.upload') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="form-label">File Excel (.xlsx)</label>
                    <div style="border:2px dashed var(--slate-200);border-radius:10px;padding:1.5rem;text-align:center;transition:border-color 0.2s;cursor:pointer;"
                         id="upload-zone"
                         ondragover="event.preventDefault();this.style.borderColor='var(--navy-400)';this.style.background='rgba(42,82,152,0.04)'"
                         ondragleave="this.style.borderColor='var(--slate-200)';this.style.background='transparent'"
                         ondrop="handleDrop(event,'staff-file')">
                        <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--slate-300);">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        <p style="font-size:0.8rem;color:var(--slate-400);">Drag & drop atau <label for="staff-file" style="color:var(--navy-500);cursor:pointer;font-weight:600;">klik untuk pilih file</label></p>
                        <p style="font-size:0.7rem;color:var(--slate-300);margin-top:0.25rem;" id="staff-file-name">Format: .xlsx</p>
                        <input type="file" name="file" id="staff-file" accept=".xlsx" class="hidden"
                               onchange="document.getElementById('staff-file-name').textContent = this.files[0]?.name ?? 'Format: .xlsx'">
                    </div>
                    @error('file')
                        <p style="font-size:0.75rem;color:#dc2626;margin-top:0.35rem;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Column mapping info --}}
                <div style="background:rgba(42,82,152,0.05);border:1px solid rgba(42,82,152,0.12);border-radius:8px;padding:0.75rem;margin-bottom:1rem;">
                    <p style="font-size:0.72rem;font-weight:700;color:var(--navy-600);margin-bottom:0.4rem;letter-spacing:0.04em;text-transform:uppercase;">Kolom yang diperlukan</p>
                    <div class="grid grid-cols-2 gap-x-3 gap-y-1">
                        @foreach(['staff_name','nip','position','rank','unit','email','phone'] as $col)
                        <div style="font-size:0.72rem;">
                            <code style="background:var(--slate-100);padding:0.1rem 0.35rem;border-radius:3px;color:var(--navy-600);font-family:var(--font-mono);font-size:0.68rem;">{{ $col }}</code>
                        </div>
                        @endforeach
                    </div>
                </div>

                <button type="submit" class="btn-primary w-full justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Upload Data
                </button>
            </form>
        </div>

        {{-- Stats + tips --}}
        <div class="space-y-4 fade-up fade-up-2">
            <div class="stat-card">
                <div class="stat-label mb-1">Total Staff Terdaftar</div>
                <div class="stat-value" style="color:var(--navy-600);">{{ count($staffData) }}</div>
                <div class="stat-label mt-1" style="color:var(--slate-300);font-size:0.68rem;">entri dalam database</div>
            </div>
            <div style="background:rgba(201,168,76,0.07);border:1px solid rgba(201,168,76,0.2);border-radius:10px;padding:0.9rem;">
                <p style="font-size:0.72rem;font-weight:700;color:#7a5f1a;margin-bottom:0.5rem;letter-spacing:0.04em;text-transform:uppercase;">Catatan Upload</p>
                <ul style="font-size:0.73rem;color:#7a5f1a;space-y:0.25rem;line-height:1.6;">
                    <li>• Upload baru akan <strong>menggantikan</strong> seluruh data lama.</li>
                    <li>• Pastikan format kolom sesuai tabel di sebelah kiri.</li>
                    <li>• Baris pertama harus berupa header kolom.</li>
                    <li>• NIP harus unik untuk setiap staff.</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Data table --}}
    @if(count($staffData) > 0)
    <div class="glass-card rounded-2xl overflow-hidden fade-up fade-up-3">
        <div style="padding:0.75rem 1.25rem;border-bottom:1px solid var(--slate-100);display:flex;align-items:center;gap:2;">
            <div style="width:3px;height:16px;background:linear-gradient(180deg,var(--gold-500),var(--gold-300));border-radius:2px;margin-right:0.6rem;"></div>
            <span style="font-size:0.82rem;font-weight:700;color:var(--navy-800);">Daftar Staff (preview 20 pertama)</span>
        </div>
        <div class="overflow-x-auto">
            <table class="sipadu-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>NIP</th>
                        <th>Jabatan</th>
                        <th>Pangkat</th>
                        <th>Unit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(collect($staffData)->take(20) as $i => $staff)
                    <tr>
                        <td style="color:var(--slate-300);font-size:0.75rem;font-family:var(--font-mono);">{{ $i + 1 }}</td>
                        <td style="font-weight:600;color:var(--slate-800);font-size:0.8rem;">{{ $staff['staff_name'] ?? '—' }}</td>
                        <td style="font-family:var(--font-mono);font-size:0.75rem;color:var(--slate-500);">{{ $staff['nip'] ?? '—' }}</td>
                        <td style="font-size:0.78rem;color:var(--slate-600);">{{ $staff['position'] ?? '—' }}</td>
                        <td style="font-size:0.78rem;color:var(--slate-500);">{{ $staff['rank'] ?? '—' }}</td>
                        <td style="font-size:0.78rem;color:var(--slate-500);">{{ $staff['unit'] ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if(count($staffData) > 20)
        <div style="padding:0.65rem 1.25rem;border-top:1px solid var(--slate-100);text-align:center;">
            <span style="font-size:0.75rem;color:var(--slate-400);">
                Menampilkan 20 dari {{ count($staffData) }} entri.
            </span>
        </div>
        @endif
    </div>
    @endif

</div>

<script>
function handleDrop(event, inputId) {
    event.preventDefault();
    const input = document.getElementById(inputId);
    const file = event.dataTransfer.files[0];
    if (file && input) {
        const dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
        const nameEl = document.getElementById(inputId + '-name') ?? document.getElementById('staff-file-name');
        if (nameEl) nameEl.textContent = file.name;
        event.currentTarget.style.borderColor = 'var(--navy-400)';
        event.currentTarget.style.background = 'rgba(42,82,152,0.04)';
    }
}
</script>
@endsection