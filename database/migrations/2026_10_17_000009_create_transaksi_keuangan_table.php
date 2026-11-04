<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_keuangan', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $t->foreignId('pesanan_id')->nullable()->constrained('pesanan')->nullOnDelete();
            $t->date('tanggal');
            $t->enum('jenis', ['Pemasukan', 'Pengeluaran']);
            $t->enum('kategori', ['Penjualan', 'Pakan', 'Obat', 'Operasional', 'Lainnya']);
            $t->unsignedInteger('jumlah');
            $t->string('keterangan', 255)->nullable();
            $t->timestamps();
        });
        DB::statement("ALTER TABLE `transaksi_keuangan` ADD CONSTRAINT chk_transaksi_keuangan_1 CHECK (jumlah > 0)");
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_keuangan');
    }
};
