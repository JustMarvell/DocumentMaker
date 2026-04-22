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
                    <th class="px-4 py-3">Tipe File</th>
                    <th class="px-4 py-3">Akses</th>
                    <th class="px-4 py-3">Autofill</th>
                    <th class="px-4 py-3 text-center">Fields</th>
                    <th class="px-4 py-3 text-center">Dibuat</th>
                    <th class="px-4 py-3 text-center">Status</th>
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
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ ucfirst($type->staff_autofill_role) }}</td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('admin.document-types.fields', $type) }}"
                                class="text-blue-600 hover:underline text-xs font-medium">
                                {{ $type->fields()->count() }} field(s)
                            </a>
                        </td>
                        <td class="px-4 py-3 text-center">{{ $type->document_logs_count }}</td>
                        <td class="px-4 py-3 text-center">
                            @if ($type->is_active)
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-semibold">Aktif</span>
                            @else
                                <span class="bg-red-100 text-red-600 px-2 py-1 rounded text-xs font-semibold">Nonaktif</span>
                            @endif
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
                        <td colspan="9" class="px-4 py-6 text-center text-gray-400">Belum ada jenis dokumen.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection