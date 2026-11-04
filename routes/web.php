<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PerawatanController;
use App\Http\Controllers\TernakController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

// Hanya untuk tamu (belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Harus login. Proteksi CSRF otomatis aktif untuk semua form POST (@csrf).
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Halaman modul yang belum dibuat: placeholder, diganti controller pada tahap WBS masing-masing.
    $halaman = fn (string $judul, string $wbs) => fn () => view('halaman', ['judul' => $judul, 'wbs' => $wbs]);

    // ---- Data Ternak (WBS 4.0) ----
    // Ubah data: pemilik dan pekerja. Lihat saja: dokter hewan juga boleh.
    Route::middleware('role:pemilik,pekerja')->group(function () {
        Route::get('/ternak/create', [TernakController::class, 'create'])->name('ternak.create');
        Route::post('/ternak', [TernakController::class, 'store'])->name('ternak.store');
        Route::get('/ternak/{ternak}/edit', [TernakController::class, 'edit'])->name('ternak.edit');
        Route::put('/ternak/{ternak}', [TernakController::class, 'update'])->name('ternak.update');
        Route::delete('/ternak/{ternak}', [TernakController::class, 'destroy'])->name('ternak.destroy');
    });

    Route::middleware('role:pemilik,pekerja,dokter_hewan')->group(function () {
        Route::get('/ternak', [TernakController::class, 'index'])->name('ternak.index');
        Route::get('/ternak/{ternak}/riwayat', [PerawatanController::class, 'riwayat'])->name('ternak.riwayat');
    });

    // ---- Perawatan (WBS 5.0) ----
    // Menjadwalkan dan mencatat: pemilik, pekerja, dokter hewan. Menghapus jadwal: pemilik saja.
    // Catatan: rute /perawatan/create harus di atas /perawatan/{perawatan}.
    Route::middleware('role:pemilik,pekerja,dokter_hewan')->group(function () {
        Route::get('/perawatan', [PerawatanController::class, 'index'])->name('perawatan.index');
        Route::get('/perawatan/create', [PerawatanController::class, 'create'])->name('perawatan.create');
        Route::post('/perawatan', [PerawatanController::class, 'store'])->name('perawatan.store');
        Route::get('/perawatan/{perawatan}/edit', [PerawatanController::class, 'edit'])->name('perawatan.edit');
        Route::put('/perawatan/{perawatan}', [PerawatanController::class, 'update'])->name('perawatan.update');
        Route::patch('/perawatan/{perawatan}/selesai', [PerawatanController::class, 'selesai'])->name('perawatan.selesai');
    });
    Route::delete('/perawatan/{perawatan}', [PerawatanController::class, 'destroy'])
        ->middleware('role:pemilik')->name('perawatan.destroy');

    Route::middleware('role:pemilik,admin_keuangan')->group(function () use ($halaman) {
        Route::get('/pesanan', $halaman('Pesanan', '6.0'))->name('pesanan.index');
        Route::get('/leads', $halaman('Leads', '7.0'))->name('leads.index');
        Route::get('/iklan', $halaman('Iklan', '7.0'))->name('iklan.index');
        Route::get('/keuangan', $halaman('Keuangan', '8.0'))->name('keuangan.index');
        Route::get('/laporan', $halaman('Laporan', '9.0'))->name('laporan.index');
    });
});
