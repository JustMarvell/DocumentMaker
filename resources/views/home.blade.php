<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Automatisasi Surat — DINAS PUPRD</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 font-sans">

    {{-- ============================================================ --}}
    {{-- Navbar --}}
    {{-- ============================================================ --}}
    <nav class="bg-white shadow px-6 py-4 flex items-center justify-between">
        <span class="font-bold text-gray-800">DINAS PUPRD Kota Tomohon</span>
        <div class="flex items-center gap-4">
            @auth
                <span class="text-sm text-gray-600">
                    {{ auth()->user()->name }}
                    <span class="ml-1 px-2 py-0.5 rounded text-xs font-semibold
                            {{ auth()->user()->role === 'admin' ? 'bg-purple-100 text-purple-700' : '' }}
                            {{ auth()->user()->role === 'staff' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ auth()->user()->role === 'guest' ? 'bg-gray-100 text-gray-600' : '' }}">
                        {{ ucfirst(auth()->user()->role) }}
                    </span>
                </span>
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="text-sm text-purple-600 hover:underline font-medium">
                        Admin Panel
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-red-500 hover:underline">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:underline">Login</a>
                <a href="{{ route('register') }}" class="text-sm text-blue-600 hover:underline">Daftar</a>
            @endauth
        </div>
    </nav>

    {{-- ============================================================ --}}
    {{-- Main content --}}
    {{-- ============================================================ --}}
    <main class="max-w-3xl mx-auto px-6 py-8">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Sistem Automatisasi Surat</h1>
            <p class="text-sm text-gray-500 mt-1">
                Isi form di bawah sesuai data yang benar untuk membuat surat atau dokumen.
            </p>
        </div>

        {{-- Flash messages --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div
                class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-4 flex items-center justify-between">
                <span class="text-sm">{{ session('success') }}</span>
                @if (session('download_url'))
                    <a href="{{ session('download_url') }}"
                        class="ml-4 bg-green-600 text-white text-sm px-4 py-1.5 rounded hover:bg-green-700 font-medium whitespace-nowrap">
                        ⬇ Unduh Dokumen
                    </a>
                @endif
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded mb-4 text-sm">
                {{ session('error') }}
            </div>
        @endif

        @auth
            @if (auth()->user()->isGuest())
                <div class="bg-yellow-50 border border-yellow-300 text-yellow-800 px-4 py-3 rounded mb-4 text-sm">
                    Anda login sebagai <strong>Guest</strong>. Hanya dokumen publik yang tersedia.
                    Hubungi administrator untuk mendapatkan akses Staff.
                </div>
            @endif
        @endauth

        {{-- ============================================================ --}}
        {{-- Form card --}}
        {{-- ============================================================ --}}
        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('document.generate') }}" method="POST">
                @csrf

                {{-- Document type selector --}}
                <div class="mb-6">
                    <label for="letter-template-type" class="block text-sm font-medium text-gray-700 mb-1">
                        Jenis Surat / Dokumen
                    </label>
                    <select name="letter-type" id="letter-template-type"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        onchange="showForm(this.value)">
                        @foreach ($documentTypes as $type)
                            <option value="{{ $type->key }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- ================================================== --}}
                {{-- Permission Letter Form --}}
                {{-- ================================================== --}}
                <div id="permission-letter-form" class="hidden">

                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4 border-b pb-2">
                        Data Surat Izin Sakit
                    </h3>

                    {{-- Staff selector --}}
                    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <label class="block text-sm font-medium text-blue-700 mb-1">
                            Pilih dari Data Staff (opsional)
                        </label>
                        <select onchange="fillForm('pl', this.value)"
                            class="w-full border border-blue-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="">— Pilih staff untuk mengisi otomatis —</option>
                        </select>
                        <p class="text-xs text-blue-500 mt-1">Memilih staff akan mengisi otomatis nama, NIP, jabatan,
                            dan unit kerja.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-4">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pegawai</label>
                            <input type="text" name="pl_employee_name" id="pl-employee-name"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="John Doe..." />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan Karyawan</label>
                            <input type="text" name="pl_employee_position" id="pl-employee-position"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Staff ABC..." />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Induk Karyawan</label>
                            <input type="text" name="pl_employee_id_number" id="pl-employee-id-number"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="7103123456789..." />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Pegawai</label>
                            <input type="text" name="pl_employee_address" id="pl-employee-address"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="JL. 123 Tomohon..." />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Surat</label>
                            <input type="text" name="pl_letter_address" id="pl-letter-address"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Tomohon..." />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Surat</label>
                            <input type="date" name="pl_letter_date" id="pl-letter-date"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Banyaknya Lampiran</label>
                            <input type="number" name="pl_attachment_count" id="pl-attachment-count"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Surat</label>
                            <input type="text" name="pl_target_name" id="pl-target-name"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="cth. Pimpinan Dinas PUPR, Kaprodi, dsb" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Tujuan</label>
                            <input type="text" name="pl_target_address" id="pl-target-address"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="cth. Dinas PUPRD Kota Tomohon..." />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lama Izin (Hari)</label>
                            <input type="number" name="pl_total_sick_day" id="pl-total-sick-day"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Awal Cuti</label>
                                <input type="date" name="pl_start_date" id="pl-start-date"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Akhir Cuti</label>
                                <input type="date" name="pl_end_date" id="pl-end-date"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            </div>
                        </div>

                    </div>
                </div>

                {{-- ================================================== --}}
                {{-- Letter of Assignment Form --}}
                {{-- ================================================== --}}
                <div id="letter-of-assignment-form" class="hidden">

                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4 border-b pb-2">
                        Data Surat Tugas Perjalanan Dinas
                    </h3>

                    {{-- Staff selector --}}
                    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <label class="block text-sm font-medium text-blue-700 mb-1">
                            Pilih dari Data Staff (opsional)
                        </label>
                        <select onchange="fillForm('la', this.value)"
                            class="w-full border border-blue-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="">— Pilih staff untuk mengisi otomatis —</option>
                        </select>
                        <p class="text-xs text-blue-500 mt-1">Memilih staff akan mengisi otomatis nama, jabatan, dan
                            alamat.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-4">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pegawai</label>
                            <input type="text" name="la_employee_name" id="la-employee-name"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="John Doe..." />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan Pegawai</label>
                            <input type="text" name="la_employee_position" id="la-employee-position"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Staff ABC..." />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Pegawai</label>
                            <input type="text" name="la_employee_address" id="la-employee-address"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="JL. 123 Tomohon..." />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Surat</label>
                            <input type="text" name="la_letter_number" id="la-letter-number"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="123/ABC/X/YZ..." />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Penulisan Surat</label>
                            <input type="date" name="la_letter_date" id="la-letter-date"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan Tugas</label>
                            <input type="text" name="la_assignment_objective" id="la-assignment-objective"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="perjalanan dinas ke..." />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Instansi Tujuan</label>
                            <input type="text" name="la_destination_agency" id="la-destination-agency"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="PT. ABCD EFG..." />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Berangkat</label>
                                <input type="date" name="la_departure_date" id="la-departure-date"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kembali</label>
                                <input type="date" name="la_return_date" id="la-return-date"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            </div>
                        </div>

                    </div>
                </div>

                {{-- ================================================== --}}
                {{-- Employee Performance Targets Form --}}
                {{-- ================================================== --}}
                <div id="employee-performance-targets-form">

                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4 border-b pb-2">
                        Data Pegawai yang Dinilai
                    </h3>

                    {{-- Staff selector for employee --}}
                    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <label class="block text-sm font-medium text-blue-700 mb-1">
                            Pilih Pegawai yang Dinilai (opsional)
                        </label>
                        <select onchange="fillForm('ept_employee', this.value)"
                            class="w-full border border-blue-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="">— Pilih staff untuk mengisi otomatis —</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 gap-4 mb-6">

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Awal Periode
                                    Penilaian</label>
                                <input type="date" name="ept_appraisal_period_start" id="ept-appraisal-period-start"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Akhir Periode
                                    Penilaian</label>
                                <input type="date" name="ept_appraisal_period_end" id="ept-appraisal-period-end"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pegawai</label>
                            <input type="text" name="ept_employee_name" id="ept-employee-name"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="John Doe..." />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                            <input type="text" name="ept_employee_nip" id="ept-employee-nip"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="7104334234242..." />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pangkat / Gol. Ruang</label>
                            <input type="text" name="ept_employee_rank" id="ept-employee-rank"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Penata Tingkat I..." />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                            <input type="text" name="ept_employee_position" id="ept-employee-position"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Staf Administratif..." />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Unit Kerja Pegawai</label>
                            <input type="text" name="ept_employee_work_unit" id="ept-employee-work-unit"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Unit Administratif..." />
                        </div>

                    </div>

                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4 border-b pb-2">
                        Data Pejabat Penilai
                    </h3>

                    {{-- Staff selector for appraiser --}}
                    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <label class="block text-sm font-medium text-blue-700 mb-1">
                            Pilih Pejabat Penilai (opsional)
                        </label>
                        <select onchange="fillForm('ept_appraisal', this.value)"
                            class="w-full border border-blue-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="">— Pilih staff untuk mengisi otomatis —</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 gap-4 mb-6">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Penilai</label>
                            <input type="text" name="ept_appraisal_name" id="ept-appraisal-name"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Jane Doe..." />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NIP Penilai</label>
                            <input type="text" name="ept_appraisal_nip" id="ept-appraisal-nip"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="71034234235" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pangkat / Gol. Ruang
                                Penilai</label>
                            <input type="text" name="ept_appraisal_rank" id="ept-appraisal-rank"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Penata II" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan Penilai</label>
                            <input type="text" name="ept_appraisal_position" id="ept-appraisal-position"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Staf Administratif" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Unit Kerja Penilai</label>
                            <input type="text" name="ept_appraisal_work_unit" id="ept-appraisal-work-unit"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Unit Staff..." />
                        </div>

                    </div>

                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4 border-b pb-2">
                        Rencana Hasil Kerja
                    </h3>
                    <div class="grid grid-cols-1 gap-4 mb-6">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rencana Hasil Kerja
                                Pimpinan</label>
                            <input type="text" name="ept_leadership_work_result_plan"
                                id="ept-leadership-work-result-plan"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Rencana kerja pimpinan..." />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rencana Hasil Kerja</label>
                            <input type="text" name="ept_work_result_plan" id="ept-work-result-plan"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Rencana hasil kerja..." />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Indikator Kuantitas</label>
                                <input type="text" name="ept_work_quantity_indicator" id="ept-work-quantity-indicator"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Jumlah dokumen..." />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Target Kuantitas</label>
                                <input type="text" name="ept_work_quantity_target" id="ept-work-quantity-target"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="12 dokumen..." />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Indikator Kualitas</label>
                                <input type="text" name="ept_work_quality_indicator" id="ept-work-quality-indicator"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Ketepatan isi..." />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Target Kualitas</label>
                                <input type="text" name="ept_work_quality_target" id="ept-work-quality-target"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="100%..." />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Indikator Waktu</label>
                                <input type="text" name="ept_work_time_indicator" id="ept-work-time-indicator"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Tepat waktu..." />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Target Waktu</label>
                                <input type="text" name="ept_work_time_target" id="ept-work-time-target"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="12 bulan..." />
                            </div>
                        </div>

                    </div>

                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4 border-b pb-2">
                        Perilaku Kerja Tambahan
                    </h3>
                    <div class="grid grid-cols-1 gap-4">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Perilaku Kerja Tambahan</label>
                            <input type="text" name="ept_additional_work_behaviour_1"
                                id="ept-additional-work-behaviour-1"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Integritas..." />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Perilaku Kerja</label>
                            <input type="text" name="ept_additional_work_behaviour_1_description"
                                id="ept-additional-work-behaviour-1-description"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Bertindak jujur dan konsisten..." />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ekspektasi Spesifik
                                Pimpinan</label>
                            <input type="text" name="ept_leadership_spesific_expectation"
                                id="ept-leadership-spesific-expectation"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Meningkatkan kualitas laporan..." />
                        </div>

                    </div>
                </div>

                {{-- Consent + Submit --}}
                <div class="mt-6 pt-4 border-t flex items-center gap-3">
                    <input type="checkbox" id="consent" class="rounded border-gray-300 text-blue-600" required />
                    <label for="consent" class="text-sm text-gray-600">
                        Saya menyatakan bahwa informasi yang saya berikan adalah benar adanya.
                    </label>
                </div>

                <button type="submit"
                    class="mt-4 w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg text-sm transition">
                    Buat Dokumen
                </button>

            </form>
        </div>
    </main>

    <script>
        // ============================================================
        // Form section show/hide
        // ============================================================
        const formSections = [
            'permission-letter-form',
            'letter-of-assignment-form',
            'employee-performance-targets-form',
        ];

        function showForm(selectedValue) {
            formSections.forEach(function (sectionId) {
                document.getElementById(sectionId).classList.add('hidden');
            });
            const map = {
                'permission-letter': 'permission-letter-form',
                'letter-of-assignment': 'letter-of-assignment-form',
                'employee-performance-targets': 'employee-performance-targets-form',
            };
            if (map[selectedValue]) {
                document.getElementById(map[selectedValue]).classList.remove('hidden');
            }
        }

        // ============================================================
        // Staff data — fetched once on page load, shared by all dropdowns
        // ============================================================
        let staffData = [];

        async function loadStaffData() {
            try {
                const response = await fetch('{{ route('api.staff') }}');
                staffData = await response.json();
                populateAllDropdowns();
            } catch (e) {
                console.warn('Could not load staff data:', e);
            }
        }

        function populateAllDropdowns() {
            const dropdowns = document.querySelectorAll('select[onchange^="fillForm"]');
            dropdowns.forEach(function (select) {
                // Keep the placeholder option
                const placeholder = select.options[0];
                select.innerHTML = '';
                select.appendChild(placeholder);

                staffData.forEach(function (staff) {
                    const option = document.createElement('option');
                    option.value = staff.id;
                    option.textContent = staff.staff_name + (staff.nip ? ' — ' + staff.nip : '');
                    select.appendChild(option);
                });
            });
        }

        // ============================================================
        // Auto-fill logic
        // prefix = 'pl' | 'la' | 'ept_employee' | 'ept_appraisal'
        // ============================================================
        function fillForm(prefix, staffId) {
            if (!staffId) return;

            const staff = staffData.find(s => s.id == staffId);
            if (!staff) return;

            // Map of prefix → which fields to fill and their field-name suffixes
            const fieldMaps = {
                'pl': {
                    'employee-name': staff.staff_name,
                    'employee-position': staff.position ?? '',
                    'employee-id-number': staff.nip ?? '',
                },
                'la': {
                    'employee-name': staff.staff_name,
                    'employee-position': staff.position ?? '',
                    'employee-address': staff.work_unit ?? '',
                },
                'ept_employee': {
                    'employee-name': staff.staff_name,
                    'employee-nip': staff.nip ?? '',
                    'employee-rank': staff.rank ?? '',
                    'employee-position': staff.position ?? '',
                    'employee-work-unit': staff.work_unit ?? '',
                },
                'ept_appraisal': {
                    'appraisal-name': staff.staff_name,
                    'appraisal-nip': staff.nip ?? '',
                    'appraisal-rank': staff.rank ?? '',
                    'appraisal-position': staff.position ?? '',
                    'appraisal-work-unit': staff.work_unit ?? '',
                },
            };

            const fields = fieldMaps[prefix];
            if (!fields) return;

            // Derive the HTML id prefix (pl → pl, la → la, ept_employee → ept, ept_appraisal → ept)
            const idPrefix = prefix.replace('_employee', '').replace('_appraisal', '');

            Object.entries(fields).forEach(function ([fieldSuffix, value]) {
                const el = document.getElementById(idPrefix + '-' + fieldSuffix);
                if (el && value) el.value = value;
            });
        }

        // Load staff data when the page is ready
        document.addEventListener('DOMContentLoaded', loadStaffData);
    </script>

</body>

</html>