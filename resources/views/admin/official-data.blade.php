@extends('admin.layout')

@section('content')

    <h1 class="text-2xl font-bold text-gray-800 mb-6">Manajemen Data Pejabat</h1>

    <!-- Add officials form -->
    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Tambah Pejabat</h3>
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <form method="POST" action="{{ route('admin.official-data.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pejabat <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="staff_name"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="John Doe..." value="{{ old('staff_name') }}" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NIP <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="nip"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="71030504050001..." value="{{ old('nip') }}" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email <span
                            class="text-red-500">*</span></label>
                    <input type="email" name="email"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="email@gmail.com..." value="{{ old('email') }}" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit Kerja <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="work_unit"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Dinas PUPRD..." value="{{ old('work_unit') }}" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan / Gol. Pangkat</label>
                    <input type="text" name="rank"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Pembina Utama Muda..." value="{{ old('rank') }}" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Posisi</label>
                    <input type="text" name="position"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Kepala Dinas..." value="{{ old('position') }}" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                    <input type="text" name="phone_number"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="082256472819..." value="{{ old('phone_number') }}" />
                </div>

                {{-- Signature image upload --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Gambar Tanda Tangan
                        <span class="text-gray-400 font-normal text-xs ml-1">(PNG/JPG, maks. 2MB, transparan lebih
                            baik)</span>
                    </label>
                    <input type="file" name="signature_image" accept="image/png,image/jpeg,image/jpg"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" />
                </div>

                <div class="flex items-end col-span-2">
                    <button type="submit"
                        class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-blue-700 font-medium">
                        + Tambah Pejabat
                    </button>
                </div>

            </div>
        </form>
    </div>

    <!-- Officials table -->
    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Daftar Pejabat</h3>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-left">
                <tr>
                    <th class="px-3 py-3 text-center">No</th>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">NIP</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Unit Kerja</th>
                    <th class="px-4 py-3">Jabatan / Gol. Pangkat</th>
                    <th class="px-4 py-3">Posisi</th>
                    <th class="px-4 py-3">No. HP</th>
                    <th class="px-4 py-3 text-center">TTD</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($officialList as $index => $official)
                    <tr>
                        <td class="px-3 py-3 text-center text-gray-400">{{ $officialList->firstItem() + $index }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $official->staff_name }}</td>
                        <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $official->nip }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $official->email }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $official->work_unit }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $official->rank ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $official->position ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $official->phone_number ?? '—' }}</td>

                        {{-- Signature image status --}}
                        <td class="px-4 py-3 text-center">
                            @if ($official->hasSignatureImage())
                                <div class="flex flex-col items-center gap-1">
                                    <img src="{{ Storage::url($official->signature_image) }}" alt="TTD"
                                        class="h-8 object-contain border border-gray-200 rounded px-1 bg-gray-50"
                                        title="Klik untuk hapus" />
                                    <form method="POST" action="{{ route('admin.official-data.delete-signature', $official) }}"
                                        onsubmit="return confirm('Hapus gambar tanda tangan {{ addslashes($official->staff_name) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-400 hover:text-red-600">hapus</button>
                                    </form>
                                </div>
                            @else
                                <span class="text-xs text-gray-300">Belum ada</span>
                            @endif
                        </td>

                        <td class="px-4 py-3 text-center">
                            <div class="flex gap-2 justify-center">
                                <button type="button"
                                    onclick="openEditModal({{ $official->id }}, '{{ addslashes($official->staff_name) }}', '{{ $official->nip }}', '{{ $official->email }}', '{{ addslashes($official->work_unit) }}', '{{ addslashes($official->rank) }}', '{{ addslashes($official->position) }}', '{{ $official->phone_number }}')"
                                    class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700">
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('admin.official-data.destroy', $official) }}"
                                    onsubmit="return confirm('Hapus data {{ addslashes($official->staff_name) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="bg-red-600 text-white px-3 py-1 rounded text-xs hover:bg-red-700">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-4 py-8 text-center text-gray-400">
                            Belum ada data pejabat. Tambahkan menggunakan form di atas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $officialList->links() }}
    </div>

    <!-- Edit modal -->
    <div id="edit-modal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-2xl max-h-screen overflow-y-auto">

            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Edit Data Pejabat</h2>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 text-xl font-bold">✕</button>
            </div>

            <form method="POST" id="edit-form" action="" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="grid grid-cols-2 gap-4">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pejabat <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="staff_name" id="edit-staff-name"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NIP <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="nip" id="edit-nip"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email <span
                                class="text-red-500">*</span></label>
                        <input type="email" name="email" id="edit-email"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Unit Kerja <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="work_unit" id="edit-work-unit"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan / Gol. Pangkat</label>
                        <input type="text" name="rank" id="edit-rank"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Posisi</label>
                        <input type="text" name="position" id="edit-position"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                        <input type="text" name="phone_number" id="edit-phone-number"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>

                    {{-- Signature image in edit modal --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Ganti Gambar Tanda Tangan
                            <span class="text-gray-400 font-normal text-xs ml-1">(kosongkan jika tidak ingin
                                mengganti)</span>
                        </label>
                        <input type="file" name="signature_image" accept="image/png,image/jpeg,image/jpg"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white" />
                    </div>

                </div>

                <div class="flex gap-3 mt-6 justify-end">
                    <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 rounded-lg border text-sm text-gray-600 hover:bg-gray-50">Batal</button>
                    <button type="submit"
                        class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-blue-700 font-medium">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, name, nip, email, workUnit, rank, position, phone) {
            document.getElementById('edit-form').action = `/admin/official-data/${id}`;
            document.getElementById('edit-staff-name').value = name;
            document.getElementById('edit-nip').value = nip;
            document.getElementById('edit-email').value = email;
            document.getElementById('edit-work-unit').value = workUnit;
            document.getElementById('edit-rank').value = rank !== 'null' ? rank : '';
            document.getElementById('edit-position').value = position !== 'null' ? position : '';
            document.getElementById('edit-phone-number').value = phone !== 'null' ? phone : '';
            // Reset file input
            document.querySelector('#edit-form input[type="file"]').value = '';
            document.getElementById('edit-modal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('edit-modal').classList.add('hidden');
        }

        document.getElementById('edit-modal').addEventListener('click', function (e) {
            if (e.target === this) closeEditModal();
        });
    </script>

@endsection