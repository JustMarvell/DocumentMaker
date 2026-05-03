@extends('admin.layout')

@section('content')

    <div class="flex items-center gap-3 mb-2">
        <a href="{{ route('admin.document-types') }}" class="text-gray-400 hover:text-gray-600 text-sm">← Jenis Dokumen</a>
        <span class="text-gray-300">/</span>
        <a href="{{ route('admin.document-types.fields', $documentType) }}" class="text-gray-400 hover:text-gray-600 text-sm">{{ $documentType->name }}</a>
        <span class="text-gray-300">/</span>
        <span class="text-gray-600 text-sm">Nomor Surat Otomatis</span>
    </div>

    <h1 class="text-2xl font-bold text-gray-800 mb-1">Nomor Surat Otomatis — {{ $documentType->name }}</h1>
    <p class="text-sm text-gray-500 mb-6">Konfigurasi format dan counter nomor surat yang di-generate otomatis saat dokumen dibuat.</p>

    <div class="grid grid-cols-3 gap-6">

        {{-- Config form --}}
        <div class="col-span-2 space-y-5">

            {{-- Status card --}}
            @if ($counter)
                <div class="bg-white rounded-lg shadow p-5">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Status Saat Ini</p>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-center bg-blue-50 rounded-lg p-3">
                            <p class="text-2xl font-bold text-blue-600">{{ str_pad($counter->current_number, $counter->number_padding, '0', STR_PAD_LEFT) }}</p>
                            <p class="text-xs text-gray-500 mt-1">Nomor Terakhir</p>
                        </div>
                        <div class="text-center bg-green-50 rounded-lg p-3">
                            <p class="text-sm font-bold text-green-700 break-all">{{ $counter->previewNext() }}</p>
                            <p class="text-xs text-gray-500 mt-1">Nomor Berikutnya</p>
                        </div>
                        <div class="text-center {{ $counter->enabled ? 'bg-green-50' : 'bg-gray-50' }} rounded-lg p-3">
                            <p class="text-lg font-bold {{ $counter->enabled ? 'text-green-600' : 'text-gray-400' }}">
                                {{ $counter->enabled ? 'Aktif' : 'Nonaktif' }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Status Fitur</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Main config --}}
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide border-b pb-2 mb-4">Konfigurasi Format</h2>

                <form method="POST" action="{{ route('admin.document-types.number-counter.save', $documentType) }}">
                    @csrf
                    <div class="space-y-4">

                        <div class="flex items-center gap-3">
                            <input type="checkbox" name="enabled" id="enabled" value="1"
                                {{ $counter?->enabled ? 'checked' : '' }}
                                class="w-4 h-4 accent-blue-600" />
                            <label for="enabled" class="text-sm font-medium text-gray-700">
                                Aktifkan Nomor Surat Otomatis
                            </label>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                Format Nomor <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="format"
                                value="{{ $counter?->format ?? '{number}/DPUPR/{roman_month}/{year}' }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500"
                                oninput="updatePreview()" id="format-input" />
                            <p class="text-xs text-gray-400 mt-1">
                                Token: <code class="bg-gray-100 px-1 rounded">{number}</code>
                                <code class="bg-gray-100 px-1 rounded">{year}</code>
                                <code class="bg-gray-100 px-1 rounded">{month}</code>
                                <code class="bg-gray-100 px-1 rounded">{roman_month}</code>
                            </p>
                            <div class="mt-2 bg-blue-50 border border-blue-200 rounded px-3 py-1.5 text-xs text-blue-700">
                                Preview: <strong id="format-preview">{{ $counter?->previewNext() ?? '001/DPUPR/V/2026' }}</strong>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">
                                    Zero-padding (lebar angka) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="number_padding" min="1" max="10"
                                    value="{{ $counter?->number_padding ?? 3 }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    oninput="updatePreview()" id="padding-input" />
                                <p class="text-xs text-gray-400 mt-1">3 → 001, 4 → 0001</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">
                                    Reset otomatis <span class="text-red-500">*</span>
                                </label>
                                <select name="reset_on"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="never"   {{ ($counter?->reset_on ?? 'yearly') === 'never' ? 'selected' : '' }}>Tidak pernah</option>
                                    <option value="yearly"  {{ ($counter?->reset_on ?? 'yearly') === 'yearly' ? 'selected' : '' }}>Setiap tahun baru</option>
                                    <option value="monthly" {{ ($counter?->reset_on ?? 'yearly') === 'monthly' ? 'selected' : '' }}>Setiap bulan baru</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">
                                Field Key tujuan <span class="text-red-500">*</span>
                            </label>
                            <select name="field_key"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @foreach ($fields as $field)
                                    <option value="{{ $field->field_key }}"
                                        {{ ($counter?->field_key ?? 'letter_number') === $field->field_key ? 'selected' : '' }}>
                                        {{ $field->field_key }} — {{ $field->label }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-400 mt-1">Field ini akan diisi otomatis dengan nomor yang digenerate. Isi field di form akan <strong>diabaikan</strong> jika fitur aktif.</p>
                        </div>

                        <button type="submit"
                            class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-blue-700 font-medium">
                            Simpan Konfigurasi
                        </button>
                    </div>
                </form>
            </div>

            {{-- Set / Reset counter --}}
            @if ($counter)
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-sm font-semibold text-gray-700 uppercase tracking-wide border-b pb-2 mb-4">Kelola Counter</h2>

                    <div class="grid grid-cols-2 gap-6">

                        {{-- Set to specific number --}}
                        <div>
                            <p class="text-xs font-medium text-gray-600 mb-2">Set ke nomor tertentu</p>
                            <form method="POST" action="{{ route('admin.document-types.number-counter.set', $documentType) }}">
                                @csrf
                                @method('PATCH')
                                <div class="flex gap-2">
                                    <input type="number" name="current_number" min="0"
                                        placeholder="{{ $counter->current_number }}"
                                        class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                                    <button type="submit"
                                        class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-indigo-700 font-medium whitespace-nowrap">
                                        Set
                                    </button>
                                </div>
                                <p class="text-xs text-gray-400 mt-1">Nomor berikutnya akan menjadi nilai ini + 1.</p>
                            </form>
                        </div>

                        {{-- Reset to zero --}}
                        <div>
                            <p class="text-xs font-medium text-gray-600 mb-2">Reset ke nol</p>
                            <form method="POST" action="{{ route('admin.document-types.number-counter.reset', $documentType) }}"
                                onsubmit="return confirm('Reset counter ke 0? Nomor berikutnya akan dimulai dari 001.')">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="w-full border border-red-400 text-red-500 hover:bg-red-50 px-4 py-2 rounded-lg text-sm font-medium">
                                    ↺ Reset ke 0
                                </button>
                                <p class="text-xs text-gray-400 mt-1">Nomor berikutnya akan menjadi 001 (atau sesuai padding).</p>
                            </form>
                        </div>

                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar info --}}
        <div class="space-y-4">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-xs text-blue-800">
                <p class="font-semibold mb-2">Cara Kerja</p>
                <ol class="list-decimal list-inside space-y-1">
                    <li>Aktifkan fitur dan atur format.</li>
                    <li>Tentukan field key yang akan diisi otomatis (contoh: <code class="bg-blue-100 px-1 rounded">letter_number</code>).</li>
                    <li>Saat user membuat dokumen, field tersebut diisi dengan nomor yang digenerate — input user diabaikan.</li>
                    <li>Counter bertambah 1 setiap dokumen berhasil dibuat.</li>
                </ol>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-xs text-yellow-800">
                <p class="font-semibold mb-2">Token Format</p>
                <table class="w-full">
                    <tr><td class="font-mono pr-2">{number}</td><td>Nomor urut (zero-padded)</td></tr>
                    <tr><td class="font-mono pr-2">{year}</td><td>Tahun 4 digit (2026)</td></tr>
                    <tr><td class="font-mono pr-2">{month}</td><td>Bulan 2 digit (05)</td></tr>
                    <tr><td class="font-mono pr-2">{roman_month}</td><td>Bulan romawi (V)</td></tr>
                </table>
            </div>

            <div class="bg-white rounded-lg shadow p-4 text-xs text-gray-600">
                <p class="font-semibold mb-2 text-gray-700">Contoh Format</p>
                <div class="space-y-1 font-mono">
                    <p class="text-gray-500">{number}/DPUPR/{roman_month}/{year}</p>
                    <p class="text-blue-600">→ 045/DPUPR/V/2026</p>
                    <hr class="my-1.5">
                    <p class="text-gray-500">{number}/TU.00/{year}</p>
                    <p class="text-blue-600">→ 045/TU.00/2026</p>
                    <hr class="my-1.5">
                    <p class="text-gray-500">B-{number}/PUPRD/{roman_month}/{year}</p>
                    <p class="text-blue-600">→ B-045/PUPRD/V/2026</p>
                </div>
            </div>

            <a href="{{ route('admin.document-types.fields', $documentType) }}"
                class="block text-sm text-blue-600 hover:underline">← Kembali ke Kelola Field</a>
        </div>

    </div>

    <script>
    const romanMonths = {1:'I',2:'II',3:'III',4:'IV',5:'V',6:'VI',7:'VII',8:'VIII',9:'IX',10:'X',11:'XI',12:'XII'};
    const now = new Date();
    const currentNumber = {{ $counter?->current_number ?? 0 }};

    function updatePreview() {
        const format  = document.getElementById('format-input').value;
        const padding = parseInt(document.getElementById('padding-input').value) || 3;
        const next    = currentNumber + 1;
        const padded  = String(next).padStart(padding, '0');
        const result  = format
            .replace('{number}',      padded)
            .replace('{year}',        now.getFullYear())
            .replace('{month}',       String(now.getMonth()+1).padStart(2,'0'))
            .replace('{roman_month}', romanMonths[now.getMonth()+1]);
        document.getElementById('format-preview').textContent = result;
    }
    </script>

@endsection