@extends('admin.layout')

@section('content')

    <div class="flex items-center gap-3 mb-2">
        <a href="{{ route('admin.document-types') }}" class="text-gray-400 hover:text-gray-600 text-sm">← Jenis Dokumen</a>
        <span class="text-gray-300">/</span>
        <a href="{{ route('admin.document-types.fields', $documentType) }}"
            class="text-gray-400 hover:text-gray-600 text-sm">
            {{ $documentType->name }}
        </a>
        <span class="text-gray-300">/</span>
        <span class="text-gray-600 text-sm">Autofill Slots</span>
    </div>

    <h1 class="text-2xl font-bold text-gray-800 mb-2">Autofill Slots — {{ $documentType->name }}</h1>
    <p class="text-sm text-gray-500 mb-6">
        Slot menentukan berapa banyak dropdown autofill yang muncul di form dan apa label tiap dropdown.
        Setiap field bisa diassign ke salah satu slot melalui halaman Kelola Field.
    </p>

    <div class="grid grid-cols-5 gap-6">

        {{-- Add slot form --}}
        <div class="col-span-2">
            <div class="bg-white rounded-lg shadow p-5">
                <h2 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide border-b pb-2">
                    Tambah Slot Baru
                </h2>
                <form method="POST" action="{{ route('admin.document-types.slots.store', $documentType) }}">
                    @csrf
                    <div class="space-y-4">

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                Slot Key <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="slot_key" value="{{ old('slot_key') }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="employee" />
                            <p class="text-xs text-gray-400 mt-0.5">
                                Hanya huruf kecil, angka, underscore. Dipakai di field manager sebagai nilai Autofill Role.
                            </p>
                            @error('slot_key') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                Label <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="slot_label" value="{{ old('slot_label') }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Pegawai" />
                            <p class="text-xs text-gray-400 mt-0.5">
                                Label yang tampil di form sebagai judul dropdown autofill.
                            </p>
                            @error('slot_label') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit"
                            class="w-full bg-blue-600 text-white py-2 rounded-lg text-sm hover:bg-blue-700 font-medium">
                            + Tambah Slot
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-4 text-xs text-blue-800">
                <p class="font-semibold mb-1">Cara penggunaan:</p>
                <ol class="list-decimal list-inside space-y-1">
                    <li>Tambahkan slot di sini (misal: key=<code class="bg-blue-100 px-1 rounded">employee</code>,
                        label=<code class="bg-blue-100 px-1 rounded">Pegawai</code>)</li>
                    <li>Buka <a href="{{ route('admin.document-types.fields', $documentType) }}"
                            class="underline font-medium">Kelola Field</a></li>
                    <li>Set <em>Autofill Role</em> tiap field sesuai slot key yang sudah dibuat</li>
                    <li>Di form, user akan melihat dropdown "Pegawai" yang mengisi field dengan autofill role <code
                            class="bg-blue-100 px-1 rounded">employee</code></li>
                </ol>
            </div>
        </div>

        {{-- Slot list --}}
        <div class="col-span-3">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-5 py-4 border-b">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">
                        Slot Terdaftar ({{ $slots->count() }})
                    </h2>
                </div>

                @if ($slots->isEmpty())
                    <div class="px-5 py-10 text-center text-gray-400 text-sm">
                        Belum ada slot. Tambahkan slot menggunakan form di sebelah kiri.
                    </div>
                @else
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-left text-xs uppercase">
                            <tr>
                                <th class="px-4 py-3">Urutan</th>
                                <th class="px-4 py-3">Slot Key</th>
                                <th class="px-4 py-3">Label</th>
                                <th class="px-4 py-3">Fields yang Menggunakan</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($slots as $slot)
                                @php
                                    $fieldsUsingSlot = $documentType->fields()
                                        ->where('autofill_role', $slot->slot_key)
                                        ->pluck('label');
                                @endphp
                                <tr>
                                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $slot->sort_order }}</td>
                                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $slot->slot_key }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-800">{{ $slot->slot_label }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-500">
                                        @if ($fieldsUsingSlot->isEmpty())
                                            <span class="text-gray-300">— belum ada field —</span>
                                        @else
                                            {{ $fieldsUsingSlot->implode(', ') }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <form method="POST"
                                            action="{{ route('admin.document-types.slots.destroy', [$documentType, $slot]) }}"
                                            onsubmit="return confirm('Hapus slot {{ addslashes($slot->slot_label) }}? Field yang menggunakan slot ini perlu di-update secara manual.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-xs px-3 py-1 rounded border border-red-400 text-red-500 hover:bg-red-50">
                                                Hapus
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <div class="mt-4">
                <a href="{{ route('admin.document-types.fields', $documentType) }}"
                    class="text-sm text-blue-600 hover:underline">
                    ← Kembali ke Kelola Field
                </a>
            </div>
        </div>
    </div>

@endsection