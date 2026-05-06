@extends('admin.layout')

@section('content')

    <h1 class="text-2xl font-bold text-gray-800 mb-1">Pengaturan Konversi PDF</h1>
    <p class="text-sm text-gray-500 mb-6">Konfigurasi iLoveAPI dan batas penggunaan konversi PDF untuk fitur preview dokumen.</p>

    <div class="grid grid-cols-3 gap-6">

        {{-- Usage card --}}
        <div class="col-span-3 grid grid-cols-3 gap-4">
            <div class="bg-white rounded-lg shadow p-5 text-center">
                <p class="text-3xl font-bold text-blue-600">{{ $setting->used_count }}</p>
                <p class="text-xs text-gray-500 mt-1">Digunakan Bulan Ini</p>
            </div>
            <div class="bg-white rounded-lg shadow p-5 text-center">
                <p class="text-3xl font-bold {{ $setting->remaining() <= 10 ? 'text-red-500' : 'text-green-600' }}">
                    {{ $setting->remaining() }}
                </p>
                <p class="text-xs text-gray-500 mt-1">Sisa Kuota</p>
            </div>
            <div class="bg-white rounded-lg shadow p-5 text-center">
                <p class="text-3xl font-bold text-gray-700">{{ $setting->monthly_limit }}</p>
                <p class="text-xs text-gray-500 mt-1">Batas Maksimum</p>
            </div>
        </div>

        {{-- Config form --}}
        <div class="col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide border-b pb-2 mb-4">Konfigurasi</h2>

                <form method="POST" action="{{ route('admin.pdf-settings.save') }}">
                    @csrf
                    <div class="space-y-4">

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">
                                    Batas Konversi <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="monthly_limit" min="1"
                                    value="{{ $setting->monthly_limit }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                <p class="text-xs text-gray-400 mt-1">Jumlah maksimum konversi yang diizinkan per periode.</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">
                                    Reset Otomatis <span class="text-red-500">*</span>
                                </label>
                                <select name="reset_on"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="monthly" {{ $setting->reset_on === 'monthly' ? 'selected' : '' }}>Setiap bulan baru</option>
                                    <option value="manual"  {{ $setting->reset_on === 'manual' ? 'selected' : '' }}>Manual saja</option>
                                </select>
                                <p class="text-xs text-gray-400 mt-1">
                                    @if ($setting->last_reset_year)
                                        Terakhir reset: {{ $setting->last_reset_month }}/{{ $setting->last_reset_year }}
                                    @else
                                        Belum pernah direset.
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">iLoveAPI Public Key</label>
                            <input type="text" name="iloveapi_public_key"
                                value="{{ $setting->iloveapi_public_key }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="project_public_..." />
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">iLoveAPI Secret Key</label>
                            <input type="password" name="iloveapi_secret_key"
                                value="{{ $setting->iloveapi_secret_key }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="secret_..." />
                            <p class="text-xs text-gray-400 mt-1">
                                Dapatkan API key di <a href="https://developer.ilovepdf.com" target="_blank" class="text-blue-500 underline">developer.ilovepdf.com</a>.
                            </p>
                        </div>

                        <button type="submit"
                            class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-blue-700 font-medium">
                            Simpan Pengaturan
                        </button>

                    </div>
                </form>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-4">

            {{-- Manual reset --}}
            <div class="bg-white rounded-lg shadow p-5">
                <h2 class="text-sm font-semibold text-gray-700 border-b pb-2 mb-3">Reset Manual</h2>
                <p class="text-xs text-gray-500 mb-3">Reset counter penggunaan ke 0 tanpa menunggu periode berikutnya.</p>
                <form method="POST" action="{{ route('admin.pdf-settings.reset') }}"
                    onsubmit="return confirm('Reset counter konversi PDF ke 0?')">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                        class="w-full border border-red-400 text-red-500 hover:bg-red-50 px-4 py-2 rounded-lg text-sm font-medium">
                        ↺ Reset Counter ke 0
                    </button>
                </form>
            </div>

            {{-- Info --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-xs text-blue-800 space-y-2">
                <p class="font-semibold">Cara Kerja</p>
                <ul class="space-y-1 list-disc list-inside">
                    <li>Setiap preview dokumen mengkonsumsi 1 kuota konversi.</li>
                    <li>Jika kuota habis, tombol preview tidak akan berfungsi.</li>
                    <li>Dengan reset "Setiap bulan baru", counter otomatis nol di awal bulan.</li>
                    <li>Tier gratis iLoveAPI: 250 konversi/bulan.</li>
                </ul>
            </div>

            @if ($setting->remaining() <= 20)
                <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-4 text-xs text-yellow-800">
                    <strong>⚠ Kuota hampir habis!</strong> Hanya tersisa {{ $setting->remaining() }} konversi.
                    Pertimbangkan upgrade ke plan berbayar atau kurangi penggunaan preview.
                </div>
            @endif

        </div>

    </div>

@endsection