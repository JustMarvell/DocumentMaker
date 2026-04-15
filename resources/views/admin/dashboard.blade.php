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