<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\DetailPinjam;
use App\Models\Kategori;
use App\Models\User;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class AdminController extends Controller
{
    //Menampilkan Dashboard Admin
    public function index() {
        $logs = LogAktivitas::with('user')->latest()->take(10)->get();
        return view('admin.dashboard', compact('logs'));
    }

    //CRUD user (Manajemen User Admin, Petugas, Peminjaman)
    public function indexUser(Request $request) {
        $search = $request->input('search');

        $users = User::when($search, function($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere("role", "like", "%{$search}%");
        })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view("admin.user.index", compact('users', 'search'));

    }

    public function createUser() {
        return view('admin.user.create');
    }

    //Menyimpan user baru ke Database 
    public function storeUser(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,petugas,peminjam',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'no_hp' => $request->no_hp,
        ]);

        return redirect()->route('admin.user.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function editUser($id) {
        $user = User::findOrFail($id);
        return view('admin.user.edit', compact('user'));
    }

    //Memperbarui data user
    public function updateUser(Request $request, $id) {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role' => 'required|in:admin,petugas,peminjam',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'no_hp' => $request->no_hp,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        $user->update($data);

        return redirect()->route('admin.user.index')->with('success','Data user berhasil di perbarui.');
    }

    //Menghapus user
    public function destroyUser($id) {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.user.index')->with('success', 'User berhasil dihapus.');
    }

// CRUD Kategori
    public function indexKategori(Request $request) {
       
    $search = $request->input('search');

        $kategoris = Kategori::when($search, function ($query, $search) {
            return $query->where('nama_kategori', 'like', "%{$search}%");  
        })
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('admin.kategori.index', compact('kategoris', 'search'));
        }

        // Menampilkan form tambah kategori
        public function createKategori() {
            return view('admin.kategori.create');
        }

        //Menyimpan Kategori baru
        public function storeKategori(Request $request) {
            $request->validate([
                'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori',
            ]);

            Kategori::create([
                'nama_kategori' => $request->nama_kategori,
            ]);

            return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
        }

        //Menampilkan form edit kategori
        public function editKategori($id) {
            $kategori = Kategori::findOrFail($id);
            return view('admin.kategori.edit', compact('kategori'));
        }

        //Memperbarui kategori
        public function updateKategori(Request $request, $id) {
            $kategori = Kategori::findOrFail($id);

            $request->validate([
                'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori,' . $id,
            ]);

            $kategori->update([
                'nama_kategori' => $request->nama_kategori,
            ]);

            return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil diperbarui.');
        }
        
        //Menghapus Kategori
        public function destroyKategori($id) {
            $kategori = Kategori::findOrFail($id);

            //Opsional: Mengcheck apakah kategori masih dipakai oleh alat
            if ($kategori->alat()->exists()) {
                return redirect()->route('admin.kategori.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh data alat');
            }

            $kategori->delete();

            return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil dihapus.');
        }
//CRUD Alat: Menampilkan daftar alat
    public function indexAlat(Request $request) {
        $search = $request->input('search');

        $alats = Alat::with('kategori')
            ->when($search, function ($query, $search) {
                return $query->where('nama_alat', 'like', "%{$search}%")
                    ->orWhere('status_kondisi', 'like', "%{$search}%")
                    ->orWhereHas('kategori', function ($q) use ($search) {
                        $q->where('nama_kategori', 'like', "%{search}%");
                    });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.alat.index', compact('alats', 'search'));
    }

    //Menampilkan form tambah alat
    public function createAlat() {
        $kategoris = Kategori::all();
        return view('admin.alat.create', compact('kategoris'));
    }

    //Menyimpan alat baru
    public function storeAlat(Request $request) {
        $request->validate([
            'nama_alat' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
            'stok'=> 'required|integer|min:8',
            'status_kondisi'=> 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();

        // Handle Upload Gambar jika ada 
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time() .'_'. $file->getClientOriginalName();
            $file->move(public_path('storage/alat'), $filename);
            $data['gambar'] = 'storage/alat/' . $filename;
    }
    Alat::create($data);

    return redirect()->route('admin.alat.index')->with('success','Data alat berhasil ditambahkan.');
    }

    //Form edit alat
    public function editAlat($id) {
        $alat = Alat::findOrFail($id);
        $kategoris = Kategori::all();
        return view('admin.alat.edit', compact('alat', 'kategori'));
    }

    //Memperbarui data alat
    public function updateAlat(Request $request, $id) {
        $alat = Alat::findOrFail($id);

        $request->validate([
            'nama_alat' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
            'stok'=> 'required|integer|min:8',
            'status_kondisi'=> 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->all();

        // Handle Upload Gambar jika ada
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($alat->gambar && file_exists(public_path($alat->gambar))) {
                unlink(public_path($alat->gambar));
                }

            $file = $request->file('gambar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/alat'), $filename);
            $data['gambar'] = 'storage/alat/' . $filename;
        }
        $alat->update($data);
        return redirect()->route('admin.alat.index')->with('success', 'Data alat berhasil diperbarui.');
    }

    //Menghapus data alat
    public function destroyAlat($id) {
        $alat = Alat::findOrFail($id);

        //Hapus file gambar jika ada
        if ($alat->gambar && file_exists(public_path($alat->gambar))) {
            unlink(public_path($alat->gambar));
        }
        $alat->delete();
        return redirect()->route('admin.alat.index')->with('success', 'Data alat berhasil dihapus.');
    }

// CRUD Peminjaman
    //Menampilkan daftar peminjaman
    public function indexPeminjaman(Request $request) {

        $search = $request->input('search');

        $peminjamans = Peminjaman::with(['user', 'detailPinjams.alat'])
            ->when($search, function ($query, $search) {
                return $query->where('status', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.peminjaman.index', compact('peminjamans'));
    }

    //Menampilkan form tambah peminjaman

    public function createPeminjaman() {
        $users = User::where('role', 'peminjam')->get();
        $alats = Alat::where('stok', '>', 0)->get();
        return view('admin.peminjaman.create', compact('users', 'alats'));
    }

    //Menyimpan data peminjaman baru
    public function storePeminjaman(Request $request) {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tgl_pinjam' => 'required|date',
            'tgl_kembali_plan' => 'required|date|after_or_equal:tgl_pinjam',
            'alat_id' => 'required|array',
            'alat_id.*' => 'exists:alat,id',
            'jumlah'=> 'required|array',
            'jumlah.*' => 'integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            //Buat transaksi utama peminjaman
            $peminjaman = Peminjaman::create([
                'user_id' => $request->user_id,
                'tgl_pinjam' => $request->tgl_pinjam,
                'tgl_kembali_plan' => $request->tgl_kembali_plan,
                'status' => 'diajukan',
            ]);

            //Simpan detail alat yang dipinjam
            foreach ($request->alat_id as $index => $alatId) {
                $jumlahPinjam = $request->jumlah[$index];

                $alat = Alat::findOrFail($alatId);

                //Validasi Stok
                if ($jumlahPinjam > $alat->stok) {
                    throw new \Exception("Stok alat {$alat->nama_alat} tidak mencukupi.");
                }

            DetailPinjam::create([
                'peminjaman_id' => $peminjaman->id,
                'alat_id' => $alatId,
                'jumlah' => $jumlahPinjam,
            ]);
            //Kurangi stok alat jika langsung disetujui/dipinjam atau dikurangi saat status berubah menjadi 'dipinjam'

            }
        DB::commit();
        return redirect()->route('admin.peminjaman.index')->with('success', 'Peminjaman berhasil diajukan.');
        } catch (\Exception $e) {
        DB::rollBack();
        return back()->withInput('error', $e->getMessage());
        }
    }

    //Memperbarui status peminjaman (misalnya disetujui, dikembalikan, dll.)
    public function updatePeminjamanStatus(Request $request, $id) {
        $peminjaman = Peminjaman::with('detailPinjams.alat')->findOrFail($id);

        $request->validate([
            'status' => 'required|in:diajukan,dipinjam,selesai,telat',
        ]);

        DB::beginTransaction();
        try {
            $statusLama = $peminjaman->status;
            $statusBaru = $request->status;

            //Logika pengelolaan stok otomatis
            if ($statusLama !== 'dipinjam' && $statusBaru === 'dipinjam') {
                // Kurangi stok saat status berubah menjadi 'dipinjam'
                foreach ($peminjaman->detailPinjams as $detail) {
                    $alat = $detail->alat;
                    if ($alat->stok < $detail->jumlah) {
                        throw new \Exception("Stok alat {$alat->nama_alat} tidak mencukupi.");
                    }
                    $alat->decrement('stok', $detail->jumlah);
                }
            } elseif ($statusLama === 'dipinjam' && ($statusBaru === 'selesai')) {
                // Kembalikan stok saat status berubah menjadi 'selesai' atau 'telat'
                foreach ($peminjaman->detailPinjams as $detail) {
                    $detail->alat->increment('stok', $detail->jumlah);
                }
            }

            $peminjaman->update(['status' => $statusBaru]);
            DB::commit();
            return redirect()->route('admin.peminjaman.index')->with('success', 'Status peminjaman berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    //Menghapus data peminjaman
    public function destroyPeminjaman($id) {
        $peminjaman = Peminjaman::findOrFail($id);

        //Jika status peminjaman adalah 'dipinjam', kembalikan stok alat terlebih dahulu sebelum dihapus
        if ($peminjaman->status === 'dipinjam') {
            foreach ($peminjaman->detailPinjams as $detail) {
                $detail->alat->increment('stok', $detail->jumlah);
            }
        }

        $peminjaman->delete();

        return redirect()->route('admin.peminjaman.index')->with('success', 'Peminjaman berhasil dihapus.');
    }
}