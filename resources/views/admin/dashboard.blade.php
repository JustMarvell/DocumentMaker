@extends('admin.layout')

@section('content')

    <h1 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h1>

    {{-- Stat cards --}}
    <div class="grid grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500">Total Dokumen Dibuat</p>
            <p class="text-3xl font-bold text-blue-600 mt-1">{{ $totalGenerated }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500">Total Gagal</p>
            <p class="text-3xl font-bold text-red-500 mt-1">{{ $totalFailed }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500">Total Pengguna</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ $totalUsers }}</p>
        </div>
    </div>

    {{-- Email Test --}} 
    <div class="bg-white rounded-lg shadow p-5 mb-6">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h2 class="text-sm font-semibold text-gray-700">Uji Koneksi Email</h2>
                <p class="text-xs text-gray-400 mt-0.5">Kirim email uji untuk memastikan konfigurasi SMTP berjalan.</p>
            </div>
            <div id="email-test-status" class="hidden text-xs px-3 py-1.5 rounded-lg font-medium"></div>
        </div>
        <form id="email-test-form" class="flex gap-3 items-end">
            @csrf
            <div class="flex-1">
                <label class="block text-xs text-gray-500 mb-1">Kirim ke alamat email</label>
                <input type="email" name="test_email" id="test-email-input" value="{{ auth()->user()->email }}"
                    class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="email@example.com" required />
            </div>
            <button type="button" onclick="sendTestEmail()" id="email-test-btn"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 font-medium flex items-center gap-2 whitespace-nowrap">
                <svg id="email-test-icon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span id="email-test-label">Kirim Test Email</span>
            </button>
        </form>
    </div>

    <script>
        async function sendTestEmail() {
            const btn   = document.getElementById('email-test-btn');
            const label = document.getElementById('email-test-label');
            const icon  = document.getElementById('email-test-icon');
            const status = document.getElementById('email-test-status');
            const email  = document.getElementById('test-email-input').value.trim();

            if (!email) return;

            btn.disabled = true;
            label.textContent = 'Mengirim...';
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 100 16 8 8 0 01-8-8z"/>';
            status.classList.add('hidden');

            try {
                const fd = new FormData();
                fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                fd.append('test_email', email);

                const res  = await fetch('{{ route('admin.test-email') }}', { method: 'POST', body: fd });
                const text = await res.text();
                const ok   = res.ok;

                status.className = ok
                    ? 'text-xs px-3 py-1.5 rounded-lg font-medium bg-green-100 text-green-700'
                    : 'text-xs px-3 py-1.5 rounded-lg font-medium bg-red-100 text-red-600';
                status.textContent = ok ? '✓ Email berhasil dikirim' : '✕ Gagal — periksa konfigurasi SMTP';
                status.classList.remove('hidden');
            } catch(e) {
                status.className = 'text-xs px-3 py-1.5 rounded-lg font-medium bg-red-100 text-red-600';
                status.textContent = '✕ Koneksi gagal: ' + e.message;
                status.classList.remove('hidden');
            } finally {
                btn.disabled = false;
                label.textContent = 'Kirim Test Email';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>';
            }
        }
    </script>

    <div class="grid grid-cols-2 gap-6">

        {{-- Per-type breakdown --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Dokumen per Jenis</h2>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="pb-2">Jenis Dokumen</th>
                        <th class="pb-2 text-center">Berhasil</th>
                        <th class="pb-2 text-center">Gagal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($perType as $type)
                        <tr class="border-b last:border-0">
                            <td class="py-2">{{ $type->name }}</td>
                            <td class="py-2 text-center text-blue-600 font-semibold">{{ $type->success_count }}</td>
                            <td class="py-2 text-center text-red-500">{{ $type->failed_count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Recent activity --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Aktivitas Terbaru</h2>
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="pb-2">Pengguna</th>
                        <th class="pb-2">Jenis</th>
                        <th class="pb-2">Status</th>
                        <th class="pb-2">Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recentLogs as $log)
                        <tr class="border-b last:border-0">
                            <td class="py-2">{{ $log->user?->name ?? 'Guest' }}</td>
                            <td class="py-2">{{ $log->documentType->name }}</td>
                            <td class="py-2">
                                @if ($log->status === 'success')
                                    <span class="text-green-600 font-semibold">Berhasil</span>
                                @else
                                    <span class="text-red-500 font-semibold">Gagal</span>
                                @endif
                            </td>
                            <td class="py-2 text-gray-400">{{ $log->generated_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <a href="{{ route('admin.logs') }}" class="text-sm text-blue-600 hover:underline mt-4 inline-block">
                Lihat semua →
            </a>
        </div>

    </div>

@endsection