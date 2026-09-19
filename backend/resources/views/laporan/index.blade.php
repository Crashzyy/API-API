@extends('layouts.app')

@section('title', 'Cetak Laporan - Sistem Peminjaman Alat')
@section('header-title', 'Laporan Peminjaman Alat')

@section('content')
    <div class="space-y-6">
        <div class="no-print rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <form action="{{ request()->routeIs('admin.laporan.*') ? route('admin.laporan.index') : route('petugas.laporan.index') }}" method="GET" class="flex w-full flex-col gap-3 lg:flex-row lg:items-end">
                    <div>
                        <label for="mulai" class="mb-2 block text-sm font-semibold text-gray-700">Dari tanggal</label>
                        <input id="mulai" type="date" name="mulai" value="{{ $mulai }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                    </div>
                    <div>
                        <label for="selesai" class="mb-2 block text-sm font-semibold text-gray-700">Sampai tanggal</label>
                        <input id="selesai" type="date" name="selesai" value="{{ $selesai }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                    </div>
                    <div>
                        <label for="status" class="mb-2 block text-sm font-semibold text-gray-700">Status</label>
                        <select id="status" name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
                            <option value="">Semua status</option>
                            @foreach(['diajukan', 'dipinjam', 'selesai', 'telat'] as $pilihanStatus)
                                <option value="{{ $pilihanStatus }}" {{ $status === $pilihanStatus ? 'selected' : '' }}>{{ ucfirst($pilihanStatus) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit" class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-gray-700">Tampilkan</button>
                        <a href="{{ request()->routeIs('admin.laporan.*') ? route('admin.laporan.index') : route('petugas.laporan.index') }}" class="rounded-lg bg-gray-300 px-4 py-2 text-sm font-semibold text-gray-800 transition hover:bg-gray-400">Reset</a>
                    </div>
                </form>

                <div class="flex flex-wrap items-end gap-2">
                    <a href="{{ route(request()->routeIs('admin.laporan.*') ? 'admin.laporan.excel' : 'petugas.laporan.excel', request()->query()) }}" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-green-700">Excel</a>
                    <a href="{{ route(request()->routeIs('admin.laporan.*') ? 'admin.laporan.pdf' : 'petugas.laporan.pdf', request()->query()) }}" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700">PDF</a>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="mb-6 border-b border-gray-200 pb-5 text-center">
                <h1 class="text-2xl font-bold text-gray-900">Laporan Peminjaman Alat</h1>
                <p class="mt-1 text-sm text-gray-500">Sistem Peminjaman Alat</p>
                @if($mulai || $selesai || $status)
                    <p class="mt-2 text-sm text-gray-600">
                        Periode: {{ $mulai ?: 'awal' }} s/d {{ $selesai ?: 'sekarang' }}
                        @if($status) · Status: {{ ucfirst($status) }} @endif
                    </p>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left">
                    <thead class="bg-gray-100">
                        <tr class="text-xs uppercase tracking-wider text-gray-600">
                            <th class="border-b border-gray-200 px-4 py-3">No</th>
                            <th class="border-b border-gray-200 px-4 py-3">Peminjam</th>
                            <th class="border-b border-gray-200 px-4 py-3">Alat</th>
                            <th class="border-b border-gray-200 px-4 py-3">Tgl Pinjam</th>
                            <th class="border-b border-gray-200 px-4 py-3">Rencana Kembali</th>
                            <th class="border-b border-gray-200 px-4 py-3">Status</th>
                            <th class="border-b border-gray-200 px-4 py-3">Tgl Dikembalikan</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-700">
                        @forelse($peminjamans as $index => $peminjaman)
                            <tr class="hover:bg-gray-50">
                                <td class="border-b border-gray-100 px-4 py-3">{{ $index + 1 }}</td>
                                <td class="border-b border-gray-100 px-4 py-3 font-semibold text-gray-900">{{ $peminjaman->user->name ?? '-' }}</td>
                                <td class="border-b border-gray-100 px-4 py-3">
                                    @forelse($peminjaman->detailPinjams as $detail)
                                        <div>{{ $detail->alat->nama_alat ?? '-' }} ({{ $detail->jumlah }})</div>
                                    @empty
                                        -
                                    @endforelse
                                </td>
                                <td class="whitespace-nowrap border-b border-gray-100 px-4 py-3">{{ optional($peminjaman->tgl_pinjam)->format('d-m-Y') ?? '-' }}</td>
                                <td class="whitespace-nowrap border-b border-gray-100 px-4 py-3">{{ optional($peminjaman->tgl_kembali_plan)->format('d-m-Y') ?? '-' }}</td>
                                <td class="border-b border-gray-100 px-4 py-3">{{ ucfirst($peminjaman->status) }}</td>
                                <td class="whitespace-nowrap border-b border-gray-100 px-4 py-3">{{ optional(optional($peminjaman->pengembalian)->tgl_kembali)->format('d-m-Y') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">Belum ada data laporan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
