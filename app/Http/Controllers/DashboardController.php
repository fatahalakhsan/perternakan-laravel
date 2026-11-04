<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        // Ringkasan sementara untuk membuktikan koneksi database. Dashboard lengkap dibuat di WBS 9.0.
        return view('dashboard', [
            'ternak'   => DB::table('ternak')->count(),
            'tersedia' => DB::table('ternak')->where('status', 'Tersedia')->count(),
            'pesanan'  => DB::table('pesanan')->count(),
            'leads'    => DB::table('leads')->where('status', 'Baru')->count(),
        ]);
    }
}
