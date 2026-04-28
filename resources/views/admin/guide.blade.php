@extends('admin.layout')
@section('page-title', 'Panduan Penggunaan')

@section('content')
<div class="space-y-5 fade-up">

    <div>
        <div class="section-label mb-1">Dokumentasi</div>
        <h1 class="display-heading" style="font-size:1.35rem;">Panduan Penggunaan SIPADU</h1>
    </div>

    {{-- Tab nav --}}
    <div style="display:flex;gap:0.5rem;border-bottom:2px solid var(--slate-200);" x-data="{ tab: 'overview' }">
        @foreach([
            ['overview',  'Gambaran Umum'],
            ['fields',    'Jenis Field'],
            ['templates', 'Template Dokumen'],
            ['autofill',  'Autofill & Loop'],
            ['admin',     'Panduan Admin'],
        ] as [$key, $lbl])
        <button type="button"
            @click="tab = '{{ $key }}'"
            :style="tab === '{{ $key }}' ? 'border-bottom:2px solid var(--navy-600);color:var(--navy-700);margin-bottom:-2px;background:rgba(42,82,152,0.05);' : ''"
            style="padding:0.55rem 1rem;font-size:0.8rem;font-weight:600;color:var(--slate-400);background:transparent;border:none;border-radius:8px 8px 0 0;cursor:pointer;font-family:var(--font-body);transition:all 0.15s;"
            onmouseover="if(!this.style.borderBottom) this.style.color='var(--slate-600)'"
            onmouseout="if(!this.style.borderBottom) this.style.color='var(--slate-400)'">
            {{ $lbl }}
        </button>
        @endforeach
    </div>

    <div x-data="{ tab: 'overview' }">

        {{-- Overview --}}
        <div x-show="tab === 'overview'" x-transition class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="lg:col-span-2 space-y-4">
                <div class="glass-card rounded-2xl p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <div style="width:3px;height:16px;background:linear-gradient(180deg,var(--gold-500),var(--gold-300));border-radius:2px;"></div>
                        <h2 style="font-size:0.9rem;font-weight:700;color:var(--navy-800);">Apa itu SIPADU?</h2>
                    </div>
                    <p style="font-size:0.83rem;color:var(--slate-600);line-height:1.7;">
                        SIPADU (<em>Sistem Administrasi Persuratan</em>) adalah platform web untuk pembuatan dokumen dan surat resmi secara otomatis di lingkungan Dinas Pekerjaan Umum dan Penataan Ruang Daerah Kota Tomohon.
                    </p>
                    <p style="font-size:0.83rem;color:var(--slate-600);line-height:1.7;margin-top:0.75rem;">
                        Dengan SIPADU, staf hanya perlu mengisi form yang tersedia — sistem akan mengisi template Word atau Excel secara otomatis, termasuk data nama, NIP, jabatan, dan pangkat dari database pegawai yang sudah dikelola oleh Admin.
                    </p>
                </div>

                <div class="glass-card rounded-2xl p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <div style="width:3px;height:16px;background:linear-gradient(180deg,var(--gold-500),var(--gold-300));border-radius:2px;"></div>
                        <h2 style="font-size:0.9rem;font-weight:700;color:var(--navy-800);">Cara Kerja</h2>
                    </div>
                    <div class="space-y-0">
                        @foreach([
                            ['Admin', 'Admin menyiapkan template (.docx/.xlsx) dengan placeholder Jinja2 seperti {{nama}} atau {{nip}}.'],
                            ['Admin', 'Admin mendefinisikan field-field form yang diperlukan beserta konfigurasinya.'],
                            ['Staff', 'Staff membuka halaman utama, memilih jenis dokumen dari dropdown.'],
                            ['Staff', 'Staff mengisi form — bisa gunakan autofill untuk isi data pegawai otomatis.'],
                            ['Sistem', 'Sistem merender template menggunakan data form, menghasilkan file .docx atau .xlsx.'],
                            ['Staff', 'Staff mengunduh dokumen dan/atau melihat preview PDF di browser.'],
                        ] as $i => [$actor, $step])
                        <div style="display:flex;gap:0.75rem;padding:0.6rem 0;border-bottom:1px solid rgba(42,82,152,0.06);">
                            <div style="width:22px;height:22px;border-radius:50%;background:{{ $actor === 'Admin' ? 'var(--navy-700)' : ($actor === 'Staff' ? 'var(--gold-500)' : 'rgba(42,82,152,0.15)') }};display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:0.1rem;">
                                <span style="font-size:0.6rem;font-weight:700;color:{{ $actor === 'Admin' ? '#fff' : ($actor === 'Staff' ? '#0d1526' : 'var(--navy-500)') }};">{{ $i + 1 }}</span>
                            </div>
                            <div>
                                <span class="badge {{ $actor === 'Admin' ? 'badge-navy' : ($actor === 'Staff' ? 'badge-gold' : 'badge-gray') }}" style="font-size:0.6rem;margin-bottom:0.2rem;">{{ $actor }}</span>
                                <p style="font-size:0.8rem;color:var(--slate-600);line-height:1.5;">{{ $step }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Quick ref --}}
            <div class="space-y-3">
                <div class="glass-card rounded-2xl p-4">
                    <div class="section-label mb-3">Level Akses</div>
                    @foreach([
                        ['Guest','Lihat dan buat dokumen publik saja.','badge-gray'],
                        ['Staff','Akses semua jenis dokumen.','badge-gold'],
                        ['Admin','Kelola sistem: template, field, user, data pegawai.','badge-navy'],
                    ] as [$role, $desc, $badge])
                    <div style="padding:0.5rem 0;border-bottom:1px solid var(--slate-100);">
                        <span class="badge {{ $badge }}" style="font-size:0.65rem;margin-bottom:0.25rem;">{{ $role }}</span>
                        <p style="font-size:0.75rem;color:var(--slate-500);line-height:1.4;">{{ $desc }}</p>
                    </div>
                    @endforeach
                </div>
                <div style="background:rgba(201,168,76,0.08);border:1px solid rgba(201,168,76,0.2);border-radius:12px;padding:1rem;">
                    <p style="font-size:0.72rem;font-weight:700;color:#7a5f1a;margin-bottom:0.5rem;">⚠ Penting</p>
                    <ul style="font-size:0.73rem;color:#7a5f1a;line-height:1.6;">
                        <li>• File hasil generate <strong>otomatis dihapus</strong> dari server setelah beberapa menit.</li>
                        <li>• Segera unduh setelah dokumen berhasil dibuat.</li>
                        <li>• Data pegawai hanya bisa diubah oleh Admin.</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Field types --}}
        <div x-show="tab === 'fields'" x-transition class="glass-card rounded-2xl overflow-hidden">
            <table class="sipadu-table">
                <thead>
                    <tr>
                        <th>Tipe Field</th>
                        <th>Placeholder Template</th>
                        <th>Keterangan</th>
                        <th>Contoh Output</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach([
                        ['text',            '{{ "{{field_key}}" }}',        'Input teks bebas satu baris.',                       'Dinas PUPRD Kota Tomohon'],
                        ['textarea',        '{{ "{{field_key}}" }}',        'Input teks panjang multi-baris.',                    'Kegiatan pembangunan jalan...'],
                        ['number',          '{{ "{{field_key}}" }}',        'Input angka (integer atau desimal).',                 '12'],
                        ['date',            '{{ "{{field_key}}" }}',        'Pilih tanggal — diformat ke bahasa Indonesia.',      '01 Januari 2025'],
                        ['select',          '{{ "{{field_key}}" }}',        'Pilih satu dari daftar opsi yang ditentukan.',       'Pembangunan'],
                        ['checkbox',        '{{ "{{field_key}}" }}',        'Nilai boolean: "Ya" / "" (kosong).',                 'Ya'],
                        ['loop_staff',      '{{ "{{#loop_key}}" }} ... {{ "{{/loop_key}}" }}', 'Blok perulangan untuk daftar staff yang dipilih.',   'Budi (NIP 123) ...'],
                        ['loop_official',   '{{ "{{#loop_key}}" }} ... {{ "{{/loop_key}}" }}', 'Blok perulangan untuk daftar pejabat yang dipilih.', 'Kepala Dinas...'],
                        ['repeating_group', 'Array loop di template.',      'Grup field yang bisa ditambah per baris dinamis.',   'Row 1: A, B; Row 2: C, D'],
                        ['heading',         '(tidak ada)',                   'Label pemisah seksi — tidak dikirim ke template.',   '—'],
                    ] as [$type, $placeholder, $desc, $example])
                    <tr>
                        <td><code style="font-family:var(--font-mono);font-size:0.72rem;background:var(--slate-100);padding:0.15rem 0.45rem;border-radius:4px;color:var(--navy-600);">{{ $type }}</code></td>
                        <td><code style="font-family:var(--font-mono);font-size:0.7rem;color:var(--slate-500);">{!! $placeholder !!}</code></td>
                        <td style="font-size:0.78rem;color:var(--slate-600);">{{ $desc }}</td>
                        <td style="font-size:0.75rem;color:var(--slate-400);font-style:italic;">{{ $example }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Templates --}}
        <div x-show="tab === 'templates'" x-transition class="glass-card rounded-2xl p-5">
            <div class="flex items-center gap-2 mb-4">
                <div style="width:3px;height:16px;background:linear-gradient(180deg,var(--gold-500),var(--gold-300));border-radius:2px;"></div>
                <h2 style="font-size:0.9rem;font-weight:700;color:var(--navy-800);">Membuat Template Dokumen</h2>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <div>
                    <p style="font-size:0.75rem;font-weight:700;color:var(--navy-600);letter-spacing:0.04em;text-transform:uppercase;margin-bottom:0.5rem;">Format .docx (Word)</p>
                    <ul style="font-size:0.8rem;color:var(--slate-600);line-height:1.8;space-y:0.25rem;">
                        <li>• Buat file Word normal dengan konten surat/dokumen.</li>
                        <li>• Sisipkan placeholder <code style="background:var(--slate-100);padding:0.1rem 0.3rem;border-radius:3px;font-size:0.73rem;">{{"{{"}}nama_field{{"}}"}}</code> di posisi yang sesuai.</li>
                        <li>• Untuk loop: gunakan <code style="background:var(--slate-100);padding:0.1rem 0.3rem;border-radius:3px;font-size:0.73rem;">{{"{{"}}#key{{"}}"}} ... {{"{{"}}/ key{{"}}"}}</code>.</li>
                        <li>• Simpan sebagai <code style="background:var(--slate-100);padding:0.1rem 0.3rem;border-radius:3px;font-size:0.73rem;">.docx</code>, bukan .doc.</li>
                        <li>• Upload via halaman Edit Jenis Dokumen di Admin Panel.</li>
                    </ul>
                </div>
                <div>
                    <p style="font-size:0.75rem;font-weight:700;color:var(--navy-600);letter-spacing:0.04em;text-transform:uppercase;margin-bottom:0.5rem;">Format .xlsx (Excel)</p>
                    <ul style="font-size:0.8rem;color:var(--slate-600);line-height:1.8;">
                        <li>• Buat file Excel dengan struktur tabel yang diinginkan.</li>
                        <li>• Placeholder di dalam cell: <code style="background:var(--slate-100);padding:0.1rem 0.3rem;border-radius:3px;font-size:0.73rem;">{{"{{"}}nama_field{{"}}"}}</code>.</li>
                        <li>• Sistem akan menggantikan placeholder dengan nilai dari form.</li>
                        <li>• Gunakan sheet pertama sebagai template utama.</li>
                    </ul>
                </div>
            </div>
            <div style="margin-top:1.25rem;background:rgba(42,82,152,0.05);border:1px solid rgba(42,82,152,0.12);border-radius:8px;padding:0.9rem;">
                <p style="font-size:0.75rem;font-weight:700;color:var(--navy-700);margin-bottom:0.5rem;">Contoh placeholder untuk surat resmi:</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @foreach(['nama','nip','jabatan','pangkat','unit','tanggal','nomor_surat','perihal','tujuan'] as $ph)
                    <code style="font-family:var(--font-mono);font-size:0.72rem;background:rgba(42,82,152,0.08);border:1px solid rgba(42,82,152,0.12);padding:0.2rem 0.5rem;border-radius:5px;color:var(--navy-600);display:block;">
                        {{"{{"}}{{ $ph }}{{"}}"}}
                    </code>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Autofill --}}
        <div x-show="tab === 'autofill'" x-transition class="glass-card rounded-2xl p-5">
            <div class="flex items-center gap-2 mb-4">
                <div style="width:3px;height:16px;background:linear-gradient(180deg,var(--gold-500),var(--gold-300));border-radius:2px;"></div>
                <h2 style="font-size:0.9rem;font-weight:700;color:var(--navy-800);">Fitur Autofill & Loop</h2>
            </div>
            <p style="font-size:0.83rem;color:var(--slate-600);line-height:1.6;margin-bottom:1rem;">
                Autofill memungkinkan staf memilih nama pegawai dari dropdown, dan secara otomatis semua field terkait (NIP, jabatan, pangkat, unit) terisi tanpa perlu mengetik manual.
            </p>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div>
                    <p style="font-size:0.75rem;font-weight:700;color:var(--navy-600);letter-spacing:0.04em;text-transform:uppercase;margin-bottom:0.5rem;">Cara Konfigurasi Autofill (Admin)</p>
                    <ol style="font-size:0.8rem;color:var(--slate-600);line-height:1.8;list-style:decimal;padding-left:1.1rem;">
                        <li>Buka halaman Fields untuk jenis dokumen.</li>
                        <li>Pada field yang ingin diautofill (misal "Nama"), set <code style="background:var(--slate-100);padding:0.1rem 0.3rem;border-radius:3px;font-size:0.72rem;">staff_autofill_column</code> ke <code style="background:var(--slate-100);padding:0.1rem 0.3rem;border-radius:3px;font-size:0.72rem;">staff_name</code>.</li>
                        <li>Set <code style="background:var(--slate-100);padding:0.1rem 0.3rem;border-radius:3px;font-size:0.72rem;">autofill_role</code> ke nama slot (misalnya <code style="background:var(--slate-100);padding:0.1rem 0.3rem;border-radius:3px;font-size:0.72rem;">penandatangan</code>).</li>
                        <li>Buat slot di tab "Slots" dengan key yang sama dengan autofill_role.</li>
                    </ol>
                </div>
                <div>
                    <p style="font-size:0.75rem;font-weight:700;color:var(--navy-600);letter-spacing:0.04em;text-transform:uppercase;margin-bottom:0.5rem;">Kolom Autofill yang Tersedia</p>
                    <div class="space-y-1">
                        @foreach(['staff_name → nama lengkap','nip → Nomor Induk Pegawai','position → jabatan','rank → pangkat/golongan','unit → unit kerja','email → alamat email','phone → nomor telepon'] as $col)
                        <div style="font-size:0.77rem;display:flex;gap:0.5rem;align-items:center;">
                            <svg style="width:10px;height:10px;color:var(--gold-500);flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            <code style="font-family:var(--font-mono);background:var(--slate-100);padding:0.1rem 0.35rem;border-radius:3px;color:var(--navy-600);font-size:0.7rem;">{{ explode(' → ', $col)[0] }}</code>
                            <span style="color:var(--slate-500);">{{ explode(' → ', $col)[1] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Admin guide --}}
        <div x-show="tab === 'admin'" x-transition class="space-y-4">
            @foreach([
                ['Menambah Jenis Dokumen Baru', [
                    'Siapkan file template (.docx atau .xlsx) dengan placeholder Jinja2.',
                    'Buka Admin Panel → Jenis Dokumen → Tambah Dokumen.',
                    'Isi nama, key (unik, huruf kecil, tanpa spasi), level akses, dan format file.',
                    'Upload file template.',
                    'Setelah disimpan, buka halaman "Fields" untuk menambahkan field-field form.',
                ]],
                ['Mengelola Data Pegawai', [
                    'Siapkan file Excel dengan kolom: staff_name, nip, position, rank, unit, email, phone.',
                    'Buka Admin Panel → Data Staff (atau Data Pejabat untuk pejabat).',
                    'Upload file Excel — data lama akan digantikan seluruhnya.',
                    'Verifikasi data di tabel preview yang muncul.',
                ]],
                ['Mengelola Pengguna', [
                    'Buka Admin Panel → Pengguna.',
                    'Cari pengguna menggunakan fitur pencarian.',
                    'Ubah role melalui dropdown di kolom "Aksi" — perubahan langsung tersimpan.',
                    'Hapus pengguna yang tidak aktif (aksi tidak dapat dibatalkan).',
                ]],
            ] as [$title, $steps])
            <div class="glass-card rounded-2xl p-5">
                <div class="flex items-center gap-2 mb-3">
                    <div style="width:3px;height:16px;background:linear-gradient(180deg,var(--gold-500),var(--gold-300));border-radius:2px;"></div>
                    <h2 style="font-size:0.88rem;font-weight:700;color:var(--navy-800);">{{ $title }}</h2>
                </div>
                <ol style="font-size:0.8rem;color:var(--slate-600);line-height:1.8;list-style:decimal;padding-left:1.1rem;">
                    @foreach($steps as $step)<li>{{ $step }}</li>@endforeach
                </ol>
            </div>
            @endforeach
        </div>

    </div>
</div>
@endsection