@extends('admin.layout')

@section('content')

    <h1 class="text-2xl font-bold text-gray-800 mb-6">Riwayat Dokumen</h1>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.logs') }}" class="bg-white rounded-lg shadow p-4 mb-6 flex gap-4 items-end">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Jenis Dokumen</label>
            <select name="type" class="border rounded px-3 py-2 text-sm">
                <option value="">Semua</option>
                @foreach ($documentTypes as $type)
                    <option value="{{ $type->key }}" {{ request('type') === $type->key ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Status</label>
            <select name="status" class="border rounded px-3 py-2 text-sm">
                <option value="">Semua</option>
                <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Berhasil</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Gagal</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Pengguna</label>
            <select name="user_id" class="border rounded px-3 py-2 text-sm">
                <option value="">Semua</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
            Filter
        </button>
        <a href="{{ route('admin.logs') }}" class="text-sm text-gray-500 hover:underline">Reset</a>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-left">
                <tr>
                    <th class="px-4 py-3">Pengguna</th>
                    <th class="px-4 py-3">Jenis Dokumen</th>
                    <th class="px-4 py-3">File</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Dibuat</th>
                    <th class="px-4 py-3">Diunduh</th>
                    <th class="px-4 py-3">Dihapus</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($logs as $log)
                    <tr>
                        <td class="px-4 py-3">
                            {{ $log->user?->name ?? 'Guest' }}
                            @if($log->user?->nip)
                                <span class="block text-xs text-gray-400">{{ $log->user->nip }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $log->documentType->name }}</td>
                        <td class="px-4 py-3 text-xs text-gray-400 font-mono">{{ $log->output_filename }}</td>
                        <td class="px-4 py-3">
                            @if ($log->status === 'success')
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-semibold">Berhasil</span>
                            @else
                                <span class="bg-red-100 text-red-600 px-2 py-1 rounded text-xs font-semibold">Gagal</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $log->generated_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $log->downloaded_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $log->deleted_at?->format('d/m/Y H:i') ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-gray-400">Belum ada riwayat dokumen.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $logs->links() }}
    </div>

@endsection