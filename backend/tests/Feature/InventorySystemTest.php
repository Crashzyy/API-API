<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\DetailPinjam;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class InventorySystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_and_reach_dashboard(): void
    {
        $admin = $this->createUser('admin', 'admin@example.test');

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_peminjam_cannot_access_admin_dashboard(): void
    {
        $peminjam = $this->createUser('peminjam', 'peminjam@example.test');

        $response = $this->actingAs($peminjam)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    public function test_admin_can_create_category(): void
    {
        $admin = $this->createUser('admin', 'admin-category@example.test');

        $response = $this->actingAs($admin)->post(route('admin.kategori.store'), [
            'nama_kategori' => 'Jaringan',
        ]);

        $response->assertRedirect(route('admin.kategori.index'));
        $this->assertDatabaseHas('kategori', [
            'nama_kategori' => 'Jaringan',
        ]);
    }

    public function test_peminjam_can_submit_a_loan_request(): void
    {
        $peminjam = $this->createUser('peminjam', 'borrower@example.test');
        $kategori = Kategori::create(['nama_kategori' => 'Kamera']);
        $alat = Alat::create([
            'kategori_id' => $kategori->id,
            'nama_alat' => 'Kamera DSLR',
            'stok' => 5,
            'status_kondisi' => 'Baik',
            'deskripsi' => 'Kamera untuk praktik',
        ]);

        $response = $this->actingAs($peminjam)->post(
            route('peminjam.peminjaman.ajukan'),
            [
                'tgl_kembali_plan' => now()->addDays(3)->toDateString(),
                'alat_id' => [$alat->id],
                'jumlah' => [2],
            ]
        );

        $response->assertRedirect(route('peminjam.riwayat'));
        $this->assertDatabaseHas('peminjaman', [
            'user_id' => $peminjam->id,
            'status' => 'diajukan',
        ]);
        $this->assertDatabaseHas('detail_pinjam', [
            'alat_id' => $alat->id,
            'jumlah' => 2,
        ]);
    }

    public function test_petugas_can_approve_a_pending_loan_and_reduce_stock(): void
    {
        $petugas = $this->createUser('petugas', 'staff@example.test');
        [$peminjaman, $alat] = $this->createLoan('diajukan', 5, 2);

        $response = $this->actingAs($petugas)->post(
            route('petugas.peminjaman.setujui', $peminjaman->id)
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('peminjaman', [
            'id' => $peminjaman->id,
            'status' => 'dipinjam',
        ]);
        $this->assertDatabaseHas('alat', [
            'id' => $alat->id,
            'stok' => 3,
        ]);
    }

    public function test_petugas_can_record_a_return_and_restore_stock(): void
    {
        $petugas = $this->createUser('petugas', 'staff-return@example.test');
        [$peminjaman, $alat] = $this->createLoan('dipinjam', 3, 2);

        $response = $this->actingAs($petugas)->post(
            route('petugas.pengembalian.proses', $peminjaman->id),
            [
                'kondisi_kembali' => 'Lengkap dan baik',
                'denda' => 0,
            ]
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('peminjaman', [
            'id' => $peminjaman->id,
            'status' => 'selesai',
        ]);
        $this->assertDatabaseHas('alat', [
            'id' => $alat->id,
            'stok' => 5,
        ]);
        $this->assertDatabaseHas('pengembalian', [
            'peminjaman_id' => $peminjaman->id,
            'petugas_id' => $petugas->id,
            'denda' => 0,
        ]);
    }

    private function createUser(string $role, string $email): User
    {
        return User::create([
            'name' => ucfirst($role),
            'email' => $email,
            'password' => Hash::make('password'),
            'role' => $role,
        ]);
    }

    /**
     * @return array{0: Peminjaman, 1: Alat}
     */
    private function createLoan(string $status, int $stock, int $quantity): array
    {
        $borrower = $this->createUser(
            'peminjam',
            'borrower-' . uniqid('', true) . '@example.test'
        );
        $kategori = Kategori::create(['nama_kategori' => 'Elektronik']);
        $alat = Alat::create([
            'kategori_id' => $kategori->id,
            'nama_alat' => 'Router',
            'stok' => $stock,
            'status_kondisi' => 'Baik',
        ]);
        $peminjaman = Peminjaman::create([
            'user_id' => $borrower->id,
            'tgl_pinjam' => now()->toDateString(),
            'tgl_kembali_plan' => now()->addDays(3)->toDateString(),
            'status' => $status,
        ]);

        DetailPinjam::create([
            'peminjaman_id' => $peminjaman->id,
            'alat_id' => $alat->id,
            'jumlah' => $quantity,
        ]);

        return [$peminjaman, $alat];
    }
}
