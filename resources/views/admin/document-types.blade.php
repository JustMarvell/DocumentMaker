@extends('admin.layout')
@section('page-title', 'Jenis Dokumen')

@section('content')
<div class="space-y-4 fade-up">

    <div class="flex items-center justify-between">
        <div>
            <div class="section-label mb-1">Admin Panel</div>
            <h1 class="display-heading" style="font-size:1.35rem;">Jenis Dokumen</h1>
        </div>
        <a href="{{ route('admin.document-types.create') }}" class="btn-primary">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Dokumen
        </a>
    </div>

    <div class="glass-card rounded-2xl overflow-hidden fade-up fade-up-1">
        <div class="overflow-x-auto">
            <table class="sipadu-table">
                <thead>
                    <tr>
                        <th style="width:40px;">#</th>
                        <th>Nama Dokumen</th>
                        <th>Key</th>
                        <th>Format</th>
                        <th>Akses</th>
                        <th>Fields</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documentTypes as $type)
                    <tr>
                        <td style="color:var(--slate-300);font-size:0.75rem;font-family:var(--font-mono);">{{ $loop->iteration }}</td>
                        <td>
                            <span style="font-weight:600;color:var(--slate-800);font-size:0.82rem;">{{ $type->name }}</span>
                        </td>
                        <td>
                            <code style="font-family:var(--font-mono);font-size:0.72rem;background:var(--slate-100);padding:0.15rem 0.45rem;border-radius:4px;color:var(--navy-600);">
                                {{ $type->key }}
                            </code>
                        </td>
                        <td>
                            @php $fmt = strtolower($type->file_type ?? 'docx'); @endphp
                            <span class="badge {{ ['docx'=>'badge-navy','xlsx'=>'badge-green'][$fmt] ?? 'badge-gray' }}" style="font-size:0.65rem;text-transform:uppercase;">
                                {{ strtoupper($fmt) }}
                            </span>
                        </td>
                        <td>
                            @php $access = $type->access_level ?? 'staff'; @endphp
                            <span class="badge {{ $access === 'guest' ? 'badge-gray' : 'badge-gold' }}" style="font-size:0.65rem;">
                                {{ ucfirst($access) }}
                            </span>
                        </td>
                        <td style="color:var(--slate-500);font-size:0.8rem;">
                            {{ $type->fields_count ?? $type->fields->count() }}
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:0.4rem;">
                                <a href="{{ route('admin.document-types.edit', $type) }}"
                                   class="btn-outline-navy" style="padding:0.25rem 0.65rem;font-size:0.72rem;">
                                    Edit
                                </a>
                                <a href="{{ route('admin.document-types.fields', $type) }}"
                                   class="btn-primary" style="padding:0.25rem 0.65rem;font-size:0.72rem;">
                                    Fields
                                </a>
                                <form method="POST" action="{{ route('admin.document-types.destroy', $type) }}"
                                      onsubmit="return confirm('Hapus dokumen {{ addslashes($type->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        style="width:26px;height:26px;border:1px solid rgba(239,68,68,0.25);border-radius:6px;background:rgba(239,68,68,0.06);color:rgba(239,68,68,0.7);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all 0.2s;font-size:0.8rem;"
                                        onmouseover="this.style.background='rgba(239,68,68,0.15)';this.style.color='rgb(239,68,68)'"
                                        onmouseout="this.style.background='rgba(239,68,68,0.06)';this.style.color='rgba(239,68,68,0.7)'">
                                        <svg style="width:11px;height:11px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-10" style="color:var(--slate-300);">
                            Belum ada jenis dokumen. <a href="{{ route('admin.document-types.create') }}" style="color:var(--navy-500);">Tambahkan sekarang →</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection