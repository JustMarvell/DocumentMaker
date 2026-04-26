@extends('admin.layout')

@section('content')
<div class="flex items-start justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Panduan Sistem SIPADU</h1>
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
                <a href="#troubleshooting" class="toc-link block py-1 px-2 rounded text-gray-600 hover:bg-blue-50 hover:text-blue-700">Troubleshooting</a>
            </nav>
        </div>
    </aside>

    <div class="flex-1 min-w-0 space-y-8">
        <section id="pengenalan" class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold text-blue-700 border-b border-blue-100 pb-2 mb-4">Pengenalan Sistem</h2>

            <p class="text-sm text-gray-700 mb-4">
                <strong>SIPADU</strong> (Sistem Generasi Administrasi Persuratan) adalah aplikasi web berbasis Laravel yang dirancang khusus untuk
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
            <x-code>sudo apt update && sudo apt install libreoffice</x-code>
            <x-code>libreoffice --version   # verifikasi instalasi</x-code>
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
                <x-code>php artisan db</x-code>
                <x-code>UPDATE users SET role = 'admin' WHERE email = 'email_anda@gmail.com';</x-code>
                <x-code>.quit</x-code>
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
                        <li>Ketik placeholder: <code class="bg-gray-100 px-1 rounded">{{ '{{ nama_variabel }}' }}</code></li>
                        <li>Simpan sebagai <code class="bg-gray-100 px-1 rounded">.docx</code></li>
                        <li>Upload melalui Admin Panel</li>
                    </ol>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Catatan Penting:</p>
                    <ul class="text-xs text-gray-700 space-y-1">
                        <li>• Placeholder bisa di tabel, paragraf, header, footer, text box</li>
                        <li>• Loop dalam tabel: letakkan <code class="bg-gray-100 px-1 rounded">{% raw %}{% for %}{% endraw %}</code> di dalam sel tabel</li>
                        <li>• Gunakan <code class="bg-gray-100 px-1 rounded">{{ '{{ loop.index }}' }}</code> untuk nomor urut otomatis</li>
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
                        <li>Untuk loop: gunakan <code class="bg-gray-100 px-1 rounded">{% raw %}{% for %}{% endraw %}</code> di sel terpisah</li>
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
                                <tr><td class="px-2 py-1.5 font-mono text-blue-700">{{ '{{ field_key }}' }}</td><td class="px-2 py-1.5 text-gray-600">Menampilkan nilai variabel</td></tr>
                                <tr class="bg-gray-50"><td class="px-2 py-1.5 font-mono text-blue-700">{{ '{{ val | upper }}' }}</td><td class="px-2 py-1.5 text-gray-600">HURUF BESAR</td></tr>
                                <tr><td class="px-2 py-1.5 font-mono text-blue-700">{{ '{{ val | default("") }}' }}</td><td class="px-2 py-1.5 text-gray-600">Nilai default jika kosong</td></tr>
                                <tr class="bg-gray-50"><td class="px-2 py-1.5 font-mono text-blue-700">{% raw %}{% if kondisi %}{% endraw %}</td><td class="px-2 py-1.5 text-gray-600">Kondisional</td></tr>
                                <tr><td class="px-2 py-1.5 font-mono text-blue-700">{% raw %}{% for x in list %}{% endraw %}</td><td class="px-2 py-1.5 text-gray-600">Pengulangan</td></tr>
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
                        @foreach([['upper','{{ nama | upper }}','JOHN DOE'],['lower','{{ nama | lower }}','john doe'],['title','{{ nama | title }}','John Doe'],['default','{{ nilai | default("N/A") }}','N/A jika kosong'],['length','{{ daftar | length }}','Jumlah item dalam list'],['first','{{ daftar | first }}','Item pertama'],['last','{{ daftar | last }}','Item terakhir'],['join','{{ list | join(", ") }}','A, B, C']] as $row)
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

            <div class="space-y-4">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Contoh 1: Surat Tugas (Word)</p>
                    <div class="bg-gray-900 text-green-400 rounded-lg p-4 font-mono text-xs overflow-x-auto">
                        <pre>SURAT TUGAS
Nomor: {{ '{{ letter_number }}' }}

Yang bertanda tangan di bawah ini menerangkan bahwa:
Nama     : {{ '{{ employee_name }}' }}
NIP      : {{ '{{ employee_nip }}' }}
Jabatan  : {{ '{{ employee_position }}' }}
Unit     : {{ '{{ employee_work_unit }}' }}

Ditugaskan melaksanakan perjalanan dinas ke:
{{ '{{ destination_agency }}' }}

Pada tanggal {{ '{{ departure_date }}' }} s.d. {{ '{{ return_date }}' }}

Tomohon, {{ '{{ letter_date }}' }}
Kepala Dinas,

{{ '{{ head_of_office_name }}' }}
NIP. {{ '{{ head_of_office_nip }}' }}</pre>
                    </div>
                </div>

                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Contoh 2: Daftar Hadir (Excel/Word dengan Loop)</p>
                    <div class="bg-gray-900 text-green-400 rounded-lg p-4 font-mono text-xs overflow-x-auto">
                        <pre>DAFTAR HADIR RAPAT
Perihal : {{ '{{ meeting_subject }}' }}
Tanggal : {{ '{{ meeting_date }}' }}
Tempat  : {{ '{{ meeting_location }}' }}

NO | NAMA              | NIP       | JABATAN    | TTD
{% raw %}{% for peserta in participants %}{% endraw %}
{{ '{{ loop.index }}' }}  | {{ '{{ peserta.staff_name }}' }} | {{ '{{ peserta.nip }}' }} | {{ '{{ peserta.position }}' }} |
{% raw %}{% endfor %}{% endraw %}</pre>
                    </div>
                </div>

                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Contoh 3: Dengan Kondisional</p>
                    <div class="bg-gray-900 text-green-400 rounded-lg p-4 font-mono text-xs overflow-x-auto">
                        <pre>{% raw %}{% if employee_rank %}
Pangkat/Gol. Ruang: {{ employee_rank }}
{% else %}
Pangkat/Gol. Ruang: -
{% endif %}

{% if is_approved %}
Status: DISETUJUI
{% else %}
Status: MENUNGGU PERSETUJUAN
{% endif %}{% endraw %}</pre>
                    </div>
                </div>

                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Loop di Excel — Penempatan Sel</p>
                    <div class="bg-gray-900 text-green-400 rounded-lg p-4 font-mono text-xs overflow-x-auto">
                        <pre>Sel A1: {% raw %}{% for peserta in daftar_peserta %}{% endraw %}
Sel A2: {{ '{{ loop.index }}' }}
Sel B2: {{ '{{ peserta.staff_name }}' }}
Sel C2: {{ '{{ peserta.nip }}' }}
Sel D2: {{ '{{ peserta.position }}' }}
Sel A3: {% raw %}{% endfor %}{% endraw %}</pre>
                    </div>
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
            <x-code>php artisan documents:purge</x-code>
            <x-code>php artisan documents:purge --ttl=60  # hapus file > 60 detik</x-code>
        </section>

        <section id="deployment" class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-bold text-blue-700 border-b border-blue-100 pb-2 mb-4">Panduan Deployment ke VPS Ubuntu</h2>

            <div class="space-y-4">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">1. Update server dan install dependensi</p>
                    <x-code>sudo apt update && sudo apt upgrade -y</x-code>
                    <x-code>sudo apt install php8.3 php8.3-fpm php8.3-sqlite3 php8.3-xml php8.3-curl</x-code>
                    <x-code>sudo apt install python3 python3-venv python3-pip nodejs npm libreoffice nginx</x-code>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">2. Clone dan setup project</p>
                    <x-code>git clone https://github.com/JustMarvell/DocumentMaker.git /var/www/sipadu</x-code>
                    <x-code>cd /var/www/sipadu</x-code>
                    <x-code>composer install --no-dev</x-code>
                    <x-code>cp .env.example .env && php artisan key:generate</x-code>
                    <x-code>python3 -m venv venv && venv/bin/pip install docxtpl openpyxl jinja2</x-code>
                    <x-code>npm install && npm run build</x-code>
                    <x-code>touch database/database.sqlite && php artisan migrate --seed</x-code>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">3. Set permission</p>
                    <x-code>sudo chown -R www-data:www-data /var/www/sipadu</x-code>
                    <x-code>sudo chmod -R 775 /var/www/sipadu/storage</x-code>
                    <x-code>sudo chmod -R 775 /var/www/sipadu/public/cached_result</x-code>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">4. Setup crontab scheduler</p>
                    <x-code>sudo crontab -u www-data -e</x-code>
                    <x-code># Tambahkan baris berikut:</x-code>
                    <x-code>* * * * * cd /var/www/sipadu && php artisan schedule:run >> /dev/null 2>&1</x-code>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">5. Membuat admin pertama</p>
                    <x-code>php artisan db</x-code>
                    <x-code>UPDATE users SET role = 'admin' WHERE email = 'admin@dinas.go.id';</x-code>
                    <x-code>.quit</x-code>
                </div>
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
                        ❗ Variabel tidak tergantikan di dokumen (masih {{ '{{ nama_variabel }}' }})
                    </div>
                    <div class="px-4 py-3 text-sm text-gray-700">
                        <ul class="text-xs space-y-1 text-gray-600">
                            <li>• Pastikan field_key di admin PERSIS sama dengan nama variabel di template (case-sensitive)</li>
                            <li>• Periksa tidak ada spasi berlebih di dalam <code class="bg-gray-100 px-1 rounded">{{ '{{ }}' }}</code></li>
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
                            <li>• Pastikan <code class="bg-gray-100 px-1 rounded">{% raw %}{% for %}{% endraw %}</code> dan <code class="bg-gray-100 px-1 rounded">{% raw %}{% endfor %}{% endraw %}</code> ada di sel yang BERBEDA (bukan satu sel)</li>
                            <li>• Pastikan baris template (antara for dan endfor) tidak kosong</li>
                            <li>• Periksa field_key di admin sesuai dengan nama list di template (setelah kata "in")</li>
                        </ul>
                    </div>
                </div>

            </div>
        </section>

        <div class="text-center text-xs text-gray-400 py-4 border-t border-gray-200">
            SIPADU — Sistem Generasi Administrasi Persuratan &nbsp;|&nbsp;
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

window.addEventListener('scroll', highlightToc);
highlightToc();
</script>

@endsection