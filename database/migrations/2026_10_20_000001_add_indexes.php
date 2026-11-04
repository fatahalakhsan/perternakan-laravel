<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Indeks untuk kolom yang sering dipakai mencari atau memfilter (mempercepat query).
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ternak', fn (Blueprint $t) => $t->index('status', 'idx_ternak_status'));
        Schema::table('perawatan', fn (Blueprint $t) => $t->index(['status', 'jadwal'], 'idx_perawatan_status_jadwal'));
        Schema::table('pesanan', fn (Blueprint $t) => $t->index('tanggal', 'idx_pesanan_tanggal'));
        Schema::table('leads', fn (Blueprint $t) => $t->index('status', 'idx_leads_status'));
        Schema::table('transaksi_keuangan', fn (Blueprint $t) => $t->index('tanggal', 'idx_transaksi_tanggal'));
    }

    public function down(): void
    {
        Schema::table('ternak', fn (Blueprint $t) => $t->dropIndex('idx_ternak_status'));
        Schema::table('perawatan', fn (Blueprint $t) => $t->dropIndex('idx_perawatan_status_jadwal'));
        Schema::table('pesanan', fn (Blueprint $t) => $t->dropIndex('idx_pesanan_tanggal'));
        Schema::table('leads', fn (Blueprint $t) => $t->dropIndex('idx_leads_status'));
        Schema::table('transaksi_keuangan', fn (Blueprint $t) => $t->dropIndex('idx_transaksi_tanggal'));
    }
};
