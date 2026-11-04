<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perawatan', function (Blueprint $t) {
            $t->id();
            $t->foreignId('ternak_id')->constrained('ternak')->cascadeOnDelete();
            $t->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $t->enum('jenis', ['Vaksin', 'Pakan', 'Obat', 'Penimbangan', 'Pemeriksaan']);
            $t->dateTime('jadwal');
            $t->enum('status', ['Dijadwalkan', 'Selesai'])->default('Dijadwalkan');
            $t->dateTime('tanggal_selesai')->nullable();
            $t->unsignedInteger('biaya')->nullable();
            $t->text('catatan')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perawatan');
    }
};
