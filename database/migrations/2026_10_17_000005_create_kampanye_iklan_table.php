<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kampanye_iklan', function (Blueprint $t) {
            $t->id();
            $t->string('nama_kampanye', 100);
            $t->date('tanggal_mulai');
            $t->date('tanggal_selesai')->nullable();
            $t->unsignedInteger('biaya')->default(0);
            $t->unsignedInteger('tayangan')->default(0);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kampanye_iklan');
    }
};
