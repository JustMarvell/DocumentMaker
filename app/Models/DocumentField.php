<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentField extends Model
{
    protected $fillable = [
        'document_type_id',
        'field_key',
        'label',
        'field_type',
        'field_options',
        'is_required',
        'sort_order',
        'row_group',
        'section_label',
        'group_key',
        'is_group_child',
        'staff_autofill_column',
        'autofill_role',
        'icon',
    ];

    protected function casts() : array {
        return [
            'field_options' => 'array',
            'is_required' => 'boolean',
            'is_group_child' => 'boolean',
        ];
    }

    // relation type shii
    public function documentType() : BelongsTo {
        return $this->belongsTo(DocumentType::class);
    }

    // child field repeating group typa shii
    public function children(): HasMany {
        return $this->hasMany(DocumentField::class, 'group_key', 'field_key')
            ->where('document_type_id', $this->document_type_id)
            ->where('is_group_child', true)
            ->orderBy('sort_order');
    }

    public static function fieldTypes(): array
    {
        return [
            'text' => 'Text',
            'textarea' => 'Textarea',
            'date' => 'Date',
            'number' => 'Number',
            'select' => 'Select (Dropdown)',
            'checkbox' => 'Checkbox',
            'repeating_group' => 'Repeating Group (Loop)',
            'staff_loop' => 'Staff Loop (pilih dari data staff)',
            'official_loop' => 'Official Loop (pilih dari data pejabat)',
        ];
    }

    public static function staffColumns() : array {
        return [
            'staff_name' => 'Nama Staff',
            'nip' => 'NIP',
            'email' => 'Email',
            'phone_number' => 'No. HP',
            'rank' => 'Jabatan / Gol. Pangkat',
            'position' => 'Posisi',
            'work_unit' => 'Unit Kerja',
        ];
    }

    // fa icon list
    public static function availableIcons(): array
    {
        return [
            'Teks & Input' => [
                'fa-solid fa-font' => 'Text',
                'fa-solid fa-align-left' => 'Textarea',
                'fa-solid fa-i-cursor' => 'Input / cursor',
                'fa-solid fa-keyboard' => 'Keyboard',
                'fa-solid fa-pen' => 'Pen / tulis',
                'fa-solid fa-pen-to-square' => 'Edit',
                'fa-solid fa-pencil' => 'Pencil',
                'fa-solid fa-signature' => 'Tanda tangan',
            ],
            'Angka & Tanggal' => [
                'fa-solid fa-calendar' => 'Kalender',
                'fa-solid fa-calendar-days' => 'Kalender (hari)',
                'fa-solid fa-clock' => 'Waktu / jam',
                'fa-solid fa-hashtag' => 'Angka / nomor',
                'fa-solid fa-calculator' => 'Kalkulator',
            ],
            'Pilihan & Centang' => [
                'fa-solid fa-square-check' => 'Checkbox',
                'fa-solid fa-check' => 'Centang',
                'fa-solid fa-circle-dot' => 'Radio / pilih satu',
                'fa-solid fa-list-ul' => 'Dropdown / daftar',
                'fa-solid fa-chevron-down' => 'Dropdown (chevron)',
                'fa-solid fa-sliders' => 'Pilihan / opsi',
                'fa-solid fa-toggle-on' => 'Toggle',
            ],
            'Dokumen & File' => [
                'fa-solid fa-file' => 'File',
                'fa-solid fa-file-lines' => 'File teks',
                'fa-solid fa-file-word' => 'File Word',
                'fa-solid fa-file-excel' => 'File Excel',
                'fa-solid fa-paperclip' => 'Lampiran',
                'fa-solid fa-folder' => 'Folder',
                'fa-solid fa-folder-open' => 'Folder terbuka',
                'fa-solid fa-clipboard' => 'Clipboard',
                'fa-solid fa-clipboard-list' => 'Clipboard list',
            ],
            'Data & Tabel' => [
                'fa-solid fa-table' => 'Tabel',
                'fa-solid fa-table-list' => 'Tabel list',
                'fa-solid fa-layer-group' => 'Grup / layer',
                'fa-solid fa-list' => 'List',
                'fa-solid fa-bars' => 'Baris data',
                'fa-solid fa-database' => 'Database',
                'fa-solid fa-repeat' => 'Repeating group',
                'fa-solid fa-chart-simple' => 'Chart Simpel'
            ],
            'Orang & Organisasi' => [
                'fa-solid fa-user' => 'Pengguna',
                'fa-solid fa-users' => 'Pengguna (banyak)',
                'fa-solid fa-user-tie' => 'Pegawai / staf',
                'fa-solid fa-user-check' => 'Pegawai terverifikasi',
                'fa-solid fa-user-group' => 'Grup pegawai',
                'fa-solid fa-person' => 'Orang',
                'fa-solid fa-id-card' => 'ID card / KTP',
                'fa-solid fa-address-card' => 'Kartu identitas',
                'fa-solid fa-id-badge' => 'Badge',
                'fa-solid fa-briefcase' => 'Jabatan / pekerjaan',
                'fa-solid fa-building' => 'Instansi / kantor',
                'fa-solid fa-building-columns' => 'Institusi / gedung',
                'fa-regular fa-building' => 'Gedung (outline)',
            ],
            'Surat & Administrasi' => [
                'fa-solid fa-envelope' => 'Surat / email',
                'fa-solid fa-envelope-open-text' => 'Surat terbuka',
                'fa-solid fa-paper-plane' => 'Kirim surat',
                'fa-solid fa-inbox' => 'Kotak masuk',
                'fa-solid fa-stamp' => 'Cap / stempel',
                'fa-solid fa-scroll' => 'Surat resmi / scroll',
                'fa-solid fa-file-contract' => 'Kontrak / perjanjian',
                'fa-solid fa-file-signature' => 'Dokumen ditandatangani',
            ],
            'Lokasi & Alamat' => [
                'fa-solid fa-location-dot' => 'Lokasi',
                'fa-solid fa-map-location-dot' => 'Peta',
                'fa-solid fa-map-pin' => 'Pin lokasi',
                'fa-solid fa-road' => 'Jalan / alamat',
                'fa-solid fa-city' => 'Kota',
                'fa-solid fa-house' => 'Rumah / domisili',
                'fa-solid fa-flag' => 'Wilayah / flag',
                'fa-solid fa-globe' => 'Globe'
            ],
            'Kontak & Komunikasi' => [
                'fa-solid fa-phone' => 'Telepon',
                'fa-solid fa-mobile-screen' => 'HP / mobile',
                'fa-solid fa-fax' => 'Fax',
                'fa-solid fa-at' => 'Email / @',
                'fa-solid fa-comment' => 'Komentar',
                'fa-solid fa-link' => 'Link'
            ],
            'Keuangan & Angka' => [
                'fa-solid fa-money-bill' => 'Uang / nominal',
                'fa-solid fa-coins' => 'Koin / biaya',
                'fa-solid fa-receipt' => 'Kwitansi',
                'fa-solid fa-percent' => 'Persentase',
                'fa-solid fa-chart-bar' => 'Grafik bar',
                'fa-solid fa-chart-line' => 'Grafik garis',
            ],
            'Status & Informasi' => [
                'fa-solid fa-circle-info' => 'Informasi',
                'fa-solid fa-circle-question' => 'Pertanyaan',
                'fa-solid fa-circle-exclamation' => 'Peringatan',
                'fa-solid fa-star' => 'Penting / bintang',
                'fa-solid fa-tag' => 'Label / tag',
                'fa-solid fa-tags' => 'Label (banyak)',
                'fa-solid fa-bookmark' => 'Bookmark',
                'fa-solid fa-bell' => 'Notifikasi',
                'fa-solid fa-lock' => 'Privat / kunci',
                'fa-solid fa-eye' => 'Lihat / preview',
                'fa-solid fa-bullhorn' => 'Pengeras Suara / Toa',
                'fa-solid fa-certificate' => 'Sertifikat / Bintang'
            ],
        ];
    }

}
