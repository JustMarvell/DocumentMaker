@extends('admin.layout')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Jenis Dokumen</h1>
        <a href="{{ route('admin.document-types.create') }}"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 font-medium">
            + Tambah Template Baru
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-left">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Key</th>
                    <th class="px-4 py-3">Tipe</th>
                    <th class="px-4 py-3">Akses</th>
                    <th class="px-4 py-3 text-center">Fields</th>
                    <th class="px-4 py-3 text-center">Dibuat</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Preview</th>
                    <th class="px-4 py-3 text-center">TTD Digital</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($documentTypes as $type)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $type->name }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-400">{{ $type->key }}</td>
                        <td class="px-4 py-3">
                            <span
                                class="px-2 py-1 rounded text-xs font-semibold
                                        {{ $type->file_type === 'docx' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                                {{ strtoupper($type->file_type) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span
                                class="px-2 py-1 rounded text-xs font-semibold
                                        {{ $type->access_level === 'staff' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($type->access_level) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.document-types.fields', $type) }}"
                                class="text-blue-600 hover:underline text-xs font-medium">
                                {{ $type->fields()->count() }} field(s)
                            </a>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-500">{{ $type->document_logs_count }}</td>
                        <td class="px-4 py-3 text-center">
                            @if ($type->is_active)
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-semibold">Aktif</span>
                            @else
                                <span class="bg-red-100 text-red-600 px-2 py-1 rounded text-xs font-semibold">Nonaktif</span>
                            @endif
                        </td>

                        {{-- Preview toggle --}}
                        <td class="px-4 py-3 text-center">
                            <form method="POST" action="{{ route('admin.document-types.toggle-preview', $type) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none
                                                {{ $type->preview_enabled ? 'bg-blue-600' : 'bg-gray-300' }}"
                                    title="{{ $type->preview_enabled ? 'Preview aktif' : 'Preview nonaktif' }}">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform
                                                {{ $type->preview_enabled ? 'translate-x-6' : 'translate-x-1' }}">
                                    </span>
                                </button>
                            </form>
                        </td>

                        {{-- ── NEW: Signature toggle ── --}}
                        <td class="px-4 py-3 text-center">
                            <form method="POST" action="{{ route('admin.document-types.toggle-signature', $type) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none
                                                {{ $type->signature_enabled ? 'bg-purple-600' : 'bg-gray-300' }}"
                                    title="{{ $type->signature_enabled ? 'TTD Digital aktif — klik untuk menonaktifkan' : 'TTD Digital nonaktif — klik untuk mengaktifkan' }}">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform
                                                {{ $type->signature_enabled ? 'translate-x-6' : 'translate-x-1' }}">
                                    </span>
                                </button>
                            </form>
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex flex-col gap-1 items-stretch min-w-[100px]">
                                <a href="{{ route('admin.document-types.fields', $type) }}"
                                    class="text-xs px-2 py-1 rounded border border-blue-400 text-blue-600 hover:bg-blue-50 text-center">
                                    Kelola Field
                                </a>
                                <form method="POST" action="{{ route('admin.document-types.toggle', $type) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="w-full text-xs px-2 py-1 rounded border
                                                    {{ $type->is_active ? 'border-yellow-400 text-yellow-600 hover:bg-yellow-50' : 'border-green-500 text-green-600 hover:bg-green-50' }}">
                                        {{ $type->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.document-types.destroy', $type) }}"
                                    onsubmit="return confirm('Hapus template {{ addslashes($type->name) }}? Semua field dan file template akan ikut terhapus.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full text-xs px-2 py-1 rounded border border-red-400 text-red-500 hover:bg-red-50">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-4 py-6 text-center text-gray-400">Belum ada jenis dokumen.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection