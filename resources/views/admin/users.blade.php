@extends('admin.layout')
@section('page-title', 'Manajemen Pengguna')

@section('content')
<div class="space-y-4 fade-up">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <div class="section-label mb-1">Admin Panel</div>
            <h1 class="display-heading" style="font-size:1.35rem;">Manajemen Pengguna</h1>
        </div>
        <span class="badge badge-navy">{{ $users->total() ?? count($users) }} pengguna</span>
    </div>

    {{-- Search & filter bar --}}
    <div class="glass-card rounded-xl px-4 py-3 flex flex-wrap items-center gap-3 fade-up fade-up-1">
        <form method="GET" action="{{ route('admin.users') }}" class="flex flex-wrap gap-2 flex-1">
            <div class="flex-1 min-w-48 relative">
                <svg style="position:absolute;left:0.65rem;top:50%;transform:translateY(-50%);width:14px;height:14px;color:var(--slate-300);"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-input" style="padding-left:2rem;"
                       placeholder="Cari nama atau email...">
            </div>
            <select name="role" class="form-input" style="width:auto;cursor:pointer;">
                <option value="">Semua Role</option>
                @foreach(['admin','staff','guest'] as $r)
                    <option value="{{ $r }}" {{ request('role') === $r ? 'selected' : '' }}>
                        {{ ucfirst($r) }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary" style="padding:0.6rem 1rem;">Cari</button>
            @if(request('search') || request('role'))
                <a href="{{ route('admin.users') }}" class="btn-outline-navy" style="padding:0.6rem 1rem;">Reset</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="glass-card rounded-2xl overflow-hidden fade-up fade-up-2">
        <div class="overflow-x-auto">
            <table class="sipadu-table">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Terdaftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $i => $user)
                    <tr>
                        <td style="color:var(--slate-300);font-size:0.75rem;font-family:var(--font-mono);">
                            {{ $users->firstItem() + $loop->index }}
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.6rem;">
                                <div style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,var(--navy-600),var(--navy-400));display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <span style="color:#fff;font-size:0.7rem;font-weight:700;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </span>
                                </div>
                                <span style="font-weight:600;color:var(--slate-800);font-size:0.82rem;">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td style="color:var(--slate-500);font-size:0.8rem;">{{ $user->email }}</td>
                        <td>
                            @php $roleBadge = ['admin' => 'badge-navy', 'staff' => 'badge-gold', 'guest' => 'badge-gray'][$user->role] ?? 'badge-gray'; @endphp
                            <span class="badge {{ $roleBadge }}" style="font-size:0.65rem;">{{ ucfirst($user->role) }}</span>
                        </td>
                        <td style="color:var(--slate-400);font-size:0.75rem;white-space:nowrap;">
                            {{ $user->created_at->format('d M Y') }}
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.5rem;">
                                @if($user->id !== auth()->id())
                                    {{-- Role toggle --}}
                                    <form method="POST" action="{{ route('admin.users.update-role', $user) }}">
                                        @csrf @method('PATCH')
                                        <select name="role" onchange="this.form.submit()"
                                                style="border:1px solid var(--slate-200);border-radius:6px;padding:0.3rem 0.5rem;font-size:0.72rem;font-family:var(--font-body);color:var(--slate-700);background:#fff;cursor:pointer;outline:none;">
                                            @foreach(['admin','staff','guest'] as $r)
                                                <option value="{{ $r }}" {{ $user->role === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                                            @endforeach
                                        </select>
                                    </form>

                                    {{-- Delete --}}
                                    <form method="POST" action="{{ route('admin.users.delete', $user) }}"
                                          onsubmit="return confirm('Hapus pengguna {{ addslashes($user->name) }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                style="width:26px;height:26px;border:1px solid rgba(239,68,68,0.25);border-radius:6px;background:rgba(239,68,68,0.06);color:rgba(239,68,68,0.7);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.2s;font-size:0.8rem;"
                                                title="Hapus"
                                                onmouseover="this.style.background='rgba(239,68,68,0.15)';this.style.color='rgb(239,68,68)'"
                                                onmouseout="this.style.background='rgba(239,68,68,0.06)';this.style.color='rgba(239,68,68,0.7)'">
                                            <svg style="width:11px;height:11px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                @else
                                    <span class="badge badge-gold" style="font-size:0.62rem;">Anda</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-10" style="color:var(--slate-300);">
                            Tidak ada pengguna ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users instanceof \Illuminate\Pagination\LengthAwarePaginator && $users->hasPages())
        <div style="padding:0.75rem 1.25rem;border-top:1px solid var(--slate-100);display:flex;align-items:center;justify-content:space-between;">
            <span style="font-size:0.75rem;color:var(--slate-400);">
                Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }}
            </span>
            <div style="display:flex;gap:0.35rem;">
                @if($users->onFirstPage())
                    <span style="padding:0.3rem 0.65rem;border-radius:6px;font-size:0.75rem;color:var(--slate-300);border:1px solid var(--slate-200);">←</span>
                @else
                    <a href="{{ $users->previousPageUrl() }}"
                       style="padding:0.3rem 0.65rem;border-radius:6px;font-size:0.75rem;color:var(--navy-600);border:1px solid var(--navy-100);text-decoration:none;transition:all 0.15s;"
                       onmouseover="this.style.background='var(--navy-100)'"
                       onmouseout="this.style.background='transparent'">←</a>
                @endif
                @foreach($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                    <a href="{{ $url }}"
                       style="padding:0.3rem 0.65rem;border-radius:6px;font-size:0.75rem;text-decoration:none;transition:all 0.15s;
                              {{ $page === $users->currentPage() ? 'background:var(--navy-700);color:#fff;border:1px solid var(--navy-700);' : 'color:var(--navy-600);border:1px solid var(--navy-100);' }}"
                       {{ $page !== $users->currentPage() ? 'onmouseover="this.style.background=\'var(--navy-100)\'" onmouseout="this.style.background=\'transparent\'"' : '' }}>
                        {{ $page }}
                    </a>
                @endforeach
                @if($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}"
                       style="padding:0.3rem 0.65rem;border-radius:6px;font-size:0.75rem;color:var(--navy-600);border:1px solid var(--navy-100);text-decoration:none;transition:all 0.15s;"
                       onmouseover="this.style.background='var(--navy-100)'"
                       onmouseout="this.style.background='transparent'">→</a>
                @else
                    <span style="padding:0.3rem 0.65rem;border-radius:6px;font-size:0.75rem;color:var(--slate-300);border:1px solid var(--slate-200);">→</span>
                @endif
            </div>
        </div>
        @endif
    </div>

</div>
@endsection