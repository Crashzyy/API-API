@extends('layouts.app')

@section('title', 'Kelola User - Panel Admin')
@section('header-title', 'Manajemen Pengguna Sistem')

@section('content')
    <!-- Notifikasi Sukses/Gagal -->
    @if(session('success'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-lg shadow-sm text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-col gap-4 border-b border-gray-200 bg-gray-50 p-5 sm:flex-row sm:items-center sm:justify-between">
            <h3 class="text-lg font-bold text-gray-800">Daftar Pengguna Sistem</h3>
            <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:items-center">
                <form action="{{ route('admin.user.index') }}" method="GET" class="flex w-full sm:w-80">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, role...."
                        class="w-full rounded-l-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                    <button type="submit" class="rounded-r-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-700">
                        Cari
                    </button>
                    @if(request('search'))
                        <a href="{{ route('admin.user.index') }}"
                        class="ml-2 flex items-center rounded-lg bg-gray-300 px-3 py-2 text-sm text-gray-700 transition hover:bg-gray-400" title="Reset Pencarian">
                            Reset
                        </a>
                    @endif
                </form>
                <!-- Tombol tambah user -->
            </div>
            <a href="{{ route('admin.user.create')}}" class="rounded-lg bg-blue-600 px-4 py-2 text-center text-sm font-semibold text-white transition hover:bg-blue-700">
                + Tambah User
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left">
                <thead class="bg-gray-100">
                    <tr class="text-xs uppercase tracking-wider text-gray-600">
                        <th class="border-b border-gray-200 px-4 py-3">Nama</th>
                        <th class="border-b border-gray-200 px-4 py-3">Email</th>
                        <th class="border-b border-gray-200 px-4 py-3">Role / Hak Akses</th>
                        <th class="border-b border-gray-200 px-4 py-3">No. HP</th>
                        <th class="border-b border-gray-200 px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse($users as $user)
                        <tr class="transition hover:bg-gray-50">
                            <td class="border-b border-gray-100 px-4 py-4 font-medium text-gray-900">{{ $user->name }}</td>
                            <td class="border-b border-gray-100 px-4 py-4">{{ $user->email }}</td>
                            <td class="border-b border-gray-100 px-4 py-4">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full 
                                    @if($user->role == 'admin') bg-purple-100 text-purple-800 
                                    @elseif($user->role == 'petugas') bg-blue-100 text-blue-800 
                                    @else bg-green-100 text-green-800 @endif">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="border-b border-gray-100 px-4 py-4">{{ $user->no_hp ?? '-' }}</td>
                            <td class="border-b border-gray-100 px-4 py-4">
                                <div class="flex items-center gap-2">
                                <!-- Tombol Edit -->
                                    <a href="{{ route('admin.user.edit', $user->id) }}" class="rounded-md bg-amber-500 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-amber-600">Edit</a>
                                    
                                <!-- Tombol Hapus -->
                                <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus user ini')"> 
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-md bg-red-500 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-red-600">
                                    Hapus
                                    </button>
                                </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada data pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 bg-gray-50 p-4">
            {{ $users->links() }}
        </div>
    </div>
@endsection
