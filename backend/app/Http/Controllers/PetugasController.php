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

    public function setujuiPeminjaman($id) {
        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('detailPinjams')->findOrFail($id);
            $peminjaman->update(['status' => 'dipinjam']);

            //kurangi stok alat secara otomatis
            foreach ($peminjaman->detailPinjams as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->stok -= $detail->jumlah;
                $alat->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Peminjaman disetujui dan stok alat dikurangi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan' . $e->getMassage());
        }
    }
    public function prosesPengembalian (Request $request, $peminjamanId) {
        $request->validate([
            'kondisi_kembali' => 'required|string',
            'denda' => 'nullable|integer', 
        ]);

        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('detailPinjams')->findOrFail($peminjamanId);

            //simpan data pengembalian
            Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'tgl_kembali' => now(),
                'kondisi_kembali' => $request->kondisi_kembali,
                'denda' => $request->denda ?? 0,
                'petugas_id' => auth()->id(),
            ]);

            //update status peminjaman jadi selesai
            $peminjaman->update(['status' => 'selesai']);

            //kembalikan stok alat ke inventaris
            foreach ($peminjaman->detailPinjams as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->stok += $detail->jumlah;
                $alat->save();

            }

            DB::commit();
            return redirect()->back()->with('success', 'Pengembalian berhasil dicatat dan stok dipulihkanl.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi Kesalahan' . $e->getMassage());
        }
    }
}
