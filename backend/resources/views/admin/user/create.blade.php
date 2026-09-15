@extends('layouts.app')

@section('title', 'Tambah User - Panel Admin')
@section('header-title', 'Tambah Pengguna Baru')

@section('content')
<div class="max-w-xl rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
<form action="{{ route('admin.user.store') }}" method="POST">
    @csrf

    <div class="mb-4">
        <label class="block text-gray-700 text-sm font-semibold mb-2">Nama Lengkap</label>
        <input type="text" name="name" value="{{ old('name') }}" required
            class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-400">
        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>

    <div class="mb-4">
        <label class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required 
            class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-400">
        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        <div class="mb-4">
    <label class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
    <input type="password" name="password" required
        class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-400">
    @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
</div>

<div class="mb-4">
    <label class="block text-gray-700 text-sm font-semibold mb-2">Role / Hak Akses</label>
    <select name="role" required class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-400">
        <option value="peminjam">Peminjam</option>
        <option value="petugas">Petugas</option>
        <option value="admin">Admin</option>
    </select>
</div>

<div class="mb-6">
    <label class="block text-gray-700 text-sm font-semibold mb-2">No. HP (Opsional)</label>
    <input type="text" name="no_hp" value="{{ old('no_hp') }}"
        class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-400">
</div>

<div class="flex justify-end space-x-2">
    <a href="{{ route('admin.user.index') }}"
        class="rounded-lg bg-gray-300 px-4 py-2 text-sm font-semibold text-gray-800 transition hover:bg-gray-400">Batal</a>
    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700">Simpan</button>
</div>
</form>
</div>
@endsection
