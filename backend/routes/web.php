<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\PeminjamController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
});

//Admin
Route::middleware(['auth','role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    //CRUD User
    Route::get('/users', [AdminController::class, 'indexUser'])->name('user.index');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('user.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('user.store');
    Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('user.edit');
    Route::put('/users/{id}', [AdminController::class,'updateUser'])->name('user.update');
    Route::delete('/users/{id}', [AdminController::class,'destroyUser'])->name('user.destroy');

    //CRUD Kategori
    Route::get('/kategori', [AdminController::class,'indexKategori'])->name('kategori.index');
    Route::get('/kategori/create', [AdminController::class,'createKategori'])->name('kategori.create');
    Route::post('/kategori', [AdminController::class,'storeKategori'])->name('kategori.store');
    Route::get('/kategori/{id}/edit', [AdminController::class,'editKategori'])->name('kategori.edit');
    Route::put('/kategori/{id}', [AdminController::class,'updateKategori'])->name('kategori.update');
    Route::delete('/kategori/{id}', [AdminController::class, 'destroyKategori'])->name('kategori.destroy');

    //CRUD Alat
    Route::get('/alat', [AdminController::class,'indexAlat'])->name('alat.index');
    Route::get('/alat/create', [AdminController::class,'createAlat'])->name('alat.create');
    Route::post('/alat', [AdminController::class,'storeAlat'])->name('alat.store');
    Route::get('/alat/{id}/edit', [AdminController::class,'editAlat'])->name('alat.edit');
    Route::put('/alat/{id}', [AdminController::class,'updateAlat'])->name('alat.update');
    Route::delete('/alat/{id}', [AdminController::class,'destroyAlat'])->name('alat.destroy');

    //CRUD Peminjaman
    Route::get('/peminjaman', [AdminController::class,'indexPeminjaman'])->name('peminjaman.index');
    Route::get('/peminjaman/create', [AdminController::class,'createPeminjaman'])->name('peminjaman.create');
    Route::post('/peminjaman', [AdminController::class,'storePeminjaman'])->name('peminjaman.store');
    Route::put('/peminjaman/{id}/status', [AdminController::class,'updatePeminjamanStatus'])->name('peminjaman.updateStatus');
    Route::delete('/peminjaman/{id}', [AdminController::class,'destroyPeminjaman'])->name('peminjaman.destroy');

    // Riwayat Pengembalian
    Route::get('/pengembalian', [AdminController::class, 'indexPengembalian'])->name('pengembalian.index');

    // Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/excel', [LaporanController::class, 'excel'])->name('laporan.excel');
    Route::get('/laporan/pdf', [LaporanController::class, 'pdf'])->name('laporan.pdf');
});

//petugas
Route::middleware(['auth','role:petugas,admin'])->prefix('petugas')->name('petugas.')->group(function () {
    //Peminjaman & Persetujuan
    Route::get('/peminjaman', [PetugasController::class, 'indexPeminjaman'])->name('peminjaman.index');
    Route::post('/peminjaman/{id}/setujui', [PetugasController::class,'setujuiPeminjaman'])->name('peminjaman.setujui');
    Route::post('/peminjaman/{id}/tolak', [PetugasController::class,'tolakPeminjaman'])->name('peminjaman.tolak');

    //Pengembalian & Denda
    Route::post('/pengembalian/{id}', [PetugasController::class, 'prosesPengembalian'])->name('pengembalian.proses');
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/excel', [LaporanController::class, 'excel'])->name('laporan.excel');
    Route::get('/laporan/pdf', [LaporanController::class, 'pdf'])->name('laporan.pdf');
});

//Peminjam
Route::middleware(['auth','role:peminjam'])->prefix('peminjam')->name('peminjam.')->group(function () {
    //Katalog & Pengajuan
    Route::get('/katalog', [PeminjamController::class, 'katalogAlat'])->name('katalog');
    Route::post('/peminjaman/ajukan', [PeminjamController::class, 'ajukanPeminjaman'])->name('peminjaman.ajukan');
    Route::get('/riwayat', [PeminjamController::class, 'riwayatPeminjaman'])->name('riwayat');    
});

//Route Tamu
Route::middleware('guest')->group(function() {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

//Route Logout
Route::post('/logout', [AuthController::class,'logout'])->name('logout')->middleware('auth');

// Profil akun
Route::middleware('auth')->group(function () {
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
});
