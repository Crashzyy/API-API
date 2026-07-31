<?php

namespace Database\Seeders;

use App\Models\Pengembalian;
use Illuminate\Database\Seeder;

class PengembalianSeeder extends Seeder
{
    public function run(): void
    {
        $pengembalian = [
            [
                'peminjaman_id' => 1,
                'tgl_kembali' => '2026-06-04',
                'kondisi_kembali' => 'Lengkap dan berfungsi baik',
                'denda' => 0,
                'petugas_id' => 2,
            ],
            [
                'peminjaman_id' => 2,
                'tgl_kembali' => '2026-06-05',
                'kondisi_kembali' => 'Lengkap dan berfungsi baik',
                'denda' => 0,
                'petugas_id' => 2,
            ],
            [
                'peminjaman_id' => 3,
                'tgl_kembali' => '2026-06-07',
                'kondisi_kembali' => 'Lengkap, Casing sedikit tergores',
                'denda' => 30000, //Misal, Denda Perhari 10000
                'petugas_id' => 2,
            ],
        ];
        foreach ($pengembalian as $kembali) {
            Pengembalian::create($kembali);
        }
    }
}
