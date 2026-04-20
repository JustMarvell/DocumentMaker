@extends('admin.layout')

@section('content')

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.document-types') }}" class="text-gray-400 hover:text-gray-600 text-sm">← Jenis Dokumen</a>
        <span class="text-gray-300">/</span>
        <a href="{{ route('admin.document-types.fields', $documentType) }}" class="text-gray-400 hover:text-gray-600 text-sm">
            {{ $documentType->name }}
        </a>
        <span class="text-gray-300">/</span>
        <span class="text-gray-600 text-sm">Re-upload Template</span>
    </div>

    <h1 class="text-2xl font-bold text-gray-800 mb-2">Re-upload Template</h1>
    <p class="text-sm text-gray-500 mb-6">
        Upload file template baru untuk menggantikan template yang ada.
        Semua field yang sudah didefinisikan akan tetap tersimpan.
    </p>

    {{-- Current template info --}}
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 max-w-2xl">
        <p class="text-sm font-medium text-blue-700 mb-2">Template saat ini</p>
        <div class="grid grid-cols-2 gap-3 text-sm">
            <div>
                <span class="text-gray-500">Nama:</span>
                <span class="font-medium text-gray-800 ml-1">{{ $documentType->name }}</span>
            </div>
            <div>
                <span class="text-gray-500">File:</span>
                <code class="bg-white px-2 py-0.5 rounded border text-xs ml-1">{{ $documentType->template_filename }}</code>
            </div>
            <div>
                <span class="text-gray-500">Tipe:</span>
                <span class="ml-1 px-2 py-0.5 rounded text-xs font-semibold
                    {{ $documentType->file_type === 'docx' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                    {{ strtoupper($documentType->file_type) }}
                </span>
            </div>
            <div>
                <span class="text-gray-500">Jumlah field:</span>
                <span class="font-medium text-gray-800 ml-1">{{ $documentType->fields()->count() }} field</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
        <form method="POST"
              action="{{ route('admin.document-types.reupload.store', $documentType) }}"
              enctype="multipart/form-data">
            @csrf

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    File Template Baru <span class="text-red-500">*</span>
                </label>
                <input type="file" name="template_file" accept=".docx,.xlsx"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" />
                <p class="text-xs text-gray-400 mt-1">
                    Upload file .docx atau .xlsx. File lama akan dihapus dan digantikan.
                    Pastikan placeholder di template baru masih sesuai dengan field yang sudah didefinisikan.
                    Maksimal 10MB.
                </p>
                @error('template_file')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Warning --}}
            <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-3 mb-5">
                <p class="text-xs text-yellow-800">
                    <strong>Perhatian:</strong> Pastikan placeholder di template baru
                    (<code class="bg-yellow-100 px-1 rounded">@verbatim{{ field_key }}@endverbatim</code>)
                    masih cocok dengan field key yang sudah terdaftar.
                    Jika ada field key yang berubah, perbarui juga field di halaman
                    <a href="{{ route('admin.document-types.fields', $documentType) }}"
                       class="underline font-medium">Kelola Field</a>.
                </p>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                    class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-blue-700 font-medium">
                    Upload Template Baru
                </button>
                <a href="{{ route('admin.document-types.fields', $documentType) }}"
                   class="px-4 py-2 rounded-lg border text-sm text-gray-600 hover:bg-gray-50">
                    Batal
                </a>
            </div>

        </form>
    </div>

@endsection