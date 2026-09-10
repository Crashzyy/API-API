<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Alat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PetugasController extends Controller
{
    //Menampilkan daftar pengajuan peminjaman dari peminjam
    public function indexPeminjaman() {
        $peminjamans = Peminjaman::with(['user', 'detailPinjams.alat'])->latest()->get();
        return view('petugas.peminjaman.index', compact('peminjamans'));
    }

public function setujuiPeminjaman($id)
{
    $peminjaman = Peminjaman::with('detailPinjams.alat')
        ->findOrFail($id);

    if ($peminjaman->status !== 'diajukan') {
        return back()->with('error', 'Peminjaman sudah diproses.');
    }

    DB::beginTransaction();

    try {
        foreach ($peminjaman->detailPinjams as $detail) {
            $alat = $detail->alat;

            if (!$alat || $alat->stok < $detail->jumlah) {
                throw new \Exception(
                    "Stok alat {$alat->nama_alat} tidak mencukupi."
                );
            }

            $alat->decrement('stok', $detail->jumlah);
        }

        $peminjaman->update([
            'status' => 'dipinjam',
        ]);

        DB::commit();

        return back()->with(
            'success',
            'Peminjaman disetujui dan stok alat dikurangi.'
        );
    } catch (\Exception $e) {
        DB::rollBack();

        return back()->with('error', $e->getMessage());
    }
}
public function prosesPengembalian(Request $request, $peminjamanId)
{
    $request->validate([
        'kondisi_kembali' => 'required|string',
        'denda' => 'nullable|integer|min:0',
    ]);

    $peminjaman = Peminjaman::with('detailPinjams.alat')
        ->findOrFail($peminjamanId);

    if ($peminjaman->status !== 'dipinjam') {
        return back()->with(
            'error',
            'Peminjaman belum berstatus dipinjam.'
        );
    }

    DB::beginTransaction();

    try {
        Pengembalian::create([
            'peminjaman_id' => $peminjaman->id,
            'tgl_kembali' => now(),
            'kondisi_kembali' => $request->kondisi_kembali,
            'denda' => $request->denda ?? 0,
            'petugas_id' => auth()->id(),
        ]);

        foreach ($peminjaman->detailPinjams as $detail) {
            $detail->alat->increment('stok', $detail->jumlah);
        }

        $peminjaman->update([
            'status' => 'selesai',
        ]);

        DB::commit();

        return back()->with(
            'success',
            'Pengembalian berhasil dicatat.'
        );
    } catch (\Exception $e) {
        DB::rollBack();

        return back()->with('error', $e->getMessage());
    }
}
}
