<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $t) {
            $t->id();
            $t->foreignId('kampanye_id')->nullable()->constrained('kampanye_iklan')->nullOnDelete();
            $t->string('id_lead_meta', 50)->unique();
            $t->string('nama', 100);
            $t->string('no_wa', 20);
            $t->date('tanggal_masuk');
            $t->enum('status', ['Baru', 'Dihubungi', 'Menjadi pesanan', 'Tidak jadi'])->default('Baru');
            $t->text('catatan')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
