<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_pesanan', function (Blueprint $t) {
            $t->id();
            $t->foreignId('pesanan_id')->constrained('pesanan')->cascadeOnDelete();
            $t->foreignId('ternak_id')->unique()->constrained('ternak')->restrictOnDelete();
            $t->unsignedInteger('harga');
            $t->timestamps();
        });
        DB::statement("ALTER TABLE `detail_pesanan` ADD CONSTRAINT chk_detail_pesanan_1 CHECK (harga > 0)");
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pesanan');
    }
};
