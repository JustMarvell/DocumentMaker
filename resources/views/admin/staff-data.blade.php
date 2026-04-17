@extends('admin.layout')

@section('content')

    <h1 class="text-2xl font-bold text-gray-800 mb-6">Manajemen Data Staff</h1>

    {{-- ============================================================ --}}
    {{-- Add Staff Form --}}
    {{-- ============================================================ --}}
    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Tambah Staff</h3>
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <form method="POST" action="{{ route('admin.staff-data.store') }}">
            @csrf
            <div class="grid grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pegawai <span
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
                        placeholder="Bidang Bina Marga..." value="{{ old('work_unit') }}" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan / Gol. Pangkat</label>
                    <input type="text" name="rank"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Pembina Tkd. I..." value="{{ old('rank') }}" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Posisi</label>
                    <input type="text" name="position"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Staff..." value="{{ old('position') }}" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                    <input type="text" name="phone_number"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="082256472819..." value="{{ old('phone_number') }}" />
                </div>

                <div class="flex items-end">
                    <button type="submit"
                        class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-blue-700 font-medium">
                        + Tambah Staff
                    </button>
                </div>

            </div>
        </form>
    </div>

    {{-- ============================================================ --}}
    {{-- Search & Filter Bar --}}
    {{-- ============================================================ --}}
    <div class="mb-8 bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('admin.staff-data') }}" class="flex flex-wrap gap-4 items-end">

            <!-- Search Bar -->
            <div class="flex-1 min-w-[240px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama, NIP, email, unit kerja..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Filter: Unit Kerja -->
            <div class="min-w-[180px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Unit Kerja</label>
                <select name="work_unit"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Unit Kerja</option>
                    @foreach ($workUnits ?? [] as $unit)
                        <option value="{{ $unit }}" {{ request('work_unit') == $unit ? 'selected' : '' }}>
                            {{ $unit }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter: Jabatan / Gol. Pangkat -->
            <div class="min-w-[180px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan / Gol. Pangkat</label>
                <select name="rank"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Jabatan / Pangkat</option>
                    @foreach ($ranks ?? [] as $rank)
                        <option value="{{ $rank }}" {{ request('rank') == $rank ? 'selected' : '' }}>
                            {{ $rank }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter: Posisi -->
            <div class="min-w-[180px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Posisi</label>
                <select name="position"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Posisi</option>
                    @foreach ($positions ?? [] as $position)
                        <option value="{{ $position }}" {{ request('position') == $position ? 'selected' : '' }}>
                            {{ $position }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-end gap-3">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg text-sm font-medium">
                    Cari
                </button>

                @if (request()->filled('search') || request()->filled('work_unit') || request()->filled('rank') || request()->filled('position'))
                    <a href="{{ route('admin.staff-data') }}"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg text-sm">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ============================================================ --}}
    {{-- Staff Table --}}
    {{-- ============================================================ --}}
    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Daftar Staff</h3>
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
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($staffList as $index => $staff)
                    <tr>
                        <td class="px-3 py-3 text-center text-gray-400">{{ $staffList->firstItem() + $index }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $staff->staff_name }}</td>
                        <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $staff->nip }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $staff->email }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $staff->work_unit }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $staff->rank ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $staff->position ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $staff->phone_number ?? '—' }}</td>
                        <td class="px-4 py-3 text-center flex gap-2 justify-center">

                            {{-- Edit button — opens modal --}}
                            <button type="button"
                                onclick="openEditModal({{ $staff->id }}, '{{ addslashes($staff->staff_name) }}', '{{ $staff->nip }}', '{{ $staff->email }}', '{{ addslashes($staff->work_unit) }}', '{{ addslashes($staff->rank) }}', '{{ addslashes($staff->position) }}', '{{ $staff->phone_number }}')"
                                class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700">
                                Edit
                            </button>

                            {{-- Delete button --}}
                            <form method="POST" action="{{ route('admin.staff-data.destroy', $staff) }}"
                                onsubmit="return confirm('Hapus data {{ addslashes($staff->staff_name) }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded text-xs hover:bg-red-700">
                                    Hapus
                                </button>
                            </form>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-gray-400">
                            Belum ada data staff. Tambahkan menggunakan form di atas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $staffList->links() }}
    </div>

    {{-- ============================================================ --}}
    {{-- Edit Modal --}}
    {{-- ============================================================ --}}
    <div id="edit-modal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-2xl">

            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Edit Data Staff</h2>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 text-xl font-bold">✕</button>
            </div>

            <form method="POST" id="edit-form" action="">
                @csrf
                @method('PATCH')
                <div class="grid grid-cols-2 gap-4">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pegawai <span
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

                </div>

                <div class="flex gap-3 mt-6 justify-end">
                    <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 rounded-lg border text-sm text-gray-600 hover:bg-gray-50">
                        Batal
                    </button>
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
            document.getElementById('edit-form').action = `/admin/staff-data/${id}`;
            document.getElementById('edit-staff-name').value = name;
            document.getElementById('edit-nip').value = nip;
            document.getElementById('edit-email').value = email;
            document.getElementById('edit-work-unit').value = workUnit;
            document.getElementById('edit-rank').value = rank !== 'null' ? rank : '';
            document.getElementById('edit-position').value = position !== 'null' ? position : '';
            document.getElementById('edit-phone-number').value = phone !== 'null' ? phone : '';
            document.getElementById('edit-modal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('edit-modal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('edit-modal').addEventListener('click', function (e) {
            if (e.target === this) closeEditModal();
        });
    </script>

@endsection