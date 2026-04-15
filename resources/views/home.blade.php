<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>--Prototype--</title>

    @vite(['resources/css/home.css'])
</head>
<body>
    <div class="main-container">
        <div class="form-wrapper">
            <img
                src="https://ucarecdn.com/0bba3782-1df4-44db-86a5-2c3bd573488a/employeeavailability.png"
                alt="Employee Availability Form Illustration"
                class="form-image"
            />

            <form action="{{ route('document.generate') }}" method="POST">
                @csrf

                <!-- Title and Description -->
                <div class="form-header">
                    <h2 class="form-title">[Prototype] Sistem Automatisasi Surat/Dokumen</h2>
                    <p class="form-description">
                        prototype bertujuan untuk memudahkan karyawan untuk membuat surat baik itu surat izin,
                        surat tugas, surat keterangan, dan lain-lain.
                    </p>
                    <p class="form-description">
                        Mohon isi form dibawah berdasarkan data dan isi yang benar.
                    </p>
                </div>

                {{-- Show validation errors if any --}}
                @if ($errors->any())
                    <div class="error-box">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Show success message --}}
                @if (session('success'))
                    <div class="success-box">
                        {{ session('success') }}
                        @if (session('download_url'))
                            <a href="{{ session('download_url') }}" class="download-link">Unduh Surat</a>
                        @endif
                    </div>
                @endif

                {{-- Show error message from generation --}}
                @if (session('error'))
                    <div class="error-box">{{ session('error') }}</div>
                @endif

                {{-- Choose Template --}}
                <div class="form-group">
                    <label for="letter-type" class="form-label">Jenis Surat</label>
                    <select
                        name="letter-type"
                        id="letter-template-type"
                        class="form-select"
                        onchange="showForm(this.value)"
                    >
                        @foreach ($documentTypes as $type)
                            <option value="{{ $type->key }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- ======================================================= --}}
                {{-- Permission Letter Form                                   --}}
                {{-- ======================================================= --}}
                <div id="permission-letter-form" class="permission-letter-form">

                    <div class="form-group">
                        <label for="pl-employee-name" class="form-label">Nama Pegawai</label>
                        <input type="text" name="pl_employee_name" id="pl-employee-name"
                            class="form-input" placeholder="John Doe..." />
                    </div>

                    <div class="form-group">
                        <label for="pl-employee-address" class="form-label">Alamat Pegawai</label>
                        <input type="text" name="pl_employee_address" id="pl-employee-address"
                            class="form-input" placeholder="JL. 123 Tomohon..." />
                    </div>

                    <div class="form-group">
                        <label for="pl-letter-address" class="form-label">Alamat Surat</label>
                        <input type="text" name="pl_letter_address" id="pl-letter-address"
                            class="form-input" placeholder="Tomohon..." />
                    </div>

                    <div class="form-group">
                        <label for="pl-letter-date" class="form-label">Tanggal Surat</label>
                        <input type="date" name="pl_letter_date" id="pl-letter-date"
                            class="form-input" />
                    </div>

                    <div class="form-group">
                        <label for="pl-employee-id-number" class="form-label">Nomor Induk Karyawan</label>
                        <input type="text" name="pl_employee_id_number" id="pl-employee-id-number"
                            class="form-input" placeholder="7103123456789..." />
                    </div>

                    <div class="form-group">
                        <label for="pl-employee-position" class="form-label">Jabatan Karyawan</label>
                        <input type="text" name="pl_employee_position" id="pl-employee-position"
                            class="form-input" placeholder="Staff ABC..." />
                    </div>

                    <div class="form-group">
                        <label for="pl-attachment-count" class="form-label">Banyaknya Lampiran</label>
                        <input type="number" name="pl_attachment_count" id="pl-attachment-count"
                            class="form-input" />
                    </div>

                    <div class="form-group">
                        <label for="pl-target-name" class="form-label">Tujuan Surat</label>
                        <input type="text" name="pl_target_name" id="pl-target-name"
                            class="form-input" placeholder="cth. Pimpinan Dinas PUPR, Kaprodi, dsb" />
                    </div>

                    <div class="form-group">
                        <label for="pl-target-address" class="form-label">Alamat Tujuan</label>
                        <input type="text" name="pl_target_address" id="pl-target-address"
                            class="form-input" placeholder="cth. Dinas PUPRD Kota Tomohon..." />
                    </div>

                    <div class="form-group">
                        <label for="pl-total-sick-day" class="form-label">Lama Izin (Hari)</label>
                        <input type="number" name="pl_total_sick_day" id="pl-total-sick-day"
                            class="form-input" />
                    </div>

                    <div class="form-group">
                        <label for="pl-start-date" class="form-label">Awal Cuti</label>
                        <input type="date" name="pl_start_date" id="pl-start-date"
                            class="form-input" />
                    </div>

                    <div class="form-group">
                        <label for="pl-end-date" class="form-label">Akhir Cuti</label>
                        <input type="date" name="pl_end_date" id="pl-end-date"
                            class="form-input" />
                    </div>
                </div>

                {{-- ======================================================= --}}
                {{-- Letter of Assignment Form                                --}}
                {{-- ======================================================= --}}
                <div id="letter-of-assignment-form" class="letter-of-assignment-form hidden">

                    <div class="form-group">
                        <label for="la-employee-name" class="form-label">Nama Pegawai</label>
                        <input type="text" name="la_employee_name" id="la-employee-name"
                            class="form-input" placeholder="John Doe..." />
                    </div>

                    <div class="form-group">
                        <label for="la-employee-position" class="form-label">Jabatan Pegawai</label>
                        <input type="text" name="la_employee_position" id="la-employee-position"
                            class="form-input" placeholder="Staff ABC..." />
                    </div>

                    <div class="form-group">
                        <label for="la-employee-address" class="form-label">Alamat Pegawai</label>
                        <input type="text" name="la_employee_address" id="la-employee-address"
                            class="form-input" placeholder="JL. 123 Tomohon..." />
                    </div>

                    <div class="form-group">
                        <label for="la-letter-number" class="form-label">Nomor Surat</label>
                        <input type="text" name="la_letter_number" id="la-letter-number"
                            class="form-input" placeholder="123/ABC/X/YZ..." />
                    </div>

                    <div class="form-group">
                        <label for="la-letter-date" class="form-label">Tanggal Penulisan Surat</label>
                        <input type="date" name="la_letter_date" id="la-letter-date"
                            class="form-input" />
                    </div>

                    <div class="form-group">
                        <label for="la-assignment-objective" class="form-label">Tujuan Tugas</label>
                        <input type="text" name="la_assignment_objective" id="la-assignment-objective"
                            class="form-input" placeholder="perjalanan dinas..." />
                    </div>

                    <div class="form-group">
                        <label for="la-destination-agency" class="form-label">Instansi Tujuan</label>
                        <input type="text" name="la_destination_agency" id="la-destination-agency"
                            class="form-input" placeholder="PT. ABCD EFG..." />
                    </div>

                    <div class="form-group">
                        <label for="la-departure-date" class="form-label">Tanggal Berangkat</label>
                        <input type="date" name="la_departure_date" id="la-departure-date"
                            class="form-input" />
                    </div>

                    <div class="form-group">
                        <label for="la-return-date" class="form-label">Tanggal Kembali</label>
                        <input type="date" name="la_return_date" id="la-return-date"
                            class="form-input" />
                    </div>
                </div>

                {{-- ======================================================= --}}
                {{-- Employee Performance Targets form                       --}}
                {{-- ======================================================= --}}

                <div id="employee-performance-targets-form" class="employee-performance-targets-form hidden">
                    <div class="form-group">
                        <label for="ept-appraisal-period-start" class="form-label">AWAL PERIODE PENILAIAN</label>
                        <input type="date" name="ept_appraisal_period_start" id="ept-appraisal-period-start"
                            class="form-input" />
                    </div>

                    <div class="form-group">
                        <label for="ept-appraisal-period-end" class="form-label">AKHIR PERIODE PENILAIAN</label>
                        <input type="date" name="ept_appraisal_period_end" id="ept-appraisal-period-end"
                            class="form-input" />
                    </div>

                    <div class="form-group">
                        <label for="ept-employee-name" class="form-label">NAMA PEGAWAI</label>
                        <input type="text" name="ept_employee_name" id="ept-employee-name"
                            class="form-input" placeholder="John Doe..." />
                    </div>

                    <div class="form-group">
                        <label for="ept-employee-nip" class="form-label">NIP</label>
                        <input type="text" name="ept_employee_nip" id="ept-employee-nip"
                            class="form-input" placeholder="7104334234242..." />
                    </div>

                    <div class="form-group">
                        <label for="ept-employee-rank" class="form-label">PANGKAT / GOL. RUANG</label>
                        <input type="text" name="ept_employee_rank" id="ept-employee-rank"
                            class="form-input" placeholder="Penata Tingat I..." />
                    </div>

                    <div class="form-group">
                        <label for="ept-employee-position" class="form-label">JABATAN</label>
                        <input type="text" name="ept_employee_position" id="ept-employee-position"
                            class="form-input" placeholder="Staf Administratif..." />
                    </div>

                    <div class="form-group">
                        <label for="ept-employee-work-unit" class="form-label">UNIT KERJA PEGAWAI</label>
                        <input type="text" name="ept_employee_work_unit" id="ept-employee-work-unit"
                            class="form-input" placeholder="Unit Administratif..." />
                    </div>

                    <div class="form-group">
                        <label for="ept-appraisal-name" class="form-label">NAMA PENILAI</label>
                        <input type="text" name="ept_appraisal_name" id="ept-appraisal-name"
                            class="form-input" placeholder="Jane Doe..." />
                    </div>

                    <div class="form-group">
                        <label for="ept-appraisal-nip" class="form-label">NIP PENILAI</label>
                        <input type="text" name="ept_appraisal_nip" id="ept-appraisal-nip"
                            class="form-input" placeholder="71034234235" />
                    </div>

                    <div class="form-group">
                        <label for="ept-appraisal-rank" class="form-label">PANGKAT / GOL. RUANG PENILAI</label>
                        <input type="text" name="ept_appraisal_rank" id="ept-appraisal-rank"
                            class="form-input" placeholder="Penata II" />
                    </div>

                    <div class="form-group">
                        <label for="ept-appraisal-position" class="form-label">JABATAN PENILAI</label>
                        <input type="text" name="ept_appraisal_position" id="ept-appraisal-position"
                            class="form-input" placeholder="Staf Administratif" />
                    </div>

                    <div class="form-group">
                        <label for="ept-appraisal-work-unit" class="form-label">UNIT KERJA PENILAI</label>
                        <input type="text" name="ept_appraisal_work_unit" id="ept-appraisal-work-unit"
                            class="form-input" placeholder="Unit Staff..." />
                    </div>

                    <div class="form-group">
                        <label for="ept-leadership-work-result-plan" class="form-label">RENCANA HASIL KERJA PIMPINAN</label>
                        <input type="text" name="ept_leadership_work_result_plan" id="ept-leadership-work-result-plan"
                            class="form-input" placeholder="Work plan..." />
                    </div>

                    <div class="form-group">
                        <label for="ept-work-result-plan" class="form-label">RENCANA HASIL KERJA</label>
                        <input type="text" name="ept_work_result_plan" id="ept-work-result-plan"
                            class="form-input" placeholder="Work plan..." />
                    </div>

                    <div class="form-group">
                        <label for="ept-work-quantity-indicator" class="form-label">INDIKATOR KUANTITAS KERJA</label>
                        <input type="text" name="ept_work_quantity_indicator" id="ept-work-quantity-indicator"
                            class="form-input" placeholder="Work quantity indicator..." />
                    </div>

                    <div class="form-group">
                        <label for="ept-work-quality-indicator" class="form-label">INDIKATOR KUALITAS KERJA</label>
                        <input type="text" name="ept_work_quality_indicator" id="ept-work-quality-indicator"
                            class="form-input" placeholder="Work quality indicator..." />
                    </div>

                    <div class="form-group">
                        <label for="ept-work-time-indicator" class="form-label">INDIKATOR WAKTU KERJA</label>
                        <input type="text" name="ept_work_time_indicator" id="ept-work-time-indicator"
                            class="form-input" placeholder="Work time indicator..." />
                    </div>

                    <div class="form-group">
                        <label for="ept-work-quantity-target" class="form-label">TARGET KUANTITAS KERJA</label>
                        <input type="text" name="ept_work_quantity_target" id="ept-work-quantity-target"
                            class="form-input" placeholder="Work quantity target..." />
                    </div>

                    <div class="form-group">
                        <label for="ept-work-quality-target" class="form-label">TARGET KUALITAS KERJA</label>
                        <input type="text" name="ept_work_quality_target" id="ept-work-quality-target"
                            class="form-input" placeholder="Work quality target..." />
                    </div>

                    <div class="form-group">
                        <label for="ept-work-time-target" class="form-label">TARGET WAKTU KERJA</label>
                        <input type="text" name="ept_work_time_target" id="ept-work-time-target"
                            class="form-input" placeholder="Work time target..." />
                    </div>

                    <div class="form-group">
                        <label for="ept-work-quantity-indicator" class="form-label">INDIKATOR KUANTITAS KERJA</label>
                        <input type="text" name="ept_work_quantity_indicator" id="ept-work-quantity-indicator"
                            class="form-input" placeholder="Work quantity indicator..." />
                    </div>

                    <div class="form-group">
                        <label for="ept-work-quality-indicator" class="form-label">INDIKATOR KUALITAS KERJA</label>
                        <input type="text" name="ept_work_quality_indicator" id="ept-work-quality-indicator"
                            class="form-input" placeholder="Work quality indicator..." />
                    </div>

                    <div class="form-group">
                        <label for="ept-additional-work-behaviour-1" class="form-label">PERILAKU KERJA TAMBAHAN</label>
                        <input type="text" name="ept_additional_work_behaviour_1" id="ept-additional-work-behaviour-1"
                            class="form-input" placeholder="Additional work behaviour..." />
                    </div>

                    <div class="form-group">
                        <label for="ept-additional-work-behaviour-1-description" class="form-label">PERILAKU KERJA TAMBAHAN</label>
                        <input type="text" name="ept_additional_work_behaviour_1_description" id="ept-additional-work-behaviour-1-description"
                            class="form-input" placeholder="Additional work behaviour description..." />
                    </div>

                    <div class="form-group">
                        <label for="ept-leadership-spesific-expectation" class="form-label">EKSPEKTASI SPESIFIK PEMIMPIN</label>
                        <input type="text" name="ept_leadership_spesific_expectation" id="ept-leadership-spesific-expectation"
                            class="form-input" placeholder="Spesific expectation..." />
                    </div>
                </div>

                {{-- Consent --}}
                <div class="consent-group">
                    <input type="checkbox" id="consent" class="form-checkbox" required />
                    <label for="consent" class="consent-label">
                        Saya dengan ini menyatakan bahwa informasi yang saya berikan adalah benar adanya.
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="submit-btn">Buat Surat</button>
            </form>
        </div>
    </div>

    <script>
        const formSections = [
            'permission-letter-form',
            'letter-of-assignment-form',
            'employee-performance-targets-form',
        ];

        function showForm(selectedValue) {
            formSections.forEach(function(sectionId) {
                document.getElementById(sectionId).classList.add('hidden');
            });

            const map = {
                'permission-letter' : 'permission-letter-form',
                'letter-of-assignment' : 'letter-of-assignment-form',
                'employee-performance-targets' : 'employee-performance-targets-form',
            };

            if (map[selectedValue]) {
                document.getElementById(map[selectedValue]).classList.remove('hidden');
            }
        }
    </script>
</body>
</html>