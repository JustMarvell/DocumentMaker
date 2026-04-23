<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use App\Models\DocumentField;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // permission letter
        $pl = DocumentType::updateOrCreate(['key' => 'permission-letter'], [
            'name' => 'Surat Izin Sakit',
            'script_name' => 'docx_generator.py',
            'template_filename' => 'surat-izin-sakit.docx',
            'output_filename' => 'surat-izin-sakit',
            'access_level' => 'guest',
            'file_type' => 'docx',
            'staff_autofill_role' => 'employee',
            'is_active' => true,
        ]);

        $this->seedFields($pl, [
            ['field_key' => 'employee_name', 'label' => 'Nama Pegawai', 'field_type' => 'text', 'is_required' => true, 'sort_order' => 1, 'section_label' => 'Data Pegawai', 'staff_autofill_column' => 'staff_name'],
            ['field_key' => 'employee_position', 'label' => 'Jabatan Karyawan', 'field_type' => 'text', 'is_required' => true, 'sort_order' => 2, 'staff_autofill_column' => 'position'],
            ['field_key' => 'employee_id_number', 'label' => 'Nomor Induk Karyawan', 'field_type' => 'text', 'is_required' => false, 'sort_order' => 3, 'staff_autofill_column' => 'nip'],
            ['field_key' => 'employee_address', 'label' => 'Alamat Pegawai', 'field_type' => 'text', 'is_required' => true, 'sort_order' => 4],
            ['field_key' => 'letter_address', 'label' => 'Alamat Surat', 'field_type' => 'text', 'is_required' => false, 'sort_order' => 5, 'section_label' => 'Data Surat'],
            ['field_key' => 'letter_date', 'label' => 'Tanggal Surat', 'field_type' => 'date', 'is_required' => true, 'sort_order' => 6],
            ['field_key' => 'attachment_count', 'label' => 'Banyaknya Lampiran', 'field_type' => 'number', 'is_required' => false, 'sort_order' => 7],
            ['field_key' => 'target_name', 'label' => 'Tujuan Surat', 'field_type' => 'text', 'is_required' => true, 'sort_order' => 8],
            ['field_key' => 'target_address', 'label' => 'Alamat Tujuan', 'field_type' => 'text', 'is_required' => true, 'sort_order' => 9],
            ['field_key' => 'total_sick_day', 'label' => 'Lama Izin (Hari)', 'field_type' => 'number', 'is_required' => true, 'sort_order' => 10],
            ['field_key' => 'start_date', 'label' => 'Awal Cuti', 'field_type' => 'date', 'is_required' => true, 'sort_order' => 11],
            ['field_key' => 'end_date', 'label' => 'Akhir Cuti', 'field_type' => 'date', 'is_required' => true, 'sort_order' => 12],
        ]);

        // letter of assignment
        $la = DocumentType::updateOrCreate(['key' => 'letter-of-assignment'], [
            'name' => 'Surat Tugas Perjalanan Dinas',
            'script_name' => 'docx_generator.py',
            'template_filename' => 'Surat-tugas-perjalanan-dinas.docx',
            'output_filename' => 'surat-tugas-perjalanan-dinas',
            'access_level' => 'staff',
            'file_type' => 'docx',
            'staff_autofill_role' => 'employee',
            'is_active' => true,
        ]);

        $this->seedFields($la, [
            ['field_key' => 'employee_name', 'label' => 'Nama Pegawai', 'field_type' => 'text', 'is_required' => true, 'sort_order' => 1, 'section_label' => 'Data Pegawai', 'staff_autofill_column' => 'staff_name'],
            ['field_key' => 'employee_position', 'label' => 'Jabatan Pegawai', 'field_type' => 'text', 'is_required' => true, 'sort_order' => 2, 'staff_autofill_column' => 'position'],
            ['field_key' => 'employee_address', 'label' => 'Alamat Pegawai', 'field_type' => 'text', 'is_required' => true, 'sort_order' => 3, 'staff_autofill_column' => 'work_unit'],
            ['field_key' => 'letter_number', 'label' => 'Nomor Surat', 'field_type' => 'text', 'is_required' => true, 'sort_order' => 4, 'section_label' => 'Data Surat'],
            ['field_key' => 'letter_date', 'label' => 'Tanggal Penulisan Surat', 'field_type' => 'date', 'is_required' => true, 'sort_order' => 5],
            ['field_key' => 'assignment_objective', 'label' => 'Tujuan Tugas', 'field_type' => 'text', 'is_required' => true, 'sort_order' => 6],
            ['field_key' => 'destination_agency', 'label' => 'Instansi Tujuan', 'field_type' => 'text', 'is_required' => true, 'sort_order' => 7],
            ['field_key' => 'departure_date', 'label' => 'Tanggal Berangkat', 'field_type' => 'date', 'is_required' => true, 'sort_order' => 8],
            ['field_key' => 'return_date', 'label' => 'Tanggal Kembali', 'field_type' => 'date', 'is_required' => true, 'sort_order' => 9],
        ]);

        // employee performance target
        $skp = DocumentType::updateOrCreate(['key' => 'employee-performance-targets'], [
            'name' => 'Sasaran Kinerja Pegawai',
            'script_name' => 'xlsx_generator.py',
            'template_filename' => 'SKP-Template.xlsx',
            'output_filename' => 'sasaran-kerja-pegawai',
            'access_level' => 'staff',
            'file_type' => 'xlsx',
            'staff_autofill_role' => 'both',
            'is_active' => true,
        ]);

        $this->seedFields($skp, [
            ['field_key' => 'appraisal_period_start', 'label' => 'Awal Periode Penilaian', 'field_type' => 'date', 'is_required' => true, 'sort_order' => 1, 'section_label' => 'Periode', 'autofill_role' => 'none'],
            ['field_key' => 'appraisal_period_end', 'label' => 'Akhir Periode Penilaian', 'field_type' => 'date', 'is_required' => true, 'sort_order' => 2, 'autofill_role' => 'none'],
            ['field_key' => 'employee_name', 'label' => 'Nama Pegawai', 'field_type' => 'text', 'is_required' => true, 'sort_order' => 3, 'section_label' => 'Data Pegawai', 'staff_autofill_column' => 'staff_name', 'autofill_role' => 'employee'],
            ['field_key' => 'employee_nip', 'label' => 'NIP', 'field_type' => 'text', 'is_required' => true, 'sort_order' => 4, 'staff_autofill_column' => 'nip', 'autofill_role' => 'employee'],
            ['field_key' => 'employee_rank', 'label' => 'Pangkat / Gol. Ruang', 'field_type' => 'text', 'is_required' => true, 'sort_order' => 5, 'staff_autofill_column' => 'rank', 'autofill_role' => 'employee'],
            ['field_key' => 'employee_position', 'label' => 'Jabatan', 'field_type' => 'text', 'is_required' => true, 'sort_order' => 6, 'staff_autofill_column' => 'position', 'autofill_role' => 'employee'],
            ['field_key' => 'employee_work_unit', 'label' => 'Unit Kerja Pegawai', 'field_type' => 'text', 'is_required' => true, 'sort_order' => 7, 'staff_autofill_column' => 'work_unit', 'autofill_role' => 'employee'],
            ['field_key' => 'appraisal_name', 'label' => 'Nama Penilai', 'field_type' => 'text', 'is_required' => true, 'sort_order' => 8, 'section_label' => 'Data Penilai', 'staff_autofill_column' => 'staff_name', 'autofill_role' => 'appraiser'],
            ['field_key' => 'appraisal_nip', 'label' => 'NIP Penilai', 'field_type' => 'text', 'is_required' => true, 'sort_order' => 9, 'staff_autofill_column' => 'nip', 'autofill_role' => 'appraiser'],
            ['field_key' => 'appraisal_rank', 'label' => 'Pangkat / Gol. Ruang Penilai', 'field_type' => 'text', 'is_required' => true, 'sort_order' => 10, 'staff_autofill_column' => 'rank', 'autofill_role' => 'appraiser'],
            ['field_key' => 'appraisal_position', 'label' => 'Jabatan Penilai', 'field_type' => 'text', 'is_required' => true, 'sort_order' => 11, 'staff_autofill_column' => 'position', 'autofill_role' => 'appraiser'],
            ['field_key' => 'appraisal_work_unit', 'label' => 'Unit Kerja Penilai', 'field_type' => 'text', 'is_required' => true, 'sort_order' => 12, 'staff_autofill_column' => 'work_unit', 'autofill_role' => 'appraiser'],
            ['field_key' => 'leadership_work_result_plan', 'label' => 'Rencana Hasil Kerja Pimpinan', 'field_type' => 'text', 'is_required' => true, 'sort_order' => 13, 'section_label' => 'Rencana Hasil Kerja', 'autofill_role' => 'none'],
            ['field_key' => 'work_result_plan', 'label' => 'Rencana Hasil Kerja', 'field_type' => 'text', 'is_required' => true, 'sort_order' => 14, 'autofill_role' => 'none'],
            ['field_key' => 'work_quantity_indicator', 'label' => 'Indikator Kuantitas', 'field_type' => 'text', 'is_required' => true, 'sort_order' => 15, 'autofill_role' => 'none'],
            ['field_key' => 'work_quantity_target', 'label' => 'Target Kuantitas', 'field_type' => 'text', 'is_required' => true, 'sort_order' => 16, 'autofill_role' => 'none'],
            ['field_key' => 'work_quality_indicator', 'label' => 'Indikator Kualitas', 'field_type' => 'text', 'is_required' => true, 'sort_order' => 17, 'autofill_role' => 'none'],
            ['field_key' => 'work_quality_target', 'label' => 'Target Kualitas', 'field_type' => 'text', 'is_required' => true, 'sort_order' => 18, 'autofill_role' => 'none'],
            ['field_key' => 'work_time_indicator', 'label' => 'Indikator Waktu', 'field_type' => 'text', 'is_required' => true, 'sort_order' => 19, 'autofill_role' => 'none'],
            ['field_key' => 'work_time_target', 'label' => 'Target Waktu', 'field_type' => 'text', 'is_required' => true, 'sort_order' => 20, 'autofill_role' => 'none'],
            ['field_key' => 'additional_work_behaviour_1', 'label' => 'Perilaku Kerja Tambahan', 'field_type' => 'text', 'is_required' => true, 'sort_order' => 21, 'section_label' => 'Perilaku Kerja', 'autofill_role' => 'none'],
            ['field_key' => 'additional_work_behaviour_1_description', 'label' => 'Deskripsi Perilaku Kerja', 'field_type' => 'text', 'is_required' => true, 'sort_order' => 22, 'autofill_role' => 'none'],
            ['field_key' => 'leadership_spesific_expectation', 'label' => 'Ekspektasi Spesifik Pimpinan', 'field_type' => 'text', 'is_required' => true, 'sort_order' => 23, 'autofill_role' => 'none'],
        ]);
    }

    private function seedFields(DocumentType $type, array $fields): void
    {
        foreach ($fields as $field) {
            DocumentField::updateOrCreate(
                [
                    'document_type_id' => $type->id,
                    'field_key' => $field['field_key'],
                ],
                array_merge([
                    'document_type_id' => $type->id,
                    'field_type' => 'text',
                    'is_required' => false,
                    'sort_order' => 0,
                    'section_label' => null,
                    'group_key' => null,
                    'is_group_child' => false,
                    'staff_autofill_column' => null,
                    'autofill_role' => 'none',   // new default
                    'field_options' => null,
                ], $field)
            );
        }
    }
}
