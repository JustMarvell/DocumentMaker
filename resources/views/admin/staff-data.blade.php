@extends('admin.layout')

@section('content')
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Managemen Staff</h1>

    <h3 class="text-l font-bold text-gray-600 mb-6">Tambah Staff</h3>
    <div class="bg-white rounded-lg shadow p-4 mb-6 grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pegawai</label>
            <input type="text" name="asd-employee_name" id="asd-employee-name"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="John Doe..." />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
            <input type="text" name="asd-nip" id="asd-nip"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="71030504050001..." />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="asd-email" id="asd-email"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="email@gmail.com..." />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Unit Kerja</label>
            <input type="text" name="asd-work-unit" id="asd-work-unit"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Bidang Bina Marga..." />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan / Gol. Pangkat</label>
            <input type="text" name="asd-rank" id="asd-rank"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Pembina Tkd. I..." />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Posisi</label>
            <input type="text" name="asd-position" id="asd-position"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="Staff..." />
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">No. Hp</label>
            <input type="text" name="asd-phone-number" id="asd-phone-number"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="082256472819..." />
        </div>
        <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi</label>
            <button type="post" class="bg-blue-600 text-white px-2 py-2 rounded text-xs hover:bg-blue-700">
                Tambah
            </button>
        </div>
    </div>

    <h3 class="text-l font-bold text-gray-600 mb-6">Daftar Staff</h3>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-center">
                <tr>
                    <th class="px-1 py-3">No</th>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">Nip</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Unit Kerja</th>
                    <th class="px-4 py-3">Jabatan / Gol.Pangkat</th>
                    <th class="px-4 py-3">Posisi</th>
                    <th class="px-4 py-3">No. Hp</th>
                    <th class="px-4 py-3">Edit Data</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <tr>
                    <td class="px-1 py-3 text-gray-250 text-center">1</td>
                    <td class="px-4 py-3 font-medium text-left">nama</td>
                    <td class="px-4 py-3 text-gray-500 text-left">nip</td>
                    <td class="px-4 py-3 text-gray-500 text-left">email</td>
                    <td class="px-4 py-3 text-gray-500 text-left">unit kerja</td>
                    <td class="px-4 py-3 text-gray-500 text-left">jabatan</td>
                    <td class="px-4 py-3 text-gray-500 text-left">posisi</td>
                    <td class="px-4 py-3 text-gray-500 text-left">no hp</td>
                    <td class="px-4 py-3 text-center">
                        <button type="post"
                            class="bg-blue-600 text-white px-2 py-1 rounded text-xs hover:bg-blue-700">
                            Edit
                        </button>
                        <button type="post"
                            class="bg-red-600 text-white px-2 py-1 rounded text-xs hover:bg-red-700">
                            Hapus
                        </button>
                    </td>
                </tr>
                <tr>
                    <td colspan="9" class="px-4 py-6 text-center text-gray-400">Belum ada data.</td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection()