@extends('admin.layout')

@section('content')

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.document-types') }}" class="text-gray-400 hover:text-gray-600 text-sm">← Kembali</a>
        <h1 class="text-2xl font-bold text-gray-800">Tambah Template Baru</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
        <form method="POST" action="{{ route('admin.document-types.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 gap-5">

                {{-- Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Nama Dokumen <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Surat Keterangan Aktif..." />
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Key --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Key (unik, huruf kecil dan tanda hubung saja) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="key" value="{{ old('key') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="surat-keterangan-aktif..." />
                    <p class="text-xs text-gray-400 mt-1">Hanya huruf kecil, angka, dan tanda hubung. Contoh:
                        surat-tugas-harian</p>
                    @error('key') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- File type + Access level --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tipe File <span class="text-red-500">*</span>
                        </label>
                        <select name="file_type"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="docx" {{ old('file_type') === 'docx' ? 'selected' : '' }}>Word (.docx)</option>
                            <option value="xlsx" {{ old('file_type') === 'xlsx' ? 'selected' : '' }}>Excel (.xlsx)</option>
                        </select>
                        @error('file_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Level Akses <span class="text-red-500">*</span>
                        </label>
                        <select name="access_level"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="guest" {{ old('access_level') === 'guest' ? 'selected' : '' }}>Guest (publik)
                            </option>
                            <option value="staff" {{ old('access_level') === 'staff' ? 'selected' : '' }}>Staff only</option>
                        </select>
                        @error('access_level') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Staff autofill role --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Staff Autofill
                    </label>
                    <select name="staff_autofill_role"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="none" {{ old('staff_autofill_role') === 'none' ? 'selected' : '' }}>Tidak ada autofill
                        </option>
                        <option value="employee" {{ old('staff_autofill_role') === 'employee' ? 'selected' : '' }}>Pegawai
                            saja</option>
                        <option value="appraiser" {{ old('staff_autofill_role') === 'appraiser' ? 'selected' : '' }}>Penilai
                            saja</option>
                        <option value="both" {{ old('staff_autofill_role') === 'both' ? 'selected' : '' }}>Pegawai dan Penilai
                        </option>
                    </select>
                    <p class="text-xs text-gray-400 mt-1">
                        Menentukan apakah form akan menampilkan dropdown pilih staff untuk mengisi otomatis.
                    </p>
                    @error('staff_autofill_role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Template file upload --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        File Template <span class="text-red-500">*</span>
                    </label>
                    <input type="file" name="template_file" accept=".docx,.xlsx"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" />
                        <p class="text-xs text-gray-400 mt-1">
                            Upload file .docx atau .xlsx yang sudah berisi placeholder
                            <code class="bg-gray-100 px-1 rounded">@verbatim{{ variable_name }}@endverbatim</code>.
                            Maksimal 10MB.
                        </p>
                    @error('template_file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

            </div>

            <div class="flex gap-3 mt-6 pt-4 border-t">
                <button type="submit"
                    class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-blue-700 font-medium">
                    Simpan & Kelola Field →
                </button>
                <a href="{{ route('admin.document-types') }}"
                    class="px-4 py-2 rounded-lg border text-sm text-gray-600 hover:bg-gray-50">
                    Batal
                </a>
            </div>
        </form>
    </div>

@endsection