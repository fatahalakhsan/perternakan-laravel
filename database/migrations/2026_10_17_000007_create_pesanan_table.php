<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $t) {
            $t->id();
            $t->string('kode_pesanan', 20)->unique();
            $t->date('tanggal');
            $t->foreignId('pelanggan_id')->constrained('pelanggan')->restrictOnDelete();
            $t->foreignId('mitra_id')->nullable()->constrained('mitra')->nullOnDelete();
            $t->foreignId('lead_id')->nullable()->unique()->constrained('leads')->nullOnDelete();
            $t->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $t->enum('sumber', ['Meta Ads', 'Mitra', 'Langsung', 'Lainnya']);
            $t->enum('jenis_pesanan', ['Qurban', 'Aqiqah', 'Biasa'])->default('Biasa');
            $t->enum('kemasan', ['Hidup', 'Dipotong', 'Dikemas'])->default('Hidup');
            $t->unsignedInteger('ongkir')->default(0);
            $t->unsignedInteger('potongan')->default(0);
            $t->string('alasan_potongan', 255)->nullable();
            $t->unsignedInteger('total_harga')->default(0);
            $t->enum('status', ['Menunggu pengiriman', 'Selesai', 'Dibatalkan'])->default('Menunggu pengiriman');
            $t->timestamps();
        });
        DB::statement("ALTER TABLE `pesanan` ADD CONSTRAINT chk_pesanan_1 CHECK (ongkir >= 0)");
        DB::statement("ALTER TABLE `pesanan` ADD CONSTRAINT chk_pesanan_2 CHECK (potongan >= 0)");
        DB::statement("ALTER TABLE `pesanan` ADD CONSTRAINT chk_pesanan_3 CHECK (potongan = 0 OR alasan_potongan IS NOT NULL)");
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
