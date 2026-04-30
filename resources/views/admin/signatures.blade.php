@extends('admin.layout')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Antrian Tanda Tangan Digital</h1>
            <p class="text-sm text-gray-500 mt-1">
                Kelola permintaan tanda tangan yang masuk dari pengguna.
                @if ($pendingCount > 0)
                    <span class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded text-xs font-semibold ml-1">
                        {{ $pendingCount }} menunggu
                    </span>
                @endif
            </p>
        </div>
    </div>

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('admin.signatures') }}"
        class="bg-white rounded-lg shadow p-4 mb-6 flex gap-4 items-end">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Status</label>
            <select name="status" class="border rounded px-3 py-2 text-sm">
                <option value="">Semua</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">Filter</button>
        <a href="{{ route('admin.signatures') }}" class="text-sm text-gray-500 hover:underline">Reset</a>
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-left">
                <tr>
                    <th class="px-4 py-3">Pemohon</th>
                    <th class="px-4 py-3">Jenis Dokumen</th>
                    <th class="px-4 py-3">Pejabat Penandatangan</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3">Diminta</th>
                    <th class="px-4 py-3">Ditinjau</th>
                    <th class="px-4 py-3 text-center">Aksi Admin</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($requests as $req)
                    <tr class="{{ $req->status === 'pending' ? 'bg-yellow-50/40' : '' }}">
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-800">{{ $req->user?->name ?? 'Guest' }}</p>
                            @if ($req->user?->nip)
                                <p class="text-xs text-gray-400">{{ $req->user->nip }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-gray-700">{{ $req->documentLog->documentType->name }}</p>
                            <p class="text-xs text-gray-400 font-mono">{{ $req->documentLog->output_filename }}</p>
                        </td>
                        <td class="px-4 py-3">
                            @if ($req->official)
                                <p class="font-medium text-gray-700">{{ $req->official->staff_name }}</p>
                                <p class="text-xs text-gray-400">{{ $req->official->position ?? '—' }}</p>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if ($req->status === 'pending')
                                <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs font-semibold">Menunggu</span>
                            @elseif ($req->status === 'approved')
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-semibold">Disetujui</span>
                            @else
                                <span class="bg-red-100 text-red-600 px-2 py-1 rounded text-xs font-semibold">Ditolak</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">
                            {{ $req->requested_at?->format('d/m/Y H:i') ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">
                            {{ $req->reviewed_at?->format('d/m/Y H:i') ?? '—' }}
                            @if ($req->notes)
                                <p class="text-gray-400 mt-0.5 italic" title="{{ $req->notes }}">
                                    "{{ Str::limit($req->notes, 40) }}"
                                </p>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if ($req->isPending())
                                {{-- Approve form --}}
                                <div class="flex flex-col gap-1 items-stretch min-w-[120px]">
                                    <button type="button"
                                        onclick="openActionModal({{ $req->id }}, 'approve', '{{ addslashes($req->documentLog->documentType->name) }}', '{{ addslashes($req->user?->name ?? 'Guest') }}')"
                                        class="text-xs px-3 py-1 rounded border border-green-400 text-green-700 hover:bg-green-50 font-medium">
                                        ✓ Setujui
                                    </button>
                                    <button type="button"
                                        onclick="openActionModal({{ $req->id }}, 'reject', '{{ addslashes($req->documentLog->documentType->name) }}', '{{ addslashes($req->user?->name ?? 'Guest') }}')"
                                        class="text-xs px-3 py-1 rounded border border-red-400 text-red-500 hover:bg-red-50 font-medium">
                                        ✕ Tolak
                                    </button>
                                </div>
                            @else
                                <span class="text-xs text-gray-400">Selesai</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                            Tidak ada permintaan tanda tangan
                            @if (request('status')) dengan status "{{ request('status') }}" @endif.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $requests->links() }}</div>


    {{-- ── Admin Action Modal ──────────────────────────────────────────── --}}
    <div id="action-modal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">

            <div class="flex items-center justify-between mb-4">
                <h2 id="modal-title" class="text-lg font-semibold text-gray-800"></h2>
                <button onclick="closeActionModal()" class="text-gray-400 hover:text-gray-600 text-xl font-bold">✕</button>
            </div>

            <p id="modal-desc" class="text-sm text-gray-500 mb-4"></p>

            <form id="action-form" method="POST" action="">
                @csrf
                @method('PATCH')

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Catatan <span class="text-gray-400 font-normal">(opsional)</span>
                    </label>
                    <textarea name="notes" rows="3"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Tambahkan catatan untuk pemohon..."></textarea>
                </div>

                <div class="flex gap-3 justify-end">
                    <button type="button" onclick="closeActionModal()"
                        class="px-4 py-2 rounded-lg border text-sm text-gray-600 hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" id="modal-submit-btn" class="px-5 py-2 rounded-lg text-sm font-medium text-white">
                        Konfirmasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const approveBaseUrl = '{{ url('/admin/signatures') }}';

        function openActionModal(id, action, docName, userName) {
            const modal = document.getElementById('action-modal');
            const title = document.getElementById('modal-title');
            const desc = document.getElementById('modal-desc');
            const form = document.getElementById('action-form');
            const submitBtn = document.getElementById('modal-submit-btn');

            if (action === 'approve') {
                title.textContent = 'Setujui Permintaan';
                desc.textContent = `Anda akan menyetujui permintaan tanda tangan untuk dokumen "${docName}" dari ${userName}. Pemohon akan menerima notifikasi email.`;
                form.action = `${approveBaseUrl}/${id}/approve`;
                submitBtn.textContent = 'Setujui';
                submitBtn.className = submitBtn.className.replace(/bg-\w+-\d+/g, '');
                submitBtn.classList.add('bg-green-600', 'hover:bg-green-700');
            } else {
                title.textContent = 'Tolak Permintaan';
                desc.textContent = `Anda akan menolak permintaan tanda tangan untuk dokumen "${docName}" dari ${userName}.`;
                form.action = `${approveBaseUrl}/${id}/reject`;
                submitBtn.textContent = 'Tolak';
                submitBtn.className = submitBtn.className.replace(/bg-\w+-\d+/g, '');
                submitBtn.classList.add('bg-red-600', 'hover:bg-red-700');
            }

            // Reset notes
            form.querySelector('textarea[name="notes"]').value = '';
            modal.classList.remove('hidden');
        }

        function closeActionModal() {
            document.getElementById('action-modal').classList.add('hidden');
        }

        document.getElementById('action-modal').addEventListener('click', function (e) {
            if (e.target === this) closeActionModal();
        });
    </script>

@endsection