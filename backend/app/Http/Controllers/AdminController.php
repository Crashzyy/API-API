<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\User;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;


class AdminController extends Controller
{
    //Menampilkan Dashboard Admin
    public function index() {
        $logs = LogAktivitas::with('user')->latest()->take(10)->get();
        return view('admin.dashboard', compact('logs'));
    }

    //CRUD alat: menampilkan daftar alat
    public function IndexAlat() {
        $alats = Alat::with('kategori')->get();
        return view('admin.alat.index', compact('alats'));
    }

    //Menyimpan alat baru
    public function storeAlat(Request $request) {
        $request->validate([
            'kategori_id' => 'required',
            'nama_alat' => 'required|string|max:255',
            'stok' => 'required|integer',
            'status_kondisi' => 'required|string',
        ]);

        Alat::create($request->All());

        //Catat Log Aktivitas
        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Menambahkan alat baru:' . $request->nama_alat
        ]);

        return redirect()->back()->with('success', 'Alat berhasil ditambahkan.');
    }

    //CRUD user (Manajemen User Admin, Petugas, Peminjaman)
    public function indexUser() {
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

    public function indexKategori(Request $request) {
       
    $search = $request->input('search');

        $kategoris = Kategori::when($search, function ($query, $search) {
            return $query->where('nama_kategori', 'like', "%($search)%");  
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
        public function storeKategori() {
            $request->validate([
                'nama_kategori' => 'required|string|max:255|unique:kategoris,nama_kategori',
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
                'nama_kategori' => 'required|string|max:255|unique:kategoris,nama_kategori,' . $id,
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
            if ($kategori->$alats()->count() > 0) {
                return redirect()->route('admin.kategori.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh data alat');
            }

            $kategori->delete();

            return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil dihapus.');
        }
}
