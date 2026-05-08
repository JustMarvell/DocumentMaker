@extends('admin.layout')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Jenis Dokumen</h1>
        <a href="{{ route('admin.document-types.create') }}"
            class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 font-medium">
            + Tambah Template Baru
        </a>
    </div>

    {{-- Toast --}}
    <div id="dt-toast"
         style="display:none;position:fixed;bottom:1.5rem;left:50%;transform:translateX(-50%);z-index:9999;
                padding:0.55rem 1.1rem;border-radius:8px;font-size:0.82rem;font-weight:500;color:#fff;
                box-shadow:0 4px 16px rgba(0,0,0,0.22);transition:opacity 0.3s ease;white-space:nowrap;">
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
                    <th class="px-4 py-3 text-center">Gambar TTD</th>
                    <th class="px-4 py-3 text-center">QR Code</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100" id="dt-tbody">
                @forelse ($documentTypes as $type)
                    <tr id="dt-row-{{ $type->id }}">
                        <td class="px-4 py-3 font-medium">{{ $type->name }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-400">{{ $type->key }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded text-xs font-semibold
                                {{ $type->file_type === 'docx' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                                {{ strtoupper($type->file_type) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded text-xs font-semibold
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

                        {{-- Status badge --}}
                        <td class="px-4 py-3 text-center">
                            <span id="badge-active-{{ $type->id }}"
                                class="{{ $type->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }} px-2 py-1 rounded text-xs font-semibold">
                                {{ $type->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>

                        {{-- Preview toggle --}}
                        <td class="px-4 py-3 text-center">
                            <button type="button"
                                id="toggle-preview-{{ $type->id }}"
                                onclick="ajaxToggle('{{ route('admin.document-types.toggle-preview', $type) }}', {{ $type->id }}, 'preview')"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none
                                    {{ $type->preview_enabled ? 'bg-blue-600' : 'bg-gray-300' }}"
                                title="{{ $type->preview_enabled ? 'Preview aktif' : 'Preview nonaktif' }}">
                                <span id="thumb-preview-{{ $type->id }}"
                                    class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform
                                        {{ $type->preview_enabled ? 'translate-x-6' : 'translate-x-1' }}">
                                </span>
                            </button>
                        </td>

                        {{-- Signature toggle --}}
                        <td class="px-4 py-3 text-center">
                            <button type="button"
                                id="toggle-signature-{{ $type->id }}"
                                onclick="ajaxToggle('{{ route('admin.document-types.toggle-signature', $type) }}', {{ $type->id }}, 'signature')"
                                class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none
                                    {{ $type->signature_enabled ? 'bg-purple-600' : 'bg-gray-300' }}"
                                title="TTD Digital">
                                <span id="thumb-signature-{{ $type->id }}"
                                    class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform
                                        {{ $type->signature_enabled ? 'translate-x-6' : 'translate-x-1' }}">
                                </span>
                            </button>
                        </td>

                        {{-- Signature image toggle --}}
                        <td class="px-4 py-3 text-center" id="cell-sig-image-{{ $type->id }}"
                            data-route="{{ route('admin.document-types.toggle-signature-image', $type) }}"
                            data-on="{{ $type->signature_use_image ? '1' : '0' }}">
                            @if ($type->signature_enabled)
                                <button type="button"
                                    id="toggle-sig-image-{{ $type->id }}"
                                    onclick="ajaxToggle('{{ route('admin.document-types.toggle-signature-image', $type) }}', {{ $type->id }}, 'sig-image')"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors
                                        {{ $type->signature_use_image ? 'bg-purple-600' : 'bg-gray-300' }}"
                                    title="Embed gambar tanda tangan">
                                    <span id="thumb-sig-image-{{ $type->id }}"
                                        class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform
                                            {{ $type->signature_use_image ? 'translate-x-6' : 'translate-x-1' }}">
                                    </span>
                                </button>
                            @else
                                <span class="text-xs text-gray-300" id="placeholder-sig-image-{{ $type->id }}">—</span>
                            @endif
                        </td>

                        {{-- QR code toggle --}}
                        <td class="px-4 py-3 text-center" id="cell-sig-qr-{{ $type->id }}"
                            data-route="{{ route('admin.document-types.toggle-signature-qr', $type) }}"
                            data-on="{{ $type->signature_use_qr ? '1' : '0' }}">
                            @if ($type->signature_enabled)
                                <button type="button"
                                    id="toggle-sig-qr-{{ $type->id }}"
                                    onclick="ajaxToggle('{{ route('admin.document-types.toggle-signature-qr', $type) }}', {{ $type->id }}, 'sig-qr')"
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors
                                        {{ $type->signature_use_qr ? 'bg-indigo-600' : 'bg-gray-300' }}"
                                    title="Embed QR code verifikasi">
                                    <span id="thumb-sig-qr-{{ $type->id }}"
                                        class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform
                                            {{ $type->signature_use_qr ? 'translate-x-6' : 'translate-x-1' }}">
                                    </span>
                                </button>
                            @else
                                <span class="text-xs text-gray-300" id="placeholder-sig-qr-{{ $type->id }}">—</span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-4 py-3">
                            <div class="flex flex-col gap-1 items-stretch min-w-[100px]">
                                <a href="{{ route('admin.document-types.fields', $type) }}"
                                    class="text-xs px-2 py-1 rounded border border-blue-400 text-blue-600 hover:bg-blue-50 text-center">
                                    Kelola Field
                                </a>

                                {{-- Toggle active --}}
                                <button type="button"
                                    id="btn-active-{{ $type->id }}"
                                    data-active="{{ $type->is_active ? '1' : '0' }}"
                                    onclick="ajaxToggle('{{ route('admin.document-types.toggle', $type) }}', {{ $type->id }}, 'active')"
                                    class="text-xs px-2 py-1 rounded border
                                        {{ $type->is_active ? 'border-yellow-400 text-yellow-600 hover:bg-yellow-50' : 'border-green-500 text-green-600 hover:bg-green-50' }}">
                                    {{ $type->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>

                                {{-- Delete --}}
                                <button type="button"
                                    onclick="deleteDocType({{ $type->id }}, '{{ addslashes($type->name) }}')"
                                    class="text-xs px-2 py-1 rounded border border-red-400 text-red-500 hover:bg-red-50">
                                    Hapus
                                </button>

                                {{-- Hidden delete form for CSRF --}}
                                <form id="delete-form-{{ $type->id }}"
                                    method="POST"
                                    action="{{ route('admin.document-types.destroy', $type) }}"
                                    style="display:none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" class="px-4 py-6 text-center text-gray-400">Belum ada jenis dokumen.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4"> {{ $documentTypes->links() }} </div>

    <script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    function showToast(msg, ok = true) {
        const t = document.getElementById('dt-toast');
        t.textContent = msg;
        t.style.background = ok ? '#1e3058' : '#b91c1c';
        t.style.display = 'block';
        t.style.opacity = '1';
        clearTimeout(t._timer);
        t._timer = setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.style.display = 'none', 300); }, 2500);
    }

    async function ajaxToggle(url, id, type) {
        try {
            const fd = new FormData();
            fd.append('_token', CSRF);
            fd.append('_method', 'PATCH');
            const res = await fetch(url, {
                method: 'POST',
                body: fd,
            });
            if (!res.ok) throw new Error('Server error');
            applyToggleUI(id, type);
        } catch (e) {
            showToast('Gagal memperbarui. Coba lagi.', false);
        }
    }

    function applyToggleUI(id, type) {
        if (type === 'active') {
            const btn    = document.getElementById('btn-active-' + id);
            const badge  = document.getElementById('badge-active-' + id);
            const isNowActive = btn.dataset.active === '0';   // was inactive, now active
            btn.dataset.active = isNowActive ? '1' : '0';

            if (isNowActive) {
                btn.className = 'text-xs px-2 py-1 rounded border border-yellow-400 text-yellow-600 hover:bg-yellow-50';
                btn.textContent = 'Nonaktifkan';
                badge.className = 'bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-semibold';
                badge.textContent = 'Aktif';
            } else {
                btn.className = 'text-xs px-2 py-1 rounded border border-green-500 text-green-600 hover:bg-green-50';
                btn.textContent = 'Aktifkan';
                badge.className = 'bg-red-100 text-red-600 px-2 py-1 rounded text-xs font-semibold';
                badge.textContent = 'Nonaktif';
            }
            showToast('Status berhasil diubah.');
            return;
        }

        const colorMap = {
            'preview':   'bg-blue-600',
            'signature': 'bg-purple-600',
            'sig-image': 'bg-purple-600',
            'sig-qr':    'bg-indigo-600',
        };
        const btn   = document.getElementById('toggle-' + type + '-' + id);
        const thumb = document.getElementById('thumb-' + type + '-' + id);
        if (!btn || !thumb) return;

        const isOn = btn.classList.contains(colorMap[type]);
        if (isOn) {
            btn.classList.remove(colorMap[type]);
            btn.classList.add('bg-gray-300');
            thumb.classList.remove('translate-x-6');
            thumb.classList.add('translate-x-1');
        } else {
            btn.classList.remove('bg-gray-300');
            btn.classList.add(colorMap[type]);
            thumb.classList.remove('translate-x-1');
            thumb.classList.add('translate-x-6');
        }

        // Show/hide dependent toggles when signature master is flipped
        if (type === 'signature') {
            const nowOn = !isOn;
            ['sig-image', 'sig-qr'].forEach(function(sub) {
                const cell  = document.getElementById('cell-' + sub.replace('-', '-sig-').replace('sig-sig-', 'sig-') + '-' + id);
                // simpler: just update the two specific cells
            });
            updateSignatureDependents(id, nowOn);
        }

        showToast('Pengaturan berhasil diperbarui.');
    }

    function updateSignatureDependents(id, sigEnabled) {
        ['sig-image', 'sig-qr'].forEach(function(sub) {
            const cell = document.getElementById('cell-' + sub + '-' + id);
            if (!cell) return;

            if (!sigEnabled) {
                cell.innerHTML = '<span class="text-xs text-gray-300">—</span>';
                return;
            }

            // Read state & route stored on the cell element
            const route      = cell.dataset.route;
            const isOn       = cell.dataset.on === '1';
            const colorClass = sub === 'sig-qr' ? 'bg-indigo-600' : 'bg-purple-600';
            const onClass    = isOn ? colorClass : 'bg-gray-300';
            const thumbPos   = isOn ? 'translate-x-6' : 'translate-x-1';

            cell.innerHTML =
                '<button type="button" id="toggle-' + sub + '-' + id + '"' +
                ' onclick="ajaxToggle(\'' + route + '\', ' + id + ', \'' + sub + '\')"' +
                ' class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors ' + onClass + '">' +
                '<span id="thumb-' + sub + '-' + id + '"' +
                ' class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform ' + thumbPos + '">' +
                '</span></button>';
        });
    }

    function deleteDocType(id, name) {
        if (!confirm('Hapus template "' + name + '"? Semua field dan file template akan ikut terhapus.')) return;
        const form = document.getElementById('delete-form-' + id);
        const data = new FormData(form);
        fetch(form.action, { method: 'POST', body: data })
            .then(function(res) {
                if (!res.ok) throw new Error();
                const row = document.getElementById('dt-row-' + id);
                if (row) {
                    row.style.transition = 'opacity 0.3s';
                    row.style.opacity = '0';
                    setTimeout(() => row.remove(), 310);
                }
                showToast('"' + name + '" berhasil dihapus.');
            })
            .catch(function() { showToast('Gagal menghapus. Coba lagi.', false); });
    }
    </script>

@endsection