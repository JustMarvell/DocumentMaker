<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'Surat Izin Sakit',
                'key' => 'permission-letter',
                'script_name' => 'PermissionLetterGenerator.py',
                'template_filename' => 'surat-izin-sakit.docx',
                'output_filename' => 'surat-izin-sakit',
                'access_level' => 'guest',
                'is_active' => true,
            ],
            [
                'name' => 'Surat Tugas Perjalanan Dinas',
                'key' => 'letter-of-assignment',
                'script_name' => 'LetterOfAssignmentGenerator.py',
                'template_filename' => 'surat-tugas-perjalanan-dinas.docx',
                'output_filename' => 'surat-tugas-perjalanan-dinas.docx',
                'access_level' => 'staff',
                'is_active' => true,
            ],
            [
                'name' => 'Sasaran Kinerja Pegawai',
                'key' => 'employee-performance-targets',
                'script_name' => 'EmployeePerformanceTargetGenerator.py',
                'template_filename' => 'SKP-Template.xlsx',
                'output_filename' => 'sasaran-kinerja-pegawai',
                'access_level' => 'staff',
                'is_active' => true,
            ],
        ];

        foreach ($types as $type) {
            DocumentType::updateOrCreate(['key' => $type['key']], $type);
        }
    }
}
