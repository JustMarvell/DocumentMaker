@extends('admin.layout')

@section('content')

    <h1 class="text-2xl font-bold text-gray-800 mb-6">Riwayat Dokumen</h1>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.logs') }}"
        class="bg-white rounded-lg shadow p-4 mb-6 flex gap-4 items-end flex-wrap">
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
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">Filter</button>
        <a href="{{ route('admin.logs') }}" class="text-sm text-gray-500 hover:underline">Reset</a>
    </form>

    <div id="log-toast" style="display:none;position:fixed;bottom:1.5rem;left:50%;transform:translateX(-50%);z-index:9999;padding:0.55rem 1.1rem;border-radius:8px;font-size:0.82rem;font-weight:500;color:#fff;box-shadow:0 4px 16px rgba(0,0,0,0.22);transition:opacity 0.3s ease;white-space:nowrap;"></div>

    {{-- Bulk delete form --}}
    <form method="POST" action="{{ route('admin.logs.bulk-delete') }}" id="bulk-form">
        @csrf
        @method('DELETE')

        {{-- Selection controls --}}
        <div class="flex items-center gap-2 mb-3">
            <button type="button" onclick="selectAll()"
                class="text-xs px-3 py-1.5 rounded border border-gray-300 hover:bg-gray-50 text-gray-600">Pilih
                Semua</button>
            <button type="button" onclick="deselectAll()"
                class="text-xs px-3 py-1.5 rounded border border-gray-300 hover:bg-gray-50 text-gray-600">Batalkan
                Semua</button>
            <button type="button" onclick="invertSelection()"
                class="text-xs px-3 py-1.5 rounded border border-gray-300 hover:bg-gray-50 text-gray-600">Balik
                Pilihan</button>
            <span id="selected-count" class="text-xs text-gray-400 ml-1">0 dipilih</span>
            <button type="button" id="delete-btn" onclick="confirmDelete()"
                class="ml-auto text-xs px-4 py-1.5 rounded border border-red-400 text-red-500 hover:bg-red-50 font-medium hidden">
                Hapus Terpilih
            </button>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-left">
                    <tr>
                        <th class="px-4 py-3 w-8"></th>
                        <th class="px-4 py-3">Pengguna</th>
                        <th class="px-4 py-3">Jenis Dokumen</th>
                        <th class="px-4 py-3">File</th>
                        <th class="px-4 py-3">Unduh</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Dibuat</th>
                        <th class="px-4 py-3">Diunduh</th>
                        <th class="px-4 py-3">Dihapus</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($logs as $log)
                        <tr class="log-row hover:bg-gray-50 transition-colors" id="row-{{ $log->id }}">
                            <td class="px-4 py-3">
                                <input type="checkbox" name="ids[]" value="{{ $log->id }}"
                                    class="log-checkbox w-4 h-4 accent-red-500 cursor-pointer" onchange="updateCount()" />
                            </td>
                            <td class="px-4 py-3">
                                {{ $log->user?->name ?? 'Nama Tidak ada / Dihapus' }}
                                @if($log->user?->nip)
                                    <span class="block text-xs text-gray-400">{{ $log->user->nip }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $log->documentType->name }}</td>
                            <td class="px-4 py-3 text-xs text-gray-400 font-mono">
                                {{ $log->output_filename }}
                                @if($log->signatureRequest?->signed_filename)
                                    <span class="block text-purple-500 mt-0.5">
                                        ✎ {{ $log->signatureRequest->signed_filename }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-col gap-1">
                                    {{-- Original file --}}
                                    @php $origExists = file_exists(storage_path('app/cached_result/' . $log->output_filename)); @endphp
                                    @if($origExists)
                                        <a href="{{ route('document.download', $log->output_filename) }}"
                                            class="text-xs px-2 py-1 rounded border border-blue-400 text-blue-600 hover:bg-blue-50 text-center whitespace-nowrap">
                                            ⬇ Dokumen
                                        </a>
                                    @else
                                        <button type="button" onclick="showLogToast('File dokumen sudah dihapus.')"
                                            class="text-xs px-2 py-1 rounded border border-gray-300 text-gray-400 cursor-pointer text-center whitespace-nowrap">
                                            ⬇ Dokumen
                                        </button>
                                    @endif

                                    {{-- Signed file --}}
                                    @php 
                                        $sigReq = $log->signatureRequest; 
                                    @endphp
                                    @if($log->signatureRequest?->signed_filename)
                                    @php $signedExists = $sigReq?->signed_filename && file_exists(storage_path('app/cached_result/' . $sigReq->signed_filename)); @endphp
                                        @if($signedExists)
                                            <a href="{{ route('document.download', $log->signatureRequest->signed_filename) }}"
                                                class="text-xs px-2 py-1 rounded border border-purple-400 text-purple-600 hover:bg-purple-50 text-center whitespace-nowrap">
                                                ✎ Signed
                                            </a>
                                        @else
                                            <button type="button" onclick="showLogToast('File signed sudah dihapus.')"
                                                class="text-xs px-2 py-1 rounded border border-gray-300 text-gray-400 cursor-pointer text-center whitespace-nowrap">
                                                ✎ Signed
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if ($log->status === 'success')
                                    <span
                                        class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-semibold">Berhasil</span>
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
                            <td colspan="8" class="px-4 py-6 text-center text-gray-400">Belum ada riwayat dokumen.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </form>

    {{-- Pagination --}}
    <div class="mt-4">{{ $logs->links() }}</div>

    <script>
        function getCheckboxes() {
            return document.querySelectorAll('.log-checkbox');
        }

        function updateCount() {
            const checked = document.querySelectorAll('.log-checkbox:checked').length;
            document.getElementById('selected-count').textContent = checked + ' dipilih';
            const btn = document.getElementById('delete-btn');
            btn.classList.toggle('hidden', checked === 0);

            // highlight selected rows
            getCheckboxes().forEach(function (cb) {
                cb.closest('tr').classList.toggle('bg-red-50', cb.checked);
            });
        }

        function selectAll() {
            getCheckboxes().forEach(cb => { cb.checked = true; });
            updateCount();
        }

        function deselectAll() {
            getCheckboxes().forEach(cb => { cb.checked = false; });
            updateCount();
        }

        function invertSelection() {
            getCheckboxes().forEach(cb => { cb.checked = !cb.checked; });
            updateCount();
        }

        function confirmDelete() {
            const count = document.querySelectorAll('.log-checkbox:checked').length;
            if (count === 0) return;
            if (!confirm(`Hapus ${count} dokumen terpilih? File fisik yang masih ada di server juga akan ikut dihapus.`)) return;
            document.getElementById('bulk-form').submit();
        }

        function showLogToast(msg) {
            const t = document.getElementById('log-toast');
            t.textContent = msg;
            t.style.background = '#b91c1c';
            t.style.display = 'block';
            t.style.opacity = '1';
            clearTimeout(t._timer);
            t._timer = setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.style.display = 'none', 300); }, 3000);
        }
    </script>

@endsection