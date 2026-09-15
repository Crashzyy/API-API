@extends('layouts.app')

@section('title', 'Dashboard Admin - Sistem Peminjaman')
@section('header-title', 'Ringkasan Aktivitas Sistem')

@section('content')
    <!-- Alert Selamat Datang -->
    <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-5 text-emerald-800 shadow-sm">
        Selamat datang, <strong class="font-semibold">{{ auth()->user()->name }}</strong>! Anda login sebagai hak akses
        <span class="uppercase font-bold text-emerald-900">{{ auth()->user()->role }}</span>.
    </div>

    <!-- Tabel Log Aktivitas -->
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-5">
            <h3 class="text-lg font-bold text-slate-900">Log Aktivitas Terbaru</h3>
            <p class="mt-1 text-sm text-slate-500">Aktivitas terbaru yang tercatat di dalam sistem.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] text-left">
                <thead class="bg-slate-50">
                    <tr class="text-xs uppercase tracking-wider text-slate-500">
                        <th class="whitespace-nowrap border-b border-slate-200 px-5 py-4 font-semibold">Waktu</th>
                        <th class="whitespace-nowrap border-b border-slate-200 px-5 py-4 font-semibold">User</th>
                        <th class="border-b border-slate-200 px-5 py-4 font-semibold">Aktivitas</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-slate-700">
                    @forelse($logs as $log)
                        <tr class="transition hover:bg-slate-50">
                            <td class="whitespace-nowrap border-b border-slate-100 px-5 py-4 align-top text-slate-500">{{ $log->created_at }}</td>
                            <td class="border-b border-slate-100 px-5 py-4 align-top font-semibold text-slate-900">{{ $log->user->name ?? 'Sistem' }}</td>
                            <td class="border-b border-slate-100 px-5 py-4 align-top leading-6">{{ $log->aktivitas }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-8 text-center text-slate-500">Belum ada log aktivitas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
