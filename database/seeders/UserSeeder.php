<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Sandi awal sama untuk semua akun contoh. WAJIB diganti sebelum dipakai sungguhan.
        $hash = Hash::make('Ganti-Saya-123');
        $now  = now();
        foreach ([
            ['Pemilik Padepokan', 'pemilik@geliat.id', 'pemilik'],
            ['Pekerja Kandang', 'pekerja@geliat.id', 'pekerja'],
            ['Admin Keuangan', 'keuangan@geliat.id', 'admin_keuangan'],
            ['Dokter Hewan', 'dokter@geliat.id', 'dokter_hewan'],
        ] as [$name, $email, $role]) {
            DB::table('users')->insert([
                'name' => $name, 'email' => $email, 'password' => $hash, 'role' => $role,
                'email_verified_at' => $now, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }
}
