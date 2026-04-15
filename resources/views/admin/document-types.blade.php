@extends('admin.layout')

@section('content')

    <h1 class="text-2xl font-bold text-gray-800 mb-6">Jenis Dokumen</h1>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-left">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Key</th>
                    <th class="px-4 py-3">Script</th>
                    <th class="px-4 py-3">Akses</th>
                    <th class="px-4 py-3 text-center">Total Dibuat</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($documentTypes as $type)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $type->name }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-gray-400">{{ $type->key }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-gray-400">{{ $type->script_name }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="px-2 py-1 rounded text-xs font-semibold
                                            {{ $type->access_level === 'staff' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($type->access_level) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">{{ $type->document_logs_count }}</td>
                            <td class="px-4 py-3 text-center">
                                @if ($type->is_active)
                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-semibold">Aktif</span>
                                @else
                                    <span class="bg-red-100 text-red-600 px-2 py-1 rounded text-xs font-semibold">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <form method="POST" action="{{ route('admin.document-types.toggle', $type) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs px-3 py-1 rounded border
                                                        {{ $type->is_active
                    ? 'border-red-400 text-red-500 hover:bg-red-50'
                    : 'border-green-500 text-green-600 hover:bg-green-50' }}">
                                        {{ $type->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-gray-400">Belum ada jenis dokumen.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection