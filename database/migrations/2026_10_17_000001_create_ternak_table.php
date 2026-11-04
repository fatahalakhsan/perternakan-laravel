<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ternak', function (Blueprint $t) {
            $t->id();
            $t->string('kode', 10)->unique();
            $t->enum('jenis', ['Kambing', 'Sapi', 'Domba']);
            $t->string('ras', 60)->nullable();
            $t->enum('jenis_kelamin', ['Jantan', 'Betina']);
            $t->date('tanggal_lahir')->nullable();
            $t->decimal('bobot_kg', 6, 1);
            $t->enum('status', ['Tersedia', 'Dirawat', 'Terjual'])->default('Tersedia');
            $t->unsignedInteger('harga_beli')->nullable();
            $t->text('catatan')->nullable();
            $t->timestamps();
        });
        DB::statement("ALTER TABLE `ternak` ADD CONSTRAINT chk_ternak_1 CHECK (bobot_kg > 0)");
    }

    public function down(): void
    {
        Schema::dropIfExists('ternak');
    }
};
