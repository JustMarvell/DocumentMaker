@extends('admin.layout')

@section('content')

    <h1 class="text-2xl font-bold text-gray-800 mb-6">Manajemen Pengguna</h1>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-left">
                <tr>
                    <th class="px-4 py-3">Nama</th>
                    <th class="px-4 py-3">NIP</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Unit Kerja</th>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3">Total Dokumen</th>
                    <th class="px-4 py-3">Ubah Role</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($users as $user)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $user->nip ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $user->email }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $user->work_unit ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded text-xs font-semibold
                                        {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-700' : '' }}
                                        {{ $user->role === 'staff' ? 'bg-blue-100 text-blue-700' : '' }}
                                        {{ $user->role === 'guest' ? 'bg-gray-100 text-gray-600' : '' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">{{ $user->document_logs_count }}</td>
                        <td class="px-4 py-3">
                            @if ($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.updateRole', $user) }}"
                                    class="flex gap-2 items-center">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role" class="border rounded px-2 py-1 text-xs">
                                        <option value="guest" {{ $user->role === 'guest' ? 'selected' : '' }}>Guest</option>
                                        <option value="staff" {{ $user->role === 'staff' ? 'selected' : '' }}>Staff</option>
                                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                    </select>
                                    <button type="submit"
                                        class="bg-blue-600 text-white px-2 py-1 rounded text-xs hover:bg-blue-700">
                                        Simpan
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-gray-400">Belum ada pengguna.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>

@endsection