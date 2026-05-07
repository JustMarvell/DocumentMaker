@extends('admin.layout')

@section('content')
<div class="flex items-start justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Panduan Sistem eDocPUPRD</h1>
        <p class="text-sm text-gray-500 mt-1">
            Panduan penggunaan dan administrasi — Versi 1.0 | 2026 | by. Marvelous Makaluwu
        </p>
    </div>
    <a href="{{ route('admin.guide.download') }}"
       class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 font-medium flex-shrink-0">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
        </svg>
        Unduh Panduan (.docx)
    </a>
</div>
<div class="flex gap-6 items-start">

    {{-- Sticky sidebar TOC --}}
    <aside class="w-56 flex-shrink-0 sticky top-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Daftar Isi</p>
            <nav class="space-y-1 text-xs">
                <a href="#pengenalan"      class="toc-link block py-1 px-2 rounded text-gray-600 hover:bg-blue-50 hover:text-blue-700">Pengenalan Sistem</a>
                <a href="#persyaratan"     class="toc-link block py-1 px-2 rounded text-gray-600 hover:bg-blue-50 hover:text-blue-700">Persyaratan Sistem</a>
                <a href="#panduan-admin"   class="toc-link block py-1 px-2 rounded text-gray-600 hover:bg-blue-50 hover:text-blue-700">Panduan Administrator</a>
                <a href="#akses-admin"     class="toc-link block py-1 px-2 rounded text-gray-600 hover:bg-blue-50 hover:text-blue-700 pl-5">↳ Akses Panel Admin</a>
                <a href="#dashboard"       class="toc-link block py-1 px-2 rounded text-gray-600 hover:bg-blue-50 hover:text-blue-700 pl-5">↳ Dashboard</a>
                <a href="#manajemen-user"  class="toc-link block py-1 px-2 rounded text-gray-600 hover:bg-blue-50 hover:text-blue-700 pl-5">↳ Manajemen Pengguna</a>
                <a href="#data-staff"      class="toc-link block py-1 px-2 rounded text-gray-600 hover:bg-blue-50 hover:text-blue-700 pl-5">↳ Data Staff & Pejabat</a>
                <a href="#jenis-dokumen"   class="toc-link block py-1 px-2 rounded text-gray-600 hover:bg-blue-50 hover:text-blue-700 pl-5">↳ Jenis Dokumen</a>
                <a href="#kelola-field"    class="toc-link block py-1 px-2 rounded text-gray-600 hover:bg-blue-50 hover:text-blue-700 pl-5">↳ Kelola Field</a>
                <a href="#autofill-slots"  class="toc-link block py-1 px-2 rounded text-gray-600 hover:bg-blue-50 hover:text-blue-700 pl-5">↳ Autofill Slots</a>
                <a href="#template"        class="toc-link block py-1 px-2 rounded text-gray-600 hover:bg-blue-50 hover:text-blue-700">Membuat Template</a>
                <a href="#jinja2"          class="toc-link block py-1 px-2 rounded text-gray-600 hover:bg-blue-50 hover:text-blue-700 pl-5">↳ Referensi Jinja2</a>
                <a href="#contoh"          class="toc-link block py-1 px-2 rounded text-gray-600 hover:bg-blue-50 hover:text-blue-700 pl-5">↳ Contoh Template</a>
                <a href="#preview"         class="toc-link block py-1 px-2 rounded text-gray-600 hover:bg-blue-50 hover:text-blue-700">Fitur Preview</a>
                <a href="#scheduler"       class="toc-link block py-1 px-2 rounded text-gray-600 hover:bg-blue-50 hover:text-blue-700">Pembersihan Otomatis</a>
                <a href="#deployment"      class="toc-link block py-1 px-2 rounded text-gray-600 hover:bg-blue-50 hover:text-blue-700">Deployment</a>
                <a href="#digital-signature" class="toc-link block py-1 px-2 rounded text-gray-600 hover:bg-blue-50 hover:text-blue-700">Tanda Tangan Digital</a>
                <a href="#ttd-flow"          class="toc-link block py-1 px-2 rounded text-gray-600 hover:bg-blue-50 hover:text-blue-700 pl-5">↳ Alur TTD</a>
                <a href="#ttd-template"      class="toc-link block py-1 px-2 rounded text-gray-600 hover:bg-blue-50 hover:text-blue-700 pl-5">↳ Template TTD</a>
                <a href="#nomor-surat"       class="toc-link block py-1 px-2 rounded text-gray-600 hover:bg-blue-50 hover:text-blue-700">Nomor Surat Otomatis</a>
                <a href="#scan-template"     class="toc-link block py-1 px-2 rounded text-gray-600 hover:bg-blue-50 hover:text-blue-700">Scan Template</a>
                <a href="#pdf-preview"       class="toc-link block py-1 px-2 rounded text-gray-600 hover:bg-blue-50 hover:text-blue-700">Preview PDF (iLoveAPI)</a>
                <a href="#troubleshooting" class="toc-link block py-1 px-2 rounded text-gray-600 hover:bg-blue-50 hover:text-blue-700">Troubleshooting</a>
            </nav>
        </div>
    </aside>

    <div class="flex-1 min-w-0 space-y-8">
        <section id="pengenalan" class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold text-blue-700 border-b border-blue-100 pb-2 mb-4">Pengenalan Sistem</h2>

            <p class="text-sm text-gray-700 mb-4">
                <strong>{{ config('app.name') }}</strong> (Sistem Pembuatan Dokumen Digital) adalah aplikasi web berbasis Laravel yang dirancang khusus untuk
                <strong>DINAS PEKERJAAN UMUM DAN PENATAAN RUANG DAERAH KOTA TOMOHON</strong>.
                Sistem ini memungkinkan pegawai untuk membuat surat dan dokumen resmi secara otomatis berdasarkan template yang telah disiapkan oleh administrator.
            </p>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Fitur Utama</p>
                    <ul class="space-y-1 text-sm text-gray-700">
                        <li class="flex items-start gap-2"><span class="text-blue-500 mt-0.5">•</span> Pembuatan dokumen dari template .docx dan .xlsx</li>
                        <li class="flex items-start gap-2"><span class="text-blue-500 mt-0.5">•</span> Manajemen template dari panel admin</li>
                        <li class="flex items-start gap-2"><span class="text-blue-500 mt-0.5">•</span> Autofill otomatis dari database staff dan pejabat</li>
                        <li class="flex items-start gap-2"><span class="text-blue-500 mt-0.5">•</span> Dukungan data berulang (loop) untuk daftar peserta</li>
                        <li class="flex items-start gap-2"><span class="text-blue-500 mt-0.5">•</span> 3 tingkatan akses: Guest, Staff, Admin</li>
                        <li class="flex items-start gap-2"><span class="text-blue-500 mt-0.5">•</span> Preview dokumen via LibreOffice PDF</li>
                        <li class="flex items-start gap-2"><span class="text-blue-500 mt-0.5">•</span> Riwayat pembuatan dokumen</li>
                        <li class="flex items-start gap-2"><span class="text-blue-500 mt-0.5">•</span> Pembersihan file otomatis</li>
                    </ul>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Alur Kerja Sistem</p>
                    <ol class="space-y-1 text-sm text-gray-700">
                        <li class="flex items-start gap-2"><span class="text-blue-600 font-bold">1.</span> Admin menyiapkan template dengan placeholder Jinja2</li>
                        <li class="flex items-start gap-2"><span class="text-blue-600 font-bold">2.</span> Admin mengunggah template dan mendefinisikan field form</li>
                        <li class="flex items-start gap-2"><span class="text-blue-600 font-bold">3.</span> User memilih dokumen dan mengisi form</li>
                        <li class="flex items-start gap-2"><span class="text-blue-600 font-bold">4.</span> Script Python mengisi template dengan data user</li>
                        <li class="flex items-start gap-2"><span class="text-blue-600 font-bold">5.</span> Dokumen tersedia untuk diunduh dan/atau dipreview</li>
                    </ol>
                </div>
            </div>
        </section>
        <section id="persyaratan" class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold text-blue-700 border-b border-blue-100 pb-2 mb-4">Persyaratan Sistem</h2>

            <div class="overflow-x-auto mb-4">
                <table class="w-full text-sm">
                    <thead class="bg-blue-700 text-white">
                        <tr>
                            <th class="px-3 py-2 text-left font-medium">Komponen</th>
                            <th class="px-3 py-2 text-left font-medium">Minimum</th>
                            <th class="px-3 py-2 text-left font-medium">Direkomendasikan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <!-- Vell note : Storage probly fine if just 512 MB -->
                         <!-- project file is abt 200+ MB -->
                          <!-- RAM is also probly fine with just 500 MB but further testing is still needed. -->
                        @foreach ([['PHP','8.2','8.3+'],['Laravel','11.x','11.x'],['Python','3.10','3.12'],['Node.js','18.x','20.x'],['Database','SQLite','SQLite/MySQL'],['LibreOffice','7.x (preview)','24.x'],['RAM','1 GB','2 GB+'],['Storage','1 GB','2 GB+']] as $row)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 font-medium text-gray-700">{{ $row[0] }}</td>
                            <td class="px-3 py-2 text-gray-500">{{ $row[1] }}</td>
                            <td class="px-3 py-2 text-gray-500">{{ $row[2] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Library Python (venv)</p>
            <x-code>venv/bin/pip install docxtpl openpyxl jinja2</x-code>

            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 mt-3">Instalasi LibreOffice (untuk Preview)</p>
            <x-code>
                sudo apt update && sudo apt install libreoffice<br>libreoffice --version   # verifikasi instalasi
            </x-code>
        </section>
        <section id="panduan-admin" class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold text-blue-700 border-b border-blue-100 pb-2 mb-4">Panduan Administrator</h2>

            <div id="akses-admin" class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Akses Panel Admin</h3>
                <p class="text-sm text-gray-700 mb-2">
                    Panel admin dapat diakses melalui <code class="bg-gray-100 px-1 rounded">/admin/dashboard</code>.
                    Hanya akun dengan role <strong>Admin</strong> yang dapat mengakses halaman ini.
                    Akun admin pertama harus diatur langsung melalui database:
                </p>
                <x-code>
                    php artisan db<br>UPDATE users SET role = 'admin' WHERE email = 'email_anda@gmail.com';<br>.quit
                </x-code>
            </div>
            <div id="dashboard" class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Dashboard</h3>
                <p class="text-sm text-gray-700 mb-2">Dashboard admin menampilkan statistik penggunaan sistem:</p>
                <ul class="text-sm text-gray-700 space-y-1 mb-2">
                    <li class="flex items-start gap-2"><span class="text-blue-500">•</span> Total dokumen berhasil dibuat dan total yang gagal</li>
                    <li class="flex items-start gap-2"><span class="text-blue-500">•</span> Total pengguna terdaftar</li>
                    <li class="flex items-start gap-2"><span class="text-blue-500">•</span> Tabel jumlah dokumen per jenis</li>
                    <li class="flex items-start gap-2"><span class="text-blue-500">•</span> 10 aktivitas pembuatan dokumen terbaru</li>
                </ul>
            </div>

            <div id="manajemen-user" class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Manajemen Pengguna</h3>
                <p class="text-sm text-gray-700 mb-3">Menu Pengguna menampilkan semua akun terdaftar. Tabel tingkatan akses:</p>
                <div class="overflow-x-auto mb-3">
                    <table class="w-full text-sm">
                        <thead class="bg-blue-700 text-white">
                            <tr>
                                <th class="px-3 py-2 text-left font-medium">Role</th>
                                <th class="px-3 py-2 text-left font-medium">Akses Dokumen</th>
                                <th class="px-3 py-2 text-left font-medium">Panel Admin</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            <tr><td class="px-3 py-2 font-medium">Guest</td><td class="px-3 py-2 text-gray-600">Hanya dokumen dengan akses "Guest"</td><td class="px-3 py-2 text-gray-600">Tidak</td></tr>
                            <tr class="bg-gray-50"><td class="px-3 py-2 font-medium">Staff</td><td class="px-3 py-2 text-gray-600">Semua dokumen aktif</td><td class="px-3 py-2 text-gray-600">Tidak</td></tr>
                            <tr><td class="px-3 py-2 font-medium">Admin</td><td class="px-3 py-2 text-gray-600">Semua dokumen aktif</td><td class="px-3 py-2 text-gray-600">Ya — Akses Penuh</td></tr>
                        </tbody>
                    </table>
                </div>
                <p class="text-sm text-gray-700 mb-1 font-medium">Mengubah Role Pengguna:</p>
                <ol class="text-sm text-gray-700 space-y-1 list-decimal list-inside">
                    <li>Buka Admin Panel → Pengguna</li>
                    <li>Temukan nama pengguna yang ingin diubah</li>
                    <li>Pada kolom "Ubah Role", pilih role baru dari dropdown</li>
                    <li>Klik Simpan — perubahan berlaku segera</li>
                </ol>
                <div class="mt-2 bg-yellow-50 border border-yellow-200 rounded px-3 py-2 text-xs text-yellow-800">
                    <strong>Catatan:</strong> Admin tidak dapat mengubah role akun mereka sendiri.
                </div>
            </div>

            <div id="data-staff" class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Manajemen Data Staff & Pejabat</h3>
                <p class="text-sm text-gray-700 mb-3">
                    Menu <strong>Data Staff</strong> menyimpan informasi pegawai untuk fitur autofill.
                    Menu <strong>Data Pejabat</strong> memiliki struktur identik, diperuntukkan untuk pejabat struktural (Kepala Dinas, Sekretaris, dsb.)
                </p>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-blue-700 text-white">
                            <tr>
                                <th class="px-3 py-2 text-left font-medium">Kolom Database</th>
                                <th class="px-3 py-2 text-left font-medium">Label</th>
                                <th class="px-3 py-2 text-left font-medium">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach([['staff_name','Nama','Nama lengkap pegawai'],['nip','NIP','Nomor Induk Pegawai (unik)'],['email','Email','Alamat email (unik)'],['rank','Jabatan / Gol. Pangkat','Pangkat dan golongan ruang'],['position','Posisi','Jabatan fungsional/struktural'],['work_unit','Unit Kerja','Bidang/Subbidang tempat bertugas'],['phone_number','No. HP','Nomor telepon (opsional)']] as $row)
                            <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}">
                                <td class="px-3 py-2 font-mono text-xs text-blue-700">{{ $row[0] }}</td>
                                <td class="px-3 py-2 font-medium text-gray-700">{{ $row[1] }}</td>
                                <td class="px-3 py-2 text-gray-500">{{ $row[2] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="jenis-dokumen" class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Manajemen Jenis Dokumen</h3>
                <p class="text-sm text-gray-700 mb-3">Menu Jenis Dokumen adalah pusat kendali untuk semua template dokumen.</p>

                <div class="grid grid-cols-2 gap-3 mb-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Informasi yang Ditampilkan</p>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-blue-700 text-white"><tr><th class="px-2 py-1.5 text-left font-medium">Kolom</th><th class="px-2 py-1.5 text-left font-medium">Keterangan</th></tr></thead>
                                <tbody class="divide-y divide-gray-100 text-xs">
                                    @foreach([['Nama','Nama dokumen untuk pengguna'],['Key','ID unik internal'],['Tipe File','DOCX atau XLSX'],['Akses','Guest atau Staff'],['Fields','Jumlah field terdefinisi'],['Dibuat','Total dokumen dibuat'],['Status','Aktif atau Nonaktif'],['Preview','Toggle on/off preview PDF']] as $row)
                                    <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}"><td class="px-2 py-1.5 font-medium text-gray-700">{{ $row[0] }}</td><td class="px-2 py-1.5 text-gray-500">{{ $row[1] }}</td></tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Aksi yang Tersedia</p>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-blue-700 text-white"><tr><th class="px-2 py-1.5 text-left font-medium">Tombol</th><th class="px-2 py-1.5 text-left font-medium">Fungsi</th></tr></thead>
                                <tbody class="divide-y divide-gray-100 text-xs">
                                    @foreach([['Kelola Field','Buka manajemen field'],['Toggle Status','Aktifkan/Nonaktifkan'],['Toggle Preview','On/Off fitur preview PDF'],['Hapus','Hapus permanen beserta field-nya']] as $row)
                                    <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}"><td class="px-2 py-1.5 font-medium text-gray-700">{{ $row[0] }}</td><td class="px-2 py-1.5 text-gray-500">{{ $row[1] }}</td></tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <p class="text-sm font-medium text-gray-700 mb-1">Menambahkan Template Baru:</p>
                <ol class="text-sm text-gray-700 space-y-1 list-decimal list-inside mb-3">
                    <li>Buka Admin Panel → Jenis Dokumen</li>
                    <li>Klik "+ Tambah Template Baru"</li>
                    <li>Isi form: Nama Dokumen, Key, Tipe File, Level Akses, File Template (maks. 10MB)</li>
                    <li>Klik "Simpan & Kelola Field" — langsung diarahkan ke halaman Kelola Field</li>
                </ol>
                <p class="text-sm font-medium text-gray-700 mb-1">Re-Upload Template:</p>
                <p class="text-sm text-gray-700 mb-1">Untuk memperbarui file template tanpa mengulang konfigurasi field:</p>
                <ol class="text-sm text-gray-700 space-y-1 list-decimal list-inside">
                    <li>Buka Kelola Field → klik "↑ Re-upload Template"</li>
                    <li>Upload file baru — semua field tetap tersimpan</li>
                </ol>
            </div>

            <div id="kelola-field" class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Kelola Field (Form Fields)</h3>
                <p class="text-sm text-gray-700 mb-3">
                    Halaman Kelola Field menentukan field apa yang ditampilkan pada form. Setiap field harus memiliki
                    <code class="bg-gray-100 px-1 rounded text-xs">field_key</code> yang cocok PERSIS dengan placeholder di template.
                </p>

                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Tipe Field yang Tersedia</p>
                <div class="overflow-x-auto mb-4">
                    <table class="w-full text-sm">
                        <thead class="bg-blue-700 text-white">
                            <tr>
                                <th class="px-3 py-2 text-left font-medium">Tipe Field</th>
                                <th class="px-3 py-2 text-left font-medium">Tampilan di Form</th>
                                <th class="px-3 py-2 text-left font-medium">Cocok Untuk</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach([['text','Input teks satu baris','Nama, alamat, nomor surat'],['textarea','Area teks multi-baris','Keterangan panjang, catatan'],['date','Date picker (kalender)','Tanggal — otomatis format Indonesia'],['number','Input angka','Jumlah hari, nomor urut'],['select','Dropdown pilihan','Pilihan tetap (status, kategori)'],['checkbox','Kotak centang','Ya/Tidak, true/false'],['repeating_group','Tabel dinamis + tombol tambah baris','Data berulang yang diketik manual'],['staff_loop','Daftar checkbox staff (searchable + draggable)','Daftar peserta dari database staff'],['official_loop','Daftar checkbox pejabat (searchable + draggable)','Daftar penandatangan dari database pejabat']] as $row)
                            <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}">
                                <td class="px-3 py-2 font-mono text-xs text-blue-700 font-medium">{{ $row[0] }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ $row[1] }}</td>
                                <td class="px-3 py-2 text-gray-500">{{ $row[2] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Parameter Field</p>
                <div class="overflow-x-auto mb-4">
                    <table class="w-full text-sm">
                        <thead class="bg-blue-700 text-white">
                            <tr>
                                <th class="px-3 py-2 text-left font-medium">Parameter</th>
                                <th class="px-3 py-2 text-left font-medium">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach([['Field Key *','Nama variabel di template. Harus cocok PERSIS dengan placeholder. Hanya huruf kecil, angka, underscore.'],['Label *','Teks judul field yang ditampilkan di form'],['Tipe Field *','Lihat tabel tipe field di atas'],['Opsi','Khusus tipe select: daftar pilihan dipisahkan koma. Contoh: Baik, Cukup, Kurang'],['Label Seksi','Heading pemisah yang muncul di atas field ini (pengelompokan visual)'],['Row Group','Field dengan angka yang sama tampil berdampingan dalam satu baris'],['Staff Autofill Column','Kolom dari staff_data/official_data yang mengisi field ini otomatis'],['Autofill Role','Slot autofill mana yang mengisi field ini (gunakan slot_key)'],['Wajib Diisi','Jika dicentang, user tidak bisa submit tanpa mengisi field ini']] as $row)
                            <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}">
                                <td class="px-3 py-2 font-medium text-gray-700 whitespace-nowrap">{{ $row[0] }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ $row[1] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded px-3 py-2 text-xs text-blue-800 mb-3">
                    <strong>Row Group:</strong> Untuk menampilkan dua field berdampingan (misal: Tanggal Mulai dan Tanggal Selesai),
                    isi Row Group dengan angka yang sama (misal: 1) pada kedua field. Untuk kelompok baru gunakan angka berbeda (2, 3, dst.)
                </div>
            </div>

            <div id="autofill-slots">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Autofill Slots</h3>
                <p class="text-sm text-gray-700 mb-3">
                    Slot autofill menentukan berapa banyak dropdown autofill yang ditampilkan di form.
                    <strong>Satu slot = satu pasang dropdown (Staff + Pejabat).</strong>
                </p>
                <div class="grid grid-cols-2 gap-4 mb-3">
                    <div class="bg-gray-50 rounded p-3">
                        <p class="text-xs font-semibold text-gray-600 mb-2">Kapan Menggunakan Slots</p>
                        <ul class="text-xs text-gray-700 space-y-1">
                            <li>• 1 pegawai → buat 1 slot (key=employee)</li>
                            <li>• Pegawai + penilai → buat 2 slot</li>
                            <li>• Tiga pihak → buat 3 slot, dst.</li>
                        </ul>
                    </div>
                    <div class="bg-gray-50 rounded p-3">
                        <p class="text-xs font-semibold text-gray-600 mb-2">Cara Membuat Slot</p>
                        <ol class="text-xs text-gray-700 space-y-1">
                            <li>1. Kelola Field → "⚙ Kelola Slot Autofill"</li>
                            <li>2. Isi Slot Key (misal: employee)</li>
                            <li>3. Isi Label (misal: Pegawai yang Dinilai)</li>
                            <li>4. Klik "+ Tambah Slot"</li>
                        </ol>
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-700 mb-1">Menghubungkan Field ke Slot:</p>
                <ol class="text-sm text-gray-700 space-y-1 list-decimal list-inside mb-2">
                    <li>Kembali ke halaman Kelola Field</li>
                    <li>Edit field yang ingin diisi otomatis (misal: Nama Pegawai)</li>
                    <li>Set <strong>Staff Autofill Column</strong> ke kolom yang sesuai (misal: staff_name)</li>
                    <li>Set <strong>Autofill Role</strong> ke slot_key yang sudah dibuat (misal: employee)</li>
                    <li>Simpan — field ini sekarang terisi otomatis dari dropdown slot "employee"</li>
                </ol>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-blue-700 text-white">
                            <tr><th class="px-3 py-2 text-left font-medium">Nilai Autofill Column</th><th class="px-3 py-2 text-left font-medium">Data yang Diisi</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach([['staff_name','Nama lengkap'],['nip','NIP'],['rank','Jabatan / Golongan Pangkat'],['position','Posisi / Jabatan Fungsional'],['work_unit','Unit Kerja'],['email','Alamat Email'],['phone_number','Nomor HP']] as $row)
                            <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}">
                                <td class="px-3 py-2 font-mono text-xs text-blue-700">{{ $row[0] }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ $row[1] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        <section id="template" class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold text-blue-700 border-b border-blue-100 pb-2 mb-4">Panduan Membuat Template Dokumen</h2>
            @include('components.video-player', [
                'src'    => 'videos/test1.mp4',
                'title'  => '1. Cara Menambah Template Baru',
            ])

            <div class="bg-red-50 border border-red-200 rounded px-3 py-2 text-sm text-red-800 mb-4">
                <strong>Aturan Terpenting:</strong> Nama variabel dalam template HARUS PERSIS SAMA dengan Field Key yang didefinisikan di panel admin.
                Perbedaan satu huruf pun akan menyebabkan variabel tidak tergantikan.
            </div>

            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Template Word (.docx)</h3>
                    <p class="text-xs text-gray-600 mb-2">Menggunakan library <strong>docxtpl</strong> yang mendukung sintaks Jinja2 penuh di dalam tabel, paragraf, header, footer, dan text box.</p>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Cara Membuat:</p>
                    <ol class="text-xs text-gray-700 space-y-1 list-decimal list-inside mb-2">
                        <li>Buka Microsoft Word atau LibreOffice Writer</li>
                        <li>Buat dokumen dengan format yang diinginkan</li>
                        <li>Ketik placeholder: <code class="bg-gray-100 px-1 rounded">@verbatim{{ nama_variabel }}@endverbatim</code></li>
                        <li>Simpan sebagai <code class="bg-gray-100 px-1 rounded">.docx</code></li>
                        <li>Upload melalui Admin Panel</li>
                    </ol>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Catatan Penting:</p>
                    <ul class="text-xs text-gray-700 space-y-1">
                        <li>• Placeholder bisa di tabel, paragraf, header, footer, text box</li>
                        <li>• Loop dalam tabel: letakkan <code class="bg-gray-100 px-1 rounded">{% for %}</code> di dalam sel tabel</li>
                        <li>• Gunakan <code class="bg-gray-100 px-1 rounded">@verbatim{{ loop.index }}@endverbatim</code> untuk nomor urut otomatis</li>
                        <li>• Format tanggal otomatis dikonversi ke format Indonesia</li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Template Excel (.xlsx)</h3>
                    <p class="text-xs text-gray-600 mb-2">Menggunakan manipulasi XML langsung. Placeholder di sel dan text box.</p>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Cara Membuat:</p>
                    <ol class="text-xs text-gray-700 space-y-1 list-decimal list-inside mb-2">
                        <li>Buat file Excel dengan format yang diinginkan</li>
                        <li>Ketik placeholder di dalam sel yang ingin diisi</li>
                        <li>Untuk loop: gunakan <code class="bg-gray-100 px-1 rounded">{% for %}</code> di sel terpisah</li>
                        <li>Text box: Insert Text Box, ketik placeholder di dalamnya</li>
                        <li>Simpan sebagai <code class="bg-gray-100 px-1 rounded">.xlsx</code> dan upload</li>
                    </ol>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Catatan Penting:</p>
                    <ul class="text-xs text-gray-700 space-y-1">
                        <li>• Gambar di template akan dipertahankan di output</li>
                        <li>• Text box dengan placeholder dirender dengan benar</li>
                        <li>• Merged cells, border, formatting dipertahankan</li>
                        <li>• Loop akan menyisipkan baris baru secara otomatis</li>
                    </ul>
                </div>
            </div>

            <h3 id="jinja2" class="text-sm font-semibold text-gray-700 mb-3">Referensi Variabel Jinja2</h3>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Sintaks Dasar</p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead class="bg-blue-700 text-white"><tr><th class="px-2 py-1.5 text-left">Sintaks</th><th class="px-2 py-1.5 text-left">Kegunaan</th></tr></thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr><td class="px-2 py-1.5 font-mono text-blue-700">@verbatim{{ field_key }}@endverbatim</td><td class="px-2 py-1.5 text-gray-600">Menampilkan nilai variabel</td></tr>
                                <tr class="bg-gray-50"><td class="px-2 py-1.5 font-mono text-blue-700">@verbatim{{ val | upper }}@endverbatim</td><td class="px-2 py-1.5 text-gray-600">HURUF BESAR</td></tr>
                                <!-- WTFFFF WHYYY IS THISS SHOWS UP AS AN ERRORRRRRRRRRRRRR -->
                                 <!-- AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAa -->
                                <tr><td class="px-2 py-1.5 font-mono text-blue-700">@verbatim{{ val | default("") }}@endverbatim</td><td class="px-2 py-1.5 text-gray-600">Nilai default jika kosong</td></tr>
                                <tr class="bg-gray-50"><td class="px-2 py-1.5 font-mono text-blue-700">{% if kondisi %}</td><td class="px-2 py-1.5 text-gray-600">Kondisional</td></tr>
                                <tr><td class="px-2 py-1.5 font-mono text-blue-700">{% for x in list %}</td><td class="px-2 py-1.5 text-gray-600">Pengulangan</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Properti Loop (staff/official_loop)</p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead class="bg-blue-700 text-white"><tr><th class="px-2 py-1.5 text-left">Properti</th><th class="px-2 py-1.5 text-left">Nilai</th></tr></thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach([['peserta.staff_name','Nama lengkap'],['peserta.nip','NIP'],['peserta.rank','Jabatan/Gol. Pangkat'],['peserta.position','Posisi'],['peserta.work_unit','Unit Kerja'],['peserta.phone_number','Nomor HP'],['loop.index','Nomor urut (dari 1)'],['loop.index0','Nomor urut (dari 0)'],['loop.first','True jika item pertama'],['loop.last','True jika item terakhir']] as $row)
                                <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}">
                                    <td class="px-2 py-1.5 font-mono text-blue-700">{{ $row[0] }}</td>
                                    <td class="px-2 py-1.5 text-gray-600">{{ $row[1] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Filter Jinja2 yang Berguna</p>
            <div class="overflow-x-auto mb-4">
                <table class="w-full text-sm">
                    <thead class="bg-blue-700 text-white"><tr><th class="px-3 py-2 text-left font-medium">Filter</th><th class="px-3 py-2 text-left font-medium">Contoh</th><th class="px-3 py-2 text-left font-medium">Hasil</th></tr></thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @foreach([['upper', '@{{ nama | upper }}','JOHN DOE'],['lower','@{{ nama | lower }}','john doe'],['title','@{{ nama | title }}','John Doe'],['default','@{{ nilai | default("N/A") }}','N/A jika kosong'],['length','@{{ daftar | length }}','Jumlah item dalam list'],['first','@{{ daftar | first }}','Item pertama'],['last','@{{ daftar | last }}','Item terakhir'],['join','@{{ list | join(", ") }}','A, B, C']] as $row)
                        <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}">
                            <td class="px-3 py-2 font-mono text-xs text-blue-700">{{ $row[0] }}</td>
                            <td class="px-3 py-2 font-mono text-xs">{{ $row[1] }}</td>
                            <td class="px-3 py-2 text-gray-600">{{ $row[2] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <h3 id="contoh" class="text-sm font-semibold text-gray-700 mb-3">Contoh Template Lengkap</h3>

            {{-- PDF Viewer --}}
            <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm" id="pdf-viewer-container">

                {{-- Tab bar --}}
                <div class="flex gap-0 border-b border-gray-200 bg-gray-50 overflow-x-auto" id="pdf-tab-bar">
                    @foreach([
                        ['surat-tugas', 'Surat Tugas'],
                        ['daftar-hadir', 'Daftar Hadir (Loop)'],
                        ['kondisional', 'Kondisional'],
                        ['loop-excel', 'Loop di Excel'],
                    ] as $i => [$key, $label])
                    <button
                        onclick="switchPdfTab('{{ $key }}')"
                        id="pdf-tab-{{ $key }}"
                        class="pdf-tab-btn px-4 py-2.5 text-xs font-semibold whitespace-nowrap border-b-2 transition-colors
                            {{ $i === 0 ? 'border-blue-600 text-blue-700 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-white' }}">
                        {{ $label }}
                    </button>
                    @endforeach
                </div>

                {{-- Toolbar --}}
                <div class="flex items-center justify-between px-3 py-2 bg-gray-800 gap-2">

                    {{-- Page nav --}}
                    <div class="flex items-center gap-1.5">
                        <button onclick="pdfPrevPage()" id="pdf-prev"
                            class="w-7 h-7 rounded flex items-center justify-center text-gray-300 hover:bg-gray-700 hover:text-white transition disabled:opacity-30"
                            title="Halaman sebelumnya">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <span class="text-xs text-gray-300 font-mono min-w-[70px] text-center">
                            <span id="pdf-page-current">1</span>
                            <span class="text-gray-500">/</span>
                            <span id="pdf-page-total">—</span>
                        </span>
                        <button onclick="pdfNextPage()" id="pdf-next"
                            class="w-7 h-7 rounded flex items-center justify-center text-gray-300 hover:bg-gray-700 hover:text-white transition disabled:opacity-30"
                            title="Halaman berikutnya">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Current tab label --}}
                    <span id="pdf-tab-label" class="text-xs text-gray-400 flex-1 text-center truncate px-2">Surat Tugas</span>

                    {{-- Zoom + fullscreen --}}
                    <div class="flex items-center gap-1.5">
                        <button onclick="pdfZoomOut()"
                            class="w-7 h-7 rounded flex items-center justify-center text-gray-300 hover:bg-gray-700 hover:text-white transition"
                            title="Perkecil">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"/>
                            </svg>
                        </button>
                        <span id="pdf-zoom-label" class="text-xs text-gray-400 font-mono w-10 text-center">100%</span>
                        <button onclick="pdfZoomIn()"
                            class="w-7 h-7 rounded flex items-center justify-center text-gray-300 hover:bg-gray-700 hover:text-white transition"
                            title="Perbesar">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                            </svg>
                        </button>
                        <div class="w-px h-4 bg-gray-600 mx-0.5"></div>
                        <button onclick="pdfFullscreen()"
                            class="w-7 h-7 rounded flex items-center justify-center text-gray-300 hover:bg-gray-700 hover:text-white transition"
                            title="Layar penuh">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Canvas area --}}
                <div class="relative bg-gray-700" style="height:1040px;overflow:auto;" id="pdf-scroll-area">
                    <div id="pdf-canvas-wrap" class="flex items-start justify-center py-4 min-h-full"
                        style="transform-origin:top center;">
                        <canvas id="pdf-canvas" class="shadow-xl rounded"></canvas>
                    </div>

                    {{-- Loading --}}
                    <div id="pdf-loading"
                        class="absolute inset-0 flex flex-col items-center justify-center bg-gray-700 gap-3">
                        <div class="sipadu-spinner" style="border-top-color:var(--gold-400);"></div>
                        <p class="text-xs text-gray-400">Memuat PDF...</p>
                    </div>

                    {{-- Error / placeholder --}}
                    <div id="pdf-placeholder"
                        class="absolute inset-0 flex flex-col items-center justify-center bg-gray-700 gap-3 hidden">
                        <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-sm text-gray-400" id="pdf-placeholder-msg">
                            File PDF contoh belum tersedia.
                        </p>
                        <p class="text-xs text-gray-500 text-center max-w-xs">
                            Letakkan file PDF di <code class="bg-gray-800 px-1 rounded">public/guide-examples/</code>
                            dengan nama sesuai tab, lalu refresh halaman.
                        </p>
                    </div>
                </div>

                {{-- Footer hint --}}
                <div class="px-4 py-2 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                    <p class="text-xs text-gray-400">
                        Scroll untuk melihat seluruh halaman. Gunakan toolbar untuk zoom dan navigasi.
                    </p>
                    <a id="pdf-download-link" href="#" download
                    class="text-xs text-blue-600 hover:underline hidden">
                        ⬇ Unduh PDF
                    </a>
                </div>
            </div>
        </section>

        <section id="preview" class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold text-blue-700 border-b border-blue-100 pb-2 mb-4">Fitur Preview Dokumen</h2>
            <p class="text-sm text-gray-700 mb-4">
                Fitur preview menggunakan LibreOffice untuk mengkonversi file ke PDF dan menampilkannya di browser.
            </p>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded p-3">
                    <p class="text-xs font-semibold text-gray-600 mb-2">Cara Mengaktifkan (Admin)</p>
                    <ol class="text-xs text-gray-700 space-y-1 list-decimal list-inside">
                        <li>Buka Admin Panel → Jenis Dokumen</li>
                        <li>Klik toggle di kolom Preview pada template yang diinginkan</li>
                        <li>Toggle biru = aktif, abu = nonaktif</li>
                    </ol>
                </div>
                <div class="bg-gray-50 rounded p-3">
                    <p class="text-xs font-semibold text-gray-600 mb-2">Persyaratan Preview</p>
                    <ul class="text-xs text-gray-700 space-y-1">
                        <li>• LibreOffice terinstall di server</li>
                        <li>• Folder <code class="bg-gray-100 px-1 rounded">public/cached_result/</code> dapat ditulis</li>
                        <li>• Fitur preview diaktifkan admin untuk template</li>
                    </ul>
                </div>
            </div>
            <x-code class="mt-3">sudo apt install libreoffice   # instalasi LibreOffice</x-code>
        </section>

        <section id="scheduler" class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold text-blue-700 border-b border-blue-100 pb-2 mb-4">Sistem Pembersihan File Otomatis</h2>
            <p class="text-sm text-gray-700 mb-3">
                Sistem secara otomatis menghapus file dokumen setelah jangka waktu tertentu (default: 120 detik).
                Riwayat penghapusan dicatat di database.
            </p>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Development (lokal)</p>
                    <x-code>php artisan schedule:work</x-code>
                    <p class="text-xs text-gray-500 mt-1">Jalankan di terminal terpisah</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Production (crontab)</p>
                    <x-code>* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1</x-code>
                </div>
            </div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Mengubah TTL (routes/console.php)</p>
            <x-code>Schedule::command('documents:purge --ttl=300')->everyMinute();  // 5 menit</x-code>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1 mt-3">Menjalankan Manual</p>
            <x-code>
                php artisan documents:purge<br>php artisan documents:purge --ttl=60  # hapus file > 60 detik
            </x-code>
        </section>

        <section id="deployment" class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold text-blue-700 border-b border-blue-100 pb-2 mb-4">Panduan Deployment ke VPS Ubuntu</h2>

            <div class="space-y-4">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">1. Update server dan install dependensi</p>
                    <x-code>
                        sudo apt update && sudo apt upgrade -y<br>sudo apt install php8.3 php8.3-fpm php8.3-sqlite3 php8.3-xml php8.3-curl<br>sudo apt install python3 python3-venv python3-pip nodejs npm libreoffice nginx
                    </x-code>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">2. Clone dan setup project</p>
                    <x-code>
git clone https://github.com/JustMarvell/DocumentMaker.git /var/www/eDocPUPRD
cd /var/www/eDocPUPRD
composer install --no-dev
cp .env.example .env && php artisan key:generate
python3 -m venv venv && venv/bin/pip install docxtpl openpyxl jinja2
npm install && npm run build
touch database/database.sqlite && php artisan migrate --seed
                    </x-code>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">3. Set permission</p>
                    <x-code>
sudo chown -R www-data:www-data /var/www/eDocPUPRD
sudo chmod -R 775 /var/www/eDocPUPRD/storage
sudo chmod -R 775 /var/www/eDocPUPRD/public/cached_result
                    </x-code>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">4. Setup crontab scheduler</p>
                    <x-code>
                        sudo crontab -u www-data -e
# Tambahkan baris berikut:
* * * * * cd /var/www/eDocPUPRD && php artisan schedule:run >> /dev/null 2>&1
</x-code>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">5. Membuat admin pertama</p>
                    <x-code>
                        php artisan db
UPDATE users SET role = 'admin' WHERE email = 'admin@dinas.go.id';
.quit
                    </x-code>
                </div>
            </div>
        </section>

        {{-- Digital Signature --}}
        <section id="digital-signature" class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold text-blue-700 border-b border-blue-100 pb-2 mb-4">Tanda Tangan Digital</h2>
            <p class="text-sm text-gray-700 mb-4">
                Fitur TTD Digital memungkinkan pengguna meminta tanda tangan pejabat secara elektronik.
                Pejabat menerima email berisi dokumen terlampir dan tautan persetujuan sekali pakai.
                Setelah disetujui, sistem menyisipkan gambar tanda tangan dan QR code verifikasi ke dalam dokumen.
            </p>

            <div id="ttd-flow" class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Alur TTD</h3>
                <div class="overflow-x-auto mb-3">
                    <table class="w-full text-sm">
                        <thead class="bg-blue-700 text-white">
                            <tr><th class="px-3 py-2 text-left font-medium">Langkah</th><th class="px-3 py-2 text-left font-medium">Pelaku</th><th class="px-3 py-2 text-left font-medium">Aksi</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @foreach([
                                ['1','User','Buat dokumen → klik "Minta Tanda Tangan" → pilih pejabat → kirim'],
                                ['2','Sistem','Kirim email ke pejabat dengan dokumen terlampir dan tautan review'],
                                ['3','Pejabat','Buka tautan, tinjau dokumen, klik Setujui atau Tolak'],
                                ['4','Sistem','Jika disetujui: sisipkan gambar TTD + QR code → simpan file signed'],
                                ['5','Sistem','Kirim notifikasi email ke pemohon beserta dokumen bertanda tangan'],
                                ['6','User','Unduh dokumen dari tab Riwayat Dokumen atau email'],
                                ['7','Siapapun','Scan QR code → halaman verifikasi publik membuktikan keaslian dokumen'],
                            ] as $row)
                            <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}">
                                <td class="px-3 py-2 font-bold text-blue-600">{{ $row[0] }}</td>
                                <td class="px-3 py-2 font-medium text-gray-700">{{ $row[1] }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ $row[2] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <p class="text-sm font-medium text-gray-700 mb-1">Mengaktifkan TTD untuk Jenis Dokumen:</p>
                <ol class="text-sm text-gray-700 space-y-1 list-decimal list-inside mb-3">
                    <li>Admin Panel → Jenis Dokumen</li>
                    <li>Toggle kolom <strong>TTD Digital</strong> → biru = aktif</li>
                    <li>Opsional: toggle <strong>Gambar TTD</strong> (embed foto tanda tangan) dan <strong>QR Code</strong> (embed kode QR verifikasi)</li>
                </ol>

                <div class="bg-yellow-50 border border-yellow-200 rounded px-3 py-2 text-xs text-yellow-800 mb-3">
                    <strong>Catatan:</strong> Tombol "Minta Tanda Tangan" hanya muncul jika TTD Digital diaktifkan untuk jenis dokumen tersebut <em>dan</em> file dokumen masih ada di server (belum dihapus otomatis).
                </div>

                <p class="text-sm font-medium text-gray-700 mb-1">Kelola Antrian dari Admin:</p>
                <p class="text-sm text-gray-700 mb-1">Admin Panel → <strong>Antrian TTD</strong> — admin dapat menyetujui/menolak langsung tanpa melalui email pejabat, serta mengirim ulang email ke pejabat atau pemohon.</p>
            </div>

            <div id="ttd-template" class="mb-4">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">Template untuk TTD — Placeholder Wajib</h3>
                <p class="text-sm text-gray-700 mb-2">
                    Agar gambar TTD dan QR code disisipkan dengan benar, template harus menyertakan dummy image sebagai placeholder.
                    Sistem mengganti dummy image tersebut dengan gambar asli saat penandatanganan.
                </p>
                <div class="grid grid-cols-2 gap-4 mb-3">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Placeholder Teks (DOCX & XLSX)</p>
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs">
                                <thead class="bg-blue-700 text-white"><tr><th class="px-2 py-1.5 text-left">Placeholder</th><th class="px-2 py-1.5 text-left">Isi Saat Signed</th></tr></thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach([
                                        [@verbatim'{{ nama_pejabat }}'@endverbatim,'Nama lengkap pejabat'],
                                        [@verbatim'{{ jabatan_pejabat }}'@endverbatim,'Jabatan pejabat'],
                                        [@verbatim'{{ tgl_ttd }}'@endverbatim,'Tanggal persetujuan (format: 30 April 2026)'],
                                    ] as $row)
                                    <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}"><td class="px-2 py-1.5 font-mono text-blue-700">{{ $row[0] }}</td><td class="px-2 py-1.5 text-gray-600">{{ $row[1] }}</td></tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Placeholder Gambar</p>
                        <ul class="text-xs text-gray-700 space-y-2">
                            <li>
                                <code class="bg-gray-100 px-1 rounded">transparent35mm.png</code> — dummy untuk gambar tanda tangan.
                                Letakkan di template sebagai gambar inline/text box.
                                Sistem swap gambar ini dengan foto TTD pejabat.
                            </li>
                            <li>
                                <code class="bg-gray-100 px-1 rounded">dummy_qr.png</code> — dummy untuk QR code verifikasi.
                                Sama seperti di atas, sistem swap gambar ini dengan QR yang di-generate otomatis.
                            </li>
                        </ul>
                        <div class="bg-blue-50 border border-blue-200 rounded px-2 py-1.5 mt-2 text-xs text-blue-800">
                            File dummy tersedia di <code class="bg-blue-100 px-1 rounded">resources/img/</code>. Gunakan sebagai gambar placeholder di template.
                        </div>
                    </div>
                </div>

                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Upload Gambar Tanda Tangan Pejabat</p>
                <ol class="text-xs text-gray-700 space-y-1 list-decimal list-inside">
                    <li>Admin Panel → Data Pejabat</li>
                    <li>Klik Edit pada pejabat yang dituju</li>
                    <li>Upload gambar TTD (PNG transparan disarankan, maks 2MB)</li>
                    <li>Simpan — gambar akan otomatis digunakan saat dokumen ditandatangani</li>
                </ol>
            </div>
        </section>

        {{-- Nomor Surat --}}
        <section id="nomor-surat" class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold text-blue-700 border-b border-blue-100 pb-2 mb-4">Nomor Surat Otomatis</h2>
            <p class="text-sm text-gray-700 mb-4">
                Setiap jenis dokumen dapat dikonfigurasi agar nomor surat digenerate otomatis dan bertambah 1 setiap dokumen berhasil dibuat.
                Field yang dipilih sebagai target akan diisi otomatis — input user diabaikan.
            </p>

            <div class="grid grid-cols-2 gap-6 mb-4">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Token Format</p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-blue-700 text-white"><tr><th class="px-3 py-2 text-left font-medium">Token</th><th class="px-3 py-2 text-left font-medium">Nilai</th></tr></thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                @foreach([['{number}','Nomor urut (zero-padded)'],['{year}','Tahun 4 digit (2026)'],['{month}','Bulan 2 digit (05)'],['{roman_month}','Bulan romawi (V)']] as $row)
                                <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}"><td class="px-3 py-2 font-mono text-xs text-blue-700">{{ $row[0] }}</td><td class="px-3 py-2 text-gray-600">{{ $row[1] }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Contoh Format</p>
                    <div class="font-mono text-xs space-y-2">
                        <div class="bg-gray-50 rounded p-2">
                            <p class="text-gray-500">{number}/DPUPR/{roman_month}/{year}</p>
                            <p class="text-blue-600 font-bold">→ 045/DPUPR/V/2026</p>
                        </div>
                        <div class="bg-gray-50 rounded p-2">
                            <p class="text-gray-500">B-{number}/PUPRD/{roman_month}/{year}</p>
                            <p class="text-blue-600 font-bold">→ B-045/PUPRD/V/2026</p>
                        </div>
                    </div>
                </div>
            </div>

            <p class="text-sm font-medium text-gray-700 mb-1">Konfigurasi:</p>
            <ol class="text-sm text-gray-700 space-y-1 list-decimal list-inside mb-3">
                <li>Kelola Field → klik <strong># Nomor Surat</strong></li>
                <li>Centang "Aktifkan Nomor Surat Otomatis"</li>
                <li>Atur format, zero-padding, reset otomatis, dan field key tujuan</li>
                <li>Simpan — counter mulai berjalan pada dokumen berikutnya</li>
            </ol>
            <div class="bg-blue-50 border border-blue-200 rounded px-3 py-2 text-xs text-blue-800">
                Counter bisa di-set ke angka tertentu atau di-reset ke 0 dari halaman yang sama. Reset otomatis tersedia per tahun atau per bulan.
            </div>
        </section>

        {{-- Scan Template --}}
        <section id="scan-template" class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold text-blue-700 border-b border-blue-100 pb-2 mb-4">Scan Template — Deteksi Field Otomatis</h2>
            <p class="text-sm text-gray-700 mb-4">
                Daripada menambah field satu per satu, fitur Scan Template membaca file template dan mendeteksi semua placeholder
                <code class="bg-gray-100 px-1 rounded">@verbatim{{ variable }}@endverbatim</code> secara otomatis.
            </p>
            <ol class="text-sm text-gray-700 space-y-1 list-decimal list-inside mb-3">
                <li>Kelola Field → klik <strong>Scan Template</strong></li>
                <li>Sistem memindai template dan menampilkan variabel yang belum terdaftar sebagai field</li>
                <li>Sistem menebak tipe field berdasarkan nama variabel (misal: <code class="bg-gray-100 px-1 rounded">tanggal_*</code> → date)</li>
                <li>Edit label, tipe, dan seksi sesuai kebutuhan</li>
                <li>Centang field yang ingin ditambahkan → klik "Tambahkan Field Terpilih"</li>
            </ol>
            <div class="bg-yellow-50 border border-yellow-200 rounded px-3 py-2 text-xs text-yellow-800">
                Field yang sudah terdaftar dilewati otomatis. Tipe field hasil tebakan mungkin perlu disesuaikan manual.
            </div>
        </section>

        {{-- PDF Preview --}}
        <section id="pdf-preview" class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold text-blue-700 border-b border-blue-100 pb-2 mb-4">Preview PDF via iLoveAPI</h2>
            <p class="text-sm text-gray-700 mb-4">
                Sistem menggunakan <strong>iLoveAPI</strong> untuk mengkonversi dokumen ke PDF dan menampilkannya langsung di browser.
                Setiap konversi mengkonsumsi 1 kuota dari batas yang dikonfigurasi admin.
            </p>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="bg-gray-50 rounded p-3">
                    <p class="text-xs font-semibold text-gray-600 mb-2">Konfigurasi (Admin)</p>
                    <ol class="text-xs text-gray-700 space-y-1 list-decimal list-inside">
                        <li>Daftar di <a href="https://developer.ilovepdf.com" target="_blank" class="text-blue-500 underline">developer.ilovepdf.com</a></li>
                        <li>Salin Public Key dan Secret Key</li>
                        <li>Admin Panel → <strong>Pengaturan PDF</strong> → paste key</li>
                        <li>Set batas konversi dan mode reset</li>
                        <li>Aktifkan preview per template di halaman Jenis Dokumen (toggle Preview)</li>
                    </ol>
                </div>
                <div class="bg-gray-50 rounded p-3">
                    <p class="text-xs font-semibold text-gray-600 mb-2">Batas Konversi</p>
                    <ul class="text-xs text-gray-700 space-y-1">
                        <li>• Tier gratis iLoveAPI: <strong>250 konversi/bulan</strong></li>
                        <li>• Admin dapat set batas custom dan mode reset (bulanan/manual)</li>
                        <li>• Counter dapat di-reset manual kapan saja</li>
                        <li>• Jika kuota habis, tombol preview tidak berfungsi</li>
                        <li>• File PDF hasil konversi ikut dihapus bersama dokumen asli</li>
                    </ul>
                </div>
            </div>
            <div class="bg-red-50 border border-red-200 rounded px-3 py-2 text-sm text-red-800">
                <strong>Penting:</strong> Preview hanya tersedia jika API key iLoveAPI sudah dikonfigurasi dan toggle Preview diaktifkan untuk jenis dokumen tersebut.
            </div>
        </section>

        <section id="troubleshooting" class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold text-blue-700 border-b border-blue-100 pb-2 mb-4">Pemecahan Masalah (Troubleshooting)</h2>

            <div class="space-y-4">

                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-4 py-2 font-medium text-sm text-gray-700 border-b border-gray-200">
                        ❗ Dokumen gagal dibuat — muncul pesan error
                    </div>
                    <div class="px-4 py-3 text-sm text-gray-700 space-y-2">
                        <p>Aktifkan debug sementara di <code class="bg-gray-100 px-1 rounded text-xs">DocumentController.php</code>:</p>
                        <x-code>if (!$process->isSuccessful()) {
    dd($process->getErrorOutput(), $process->getOutput());
}</x-code>
                        <ul class="text-xs space-y-1 text-gray-600">
                            <li>• Coba buat dokumen lagi — error Python akan tampil di layar</li>
                            <li>• Periksa nama variabel di template cocok dengan field_key di admin</li>
                            <li>• Periksa file template ada di folder <code class="bg-gray-100 px-1 rounded">document_templates/</code></li>
                        </ul>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-4 py-2 font-medium text-sm text-gray-700 border-b border-gray-200">
                        ❗ Variabel tidak tergantikan di dokumen (masih @verbatim{{ nama_variabel }}@endverbatim)
                    </div>
                    <div class="px-4 py-3 text-sm text-gray-700">
                        <ul class="text-xs space-y-1 text-gray-600">
                            <li>• Pastikan field_key di admin PERSIS sama dengan nama variabel di template (case-sensitive)</li>
                            <li>• Periksa tidak ada spasi berlebih di dalam <code class="bg-gray-100 px-1 rounded">@verbatim{{ }}@endverbatim</code></li>
                            <li>• Untuk Excel: pastikan variabel di sel, bukan di comment atau nama cell</li>
                            <li>• Untuk Word: jika variabel di text box, pastikan tidak terpotong oleh formatting berbeda</li>
                        </ul>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-4 py-2 font-medium text-sm text-gray-700 border-b border-gray-200">
                        ❗ Gambar atau text box hilang dari Excel
                    </div>
                    <div class="px-4 py-3 text-sm text-gray-700">
                        <ul class="text-xs space-y-1 text-gray-600">
                            <li>• Pastikan menggunakan <code class="bg-gray-100 px-1 rounded">xlsx_generator.py</code> versi terbaru</li>
                            <li>• Jangan buka dan simpan ulang template Excel menggunakan openpyxl — selalu gunakan file asli</li>
                        </ul>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-4 py-2 font-medium text-sm text-gray-700 border-b border-gray-200">
                        ❗ Autofill tidak mengisi field
                    </div>
                    <div class="px-4 py-3 text-sm text-gray-700">
                        <ul class="text-xs space-y-1 text-gray-600">
                            <li>• Periksa Autofill Role pada field sesuai dengan slot_key yang ada</li>
                            <li>• Periksa Staff Autofill Column terisi dengan nama kolom yang benar</li>
                            <li>• Buka browser console (F12) dan periksa apakah ada error JavaScript</li>
                            <li>• Pastikan <code class="bg-gray-100 px-1 rounded">/api/staff</code> dan <code class="bg-gray-100 px-1 rounded">/api/officials</code> dapat diakses saat login</li>
                        </ul>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-4 py-2 font-medium text-sm text-gray-700 border-b border-gray-200">
                        ❗ Preview tidak muncul
                    </div>
                    <div class="px-4 py-3 text-sm text-gray-700">
                        <ul class="text-xs space-y-1 text-gray-600">
                            <li>• Pastikan LibreOffice terinstall: <code class="bg-gray-100 px-1 rounded">libreoffice --version</code></li>
                            <li>• Pastikan fitur preview diaktifkan di Admin Panel → Jenis Dokumen</li>
                            <li>• Periksa log Laravel di <code class="bg-gray-100 px-1 rounded">storage/logs/laravel.log</code></li>
                            <li>• Pastikan folder <code class="bg-gray-100 px-1 rounded">public/cached_result/</code> dapat ditulis web server</li>
                        </ul>
                    </div>
                </div>

                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-4 py-2 font-medium text-sm text-gray-700 border-b border-gray-200">
                        ❗ Loop tidak menghasilkan baris yang benar di Excel
                    </div>
                    <div class="px-4 py-3 text-sm text-gray-700">
                        <ul class="text-xs space-y-1 text-gray-600">
                            <li>• Pastikan <code class="bg-gray-100 px-1 rounded">{% for %}</code> dan <code class="bg-gray-100 px-1 rounded">{% endfor %}</code> ada di sel yang BERBEDA (bukan satu sel)</li>
                            <li>• Pastikan baris template (antara for dan endfor) tidak kosong</li>
                            <li>• Periksa field_key di admin sesuai dengan nama list di template (setelah kata "in")</li>
                        </ul>
                    </div>
                </div>

            </div>
        </section>

        <div class="text-center text-xs text-gray-400 py-4 border-t border-gray-200">
            eDocPUPRD — Sistem Pembuatan Dokumen Digital &nbsp;|&nbsp;
            DINAS PUPRD KOTA TOMOHON &nbsp;|&nbsp;
            Panduan Versi 1.0 | 2026 | by. Marvelous Makaluwu
        </div>

    </div>
</div>

<script>
const sections = document.querySelectorAll('section[id]');
const tocLinks  = document.querySelectorAll('.toc-link');

function highlightToc() {
    let current = '';
    sections.forEach(function(section) {
        if (window.scrollY >= section.offsetTop - 100) {
            current = section.getAttribute('id');
        }
    });
    tocLinks.forEach(function(link) {
        link.classList.remove('bg-blue-50', 'text-blue-700', 'font-medium');
        if (link.getAttribute('href') === '#' + current) {
            link.classList.add('bg-blue-50', 'text-blue-700', 'font-medium');
        }
    });
}

(function() {
    // Map tab key → PDF path + label
    const PDF_FILES = {
        'surat-tugas':  { path: '/guides/guide_1.pdf',  label: 'Surat Tugas' },
        'daftar-hadir': { path: '/guides/guide_1.pdf', label: 'Daftar Hadir (Loop)' },
        'kondisional':  { path: '/guides/guide_1.pdf',  label: 'Kondisional' },
        'loop-excel':   { path: '/guides/guide_1.pdf',   label: 'Loop di Excel' },
    };

    let pdfjsLib = null;
    let pdfDoc   = null;
    let curPage  = 1;
    let curZoom  = 1.0;
    let curTab   = 'surat-tugas';
    let renderTask = null;

    // Load pdf.js lazily
    function loadPdfJs(cb) {
        if (pdfjsLib) { cb(); return; }
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js';
        script.onload = function() {
            pdfjsLib = window.pdfjsLib;
            pdfjsLib.GlobalWorkerOptions.workerSrc =
                'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
            cb();
        };
        document.head.appendChild(script);
    }

    function showLoading(yes) {
        document.getElementById('pdf-loading').classList.toggle('hidden', !yes);
        document.getElementById('pdf-placeholder').classList.add('hidden');
    }

    function showPlaceholder(msg) {
        document.getElementById('pdf-loading').classList.add('hidden');
        document.getElementById('pdf-placeholder').classList.remove('hidden');
        document.getElementById('pdf-placeholder-msg').textContent = msg || 'File PDF contoh belum tersedia.';
        document.getElementById('pdf-download-link').classList.add('hidden');
    }

    function renderPage(num) {
        if (!pdfDoc) return;
        if (renderTask) { renderTask.cancel(); }

        pdfDoc.getPage(num).then(function(page) {
            const canvas  = document.getElementById('pdf-canvas');
            const ctx     = canvas.getContext('2d');
            const vp      = page.getViewport({ scale: curZoom * 1.5 }); // 1.5 base DPI boost
            canvas.width  = vp.width;
            canvas.height = vp.height;

            renderTask = page.render({ canvasContext: ctx, viewport: vp });
            renderTask.promise.then(function() {
                renderTask = null;
            }).catch(function() {});

            document.getElementById('pdf-page-current').textContent = num;
            document.getElementById('pdf-page-total').textContent   = pdfDoc.numPages;
            document.getElementById('pdf-prev').disabled = num <= 1;
            document.getElementById('pdf-next').disabled = num >= pdfDoc.numPages;
        });
    }

    function loadPdf(tabKey) {
        const info = PDF_FILES[tabKey];
        showLoading(true);
        pdfDoc  = null;
        curPage = 1;
        document.getElementById('pdf-page-current').textContent = '1';
        document.getElementById('pdf-page-total').textContent   = '—';
        document.getElementById('pdf-canvas').width  = 0;
        document.getElementById('pdf-canvas').height = 0;

        loadPdfJs(function() {
            pdfjsLib.getDocument(info.path).promise.then(function(pdf) {
                pdfDoc = pdf;
                showLoading(false);
                document.getElementById('pdf-download-link').href = info.path;
                document.getElementById('pdf-download-link').classList.remove('hidden');
                renderPage(1);
            }).catch(function(err) {
                console.warn('PDF load error:', err);
                showPlaceholder('File PDF "' + info.label + '" belum tersedia di server.');
            });
        });
    }

    // Public functions
    window.switchPdfTab = function(key) {
        curTab  = key;
        curZoom = 1.0;
        document.getElementById('pdf-zoom-label').textContent = '100%';
        document.getElementById('pdf-tab-label').textContent  = PDF_FILES[key].label;

        document.querySelectorAll('.pdf-tab-btn').forEach(function(btn) {
            const active = btn.id === 'pdf-tab-' + key;
            btn.classList.toggle('border-blue-600', active);
            btn.classList.toggle('text-blue-700', active);
            btn.classList.toggle('bg-white', active);
            btn.classList.toggle('border-transparent', !active);
            btn.classList.toggle('text-gray-500', !active);
        });

        loadPdf(key);
    };

    window.pdfPrevPage = function() {
        if (!pdfDoc || curPage <= 1) return;
        curPage--;
        renderPage(curPage);
    };

    window.pdfNextPage = function() {
        if (!pdfDoc || curPage >= pdfDoc.numPages) return;
        curPage++;
        renderPage(curPage);
    };

    window.pdfZoomIn = function() {
        curZoom = Math.min(curZoom + 0.25, 3.0);
        document.getElementById('pdf-zoom-label').textContent = Math.round(curZoom * 100) + '%';
        renderPage(curPage);
    };

    window.pdfZoomOut = function() {
        curZoom = Math.max(curZoom - 0.25, 0.5);
        document.getElementById('pdf-zoom-label').textContent = Math.round(curZoom * 100) + '%';
        renderPage(curPage);
    };

    window.pdfFullscreen = function() {
        const container = document.getElementById('pdf-viewer-container');
        if (!document.fullscreenElement) {
            container.requestFullscreen().catch(function() {});
        } else {
            document.exitFullscreen();
        }
    };

    // Keyboard shortcuts when viewer is focused
    document.getElementById('pdf-viewer-container').addEventListener('keydown', function(e) {
        if (e.key === 'ArrowRight' || e.key === 'ArrowDown') { e.preventDefault(); window.pdfNextPage(); }
        if (e.key === 'ArrowLeft'  || e.key === 'ArrowUp')   { e.preventDefault(); window.pdfPrevPage(); }
        if (e.key === '+' || e.key === '=') { e.preventDefault(); window.pdfZoomIn(); }
        if (e.key === '-')                  { e.preventDefault(); window.pdfZoomOut(); }
        if (e.key === 'f' || e.key === 'F') { e.preventDefault(); window.pdfFullscreen(); }
    });
    document.getElementById('pdf-viewer-container').setAttribute('tabindex', '0');

    // Fullscreen style tweak
    document.addEventListener('fullscreenchange', function() {
        const scrollArea = document.getElementById('pdf-scroll-area');
        if (document.fullscreenElement) {
            scrollArea.style.height = 'calc(100vh - 90px)';
        } else {
            scrollArea.style.height = '520px';
        }
    });

    // Auto-load first tab when guide section is visible (lazy)
    let loaded = false;
    const observer = new IntersectionObserver(function(entries) {
        if (entries[0].isIntersecting && !loaded) {
            loaded = true;
            loadPdf('surat-tugas');
        }
    }, { threshold: 0.1 });
    observer.observe(document.getElementById('pdf-viewer-container'));
})();

window.addEventListener('scroll', highlightToc);
highlightToc();
</script>

@endsection