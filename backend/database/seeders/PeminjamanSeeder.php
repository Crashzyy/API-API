<?php

namespace Database\Seeders;

use App\Models\Peminjaman;
use Illuminate\Database\Seeder;

class PeminjamanSeeder extends Seeder
{
    public function run(): void
    {
        $peminjaman = [
            [
                'user_id' => 3, // Rizal
                'tgl_pinjam' => '2026-06-02',
                'tgl_kembali_plan' => '2026-06-04',
                'status' => 'selesai',
            ],
            [
                'user_id' => 4, // Rian
                'tgl_pinjam' => '2026-06-03',
                'tgl_kembali_plan' => '2026-06-05',
                'status' => 'selesai',
            ],
            [
                'user_id' => 5, // Eka
                'tgl_pinjam' => '2026-06-05',
                'tgl_kembali_plan' => '2026-06-07',
                'status' => 'telat',
            ],
            [
                'user_id' => 3, 
                'tgl_pinjam' => '2026-06-08',
                'tgl_kembali_plan' => '2026-06-11',
                'status' => 'dipinjam',
            ],
            [
                'user_id' => 4, 
                'tgl_pinjam' => '2026-06-09',
                'tgl_kembali_plan' => '2026-06-11',
                'status' => 'diajukan',
            ],
        ];

        foreach ($peminjaman as $pinjam) {
            Peminjaman::create($pinjam);
        }
    }
}
