<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $mulai = $request->input('mulai');
        $selesai = $request->input('selesai');

        $peminjamans = Peminjaman::with([
            'user',
            'detailPinjams.alat',
            'pengembalian.petugas',
        ])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($mulai, fn ($query) => $query->whereDate('tgl_pinjam', '>=', $mulai))
            ->when($selesai, fn ($query) => $query->whereDate('tgl_pinjam', '<=', $selesai))
            ->latest('tgl_pinjam')
            ->get();

        return view('laporan.index', compact('peminjamans', 'status', 'mulai', 'selesai'));
    }
}
