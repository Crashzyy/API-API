@extends('layouts.app')

@section('title', 'Kelola Pengembalian - Panel Admin')
@section('header-title', 'Kelola Pengembalian Alat')

@section('content')
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 bg-gray-50 p-5">
            <h3 class="text-lg font-bold text-gray-800">Riwayat Pengembalian</h3>
            <p class="mt-1 text-sm text-gray-500">Daftar alat yang sudah dikembalikan oleh peminjam.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[850px] text-left">
                <thead class="bg-gray-100">
                    <tr class="text-xs uppercase tracking-wider text-gray-600">
                        <th class="border-b border-gray-200 px-4 py-3">Peminjam</th>
                        <th class="border-b border-gray-200 px-4 py-3">Alat</th>
                        <th class="border-b border-gray-200 px-4 py-3">Tanggal Kembali</th>
                        <th class="border-b border-gray-200 px-4 py-3">Kondisi</th>
                        <th class="border-b border-gray-200 px-4 py-3">Denda</th>
                        <th class="border-b border-gray-200 px-4 py-3">Petugas</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700">
                    @forelse($pengembalians as $pengembalian)
                        <tr class="transition hover:bg-gray-50">
                            <td class="border-b border-gray-100 px-4 py-4 font-semibold text-gray-900">
                                {{ $pengembalian->peminjaman->user->name ?? '-' }}
                            </td>
                            <td class="border-b border-gray-100 px-4 py-4">
                                @forelse($pengembalian->peminjaman->detailPinjams ?? [] as $detail)
                                    <div>{{ $detail->alat->nama_alat ?? '-' }} ({{ $detail->jumlah }})</div>
                                @empty
                                    -
                                @endforelse
                            </td>
                            <td class="whitespace-nowrap border-b border-gray-100 px-4 py-4">
                                {{ optional($pengembalian->tgl_kembali)->format('d-m-Y') ?? '-' }}
                            </td>
                            <td class="border-b border-gray-100 px-4 py-4">
                                {{ $pengembalian->kondisi_kembali ?? '-' }}
                            </td>
                            <td class="border-b border-gray-100 px-4 py-4">
                                Rp {{ number_format($pengembalian->denda ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="border-b border-gray-100 px-4 py-4">
                                {{ $pengembalian->petugas->name ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                Belum ada data pengembalian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 bg-gray-50 p-4">
            {{ $pengembalians->links() }}
        </div>
    </div>
@endsection
