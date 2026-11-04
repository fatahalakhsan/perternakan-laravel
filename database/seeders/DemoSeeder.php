<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/** Data contoh: minimal 5 baris untuk setiap tabel utama (syarat Capstone P06). */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $d   = fn (int $hari) => now()->subDays($hari)->toDateString();
        // Semua baris dalam satu insert HARUS punya kolom yang sama persis.
        $ins = fn (string $t, array $rows) => DB::table($t)->insert(
            array_map(fn ($r) => $r + ['created_at' => $now, 'updated_at' => $now], $rows)
        );

        // ID 1..11 mengikuti urutan di bawah
        $ins('ternak', [
            ['kode' => 'KB-014', 'jenis' => 'Kambing', 'ras' => 'Jawarandu', 'jenis_kelamin' => 'Jantan', 'bobot_kg' => 32, 'status' => 'Terjual'],
            ['kode' => 'KB-021', 'jenis' => 'Kambing', 'ras' => 'Jawarandu', 'jenis_kelamin' => 'Betina', 'bobot_kg' => 28, 'status' => 'Dirawat'],
            ['kode' => 'KB-026', 'jenis' => 'Kambing', 'ras' => 'Jawarandu', 'jenis_kelamin' => 'Betina', 'bobot_kg' => 30, 'status' => 'Tersedia'],
            ['kode' => 'SP-003', 'jenis' => 'Sapi', 'ras' => 'Limousin', 'jenis_kelamin' => 'Jantan', 'bobot_kg' => 310, 'status' => 'Dirawat'],
            ['kode' => 'KB-009', 'jenis' => 'Kambing', 'ras' => 'Etawa', 'jenis_kelamin' => 'Jantan', 'bobot_kg' => 35, 'status' => 'Terjual'],
            ['kode' => 'KB-002', 'jenis' => 'Kambing', 'ras' => 'Etawa', 'jenis_kelamin' => 'Jantan', 'bobot_kg' => 34, 'status' => 'Terjual'],
            ['kode' => 'DM-001', 'jenis' => 'Domba', 'ras' => 'Garut', 'jenis_kelamin' => 'Jantan', 'bobot_kg' => 38, 'status' => 'Tersedia'],
            ['kode' => 'KB-031', 'jenis' => 'Kambing', 'ras' => 'Boer', 'jenis_kelamin' => 'Jantan', 'bobot_kg' => 40, 'status' => 'Terjual'],
            ['kode' => 'SP-007', 'jenis' => 'Sapi', 'ras' => 'PO', 'jenis_kelamin' => 'Betina', 'bobot_kg' => 280, 'status' => 'Tersedia'],
            ['kode' => 'DM-002', 'jenis' => 'Domba', 'ras' => 'Garut', 'jenis_kelamin' => 'Betina', 'bobot_kg' => 33, 'status' => 'Terjual'],
            ['kode' => 'KB-040', 'jenis' => 'Kambing', 'ras' => 'Kacang', 'jenis_kelamin' => 'Jantan', 'bobot_kg' => 27, 'status' => 'Terjual'],
        ]);

        $ins('pelanggan', [
            ['nama' => 'Pak Hendra', 'no_wa' => '0812-0000-0001'],
            ['nama' => 'Bu Sari', 'no_wa' => '0812-0000-0002'],
            ['nama' => 'Pak Wawan', 'no_wa' => '0812-0000-0003'],
            ['nama' => 'Pak Budi', 'no_wa' => '0812-0000-0004'],
            ['nama' => 'Bu Citra', 'no_wa' => '0812-0000-0005'],
            ['nama' => 'Pak Dewa', 'no_wa' => '0812-0000-0006'],
        ]);

        $ins('mitra', [
            ['nama' => 'Mitra Aqiqah Tangsel', 'no_wa' => '0812-0000-0099', 'keterangan' => 'Perantara penjualan aqiqah'],
            ['nama' => 'Mitra Kurban Depok', 'no_wa' => '0812-0000-0098', 'keterangan' => 'Perantara hewan kurban'],
            ['nama' => 'Mitra Katering Bintaro', 'no_wa' => '0812-0000-0097', 'keterangan' => 'Pemasok katering'],
        ]);

        $ins('kampanye_iklan', [
            ['nama_kampanye' => 'Kambing Aqiqah • Tangsel', 'tanggal_mulai' => $d(30), 'biaya' => 1450000, 'tayangan' => 48200],
            ['nama_kampanye' => 'Hewan Kurban Dini', 'tanggal_mulai' => $d(30), 'biaya' => 2100000, 'tayangan' => 71500],
            ['nama_kampanye' => 'Sapi Limousin • Jabodetabek', 'tanggal_mulai' => $d(30), 'biaya' => 900000, 'tayangan' => 22800],
            ['nama_kampanye' => 'Domba Garut Premium', 'tanggal_mulai' => $d(20), 'biaya' => 750000, 'tayangan' => 18400],
            ['nama_kampanye' => 'Promo Akhir Bulan', 'tanggal_mulai' => $d(10), 'biaya' => 600000, 'tayangan' => 15300],
        ]);

        $ins('leads', [
            ['kampanye_id' => 1, 'id_lead_meta' => 'l:1001', 'nama' => 'Rina Putri', 'no_wa' => '0812-3456-7890', 'tanggal_masuk' => $d(0), 'status' => 'Baru'],
            ['kampanye_id' => 2, 'id_lead_meta' => 'l:1002', 'nama' => 'Dedi Kurnia', 'no_wa' => '0857-1122-3344', 'tanggal_masuk' => $d(0), 'status' => 'Baru'],
            ['kampanye_id' => 1, 'id_lead_meta' => 'l:1003', 'nama' => 'Agus Salim', 'no_wa' => '0813-9988-7766', 'tanggal_masuk' => $d(1), 'status' => 'Dihubungi'],
            ['kampanye_id' => 2, 'id_lead_meta' => 'l:1004', 'nama' => 'Maya Lestari', 'no_wa' => '0821-4455-6677', 'tanggal_masuk' => $d(2), 'status' => 'Dihubungi'],
            ['kampanye_id' => 3, 'id_lead_meta' => 'l:1005', 'nama' => 'Fajar Nugroho', 'no_wa' => '0878-1111-2222', 'tanggal_masuk' => $d(3), 'status' => 'Tidak jadi'],
            ['kampanye_id' => 4, 'id_lead_meta' => 'l:1006', 'nama' => 'Gita Permata', 'no_wa' => '0896-3333-4444', 'tanggal_masuk' => $d(4), 'status' => 'Baru'],
        ]);

        // 6 pesanan: setiap pesanan membeli 1 ternak berstatus Terjual (ternak id 6,1,5,8,10,11)
        $kode = [6, 1, 5, 8, 10, 11];
        $pelanggan = [3, 2, 1, 4, 5, 6];
        $mitra = [null, null, 1, null, null, null];
        $sumber = ['Meta Ads', 'Langsung', 'Mitra', 'Meta Ads', 'Langsung', 'Lainnya'];
        $jenis = ['Biasa', 'Aqiqah', 'Qurban', 'Qurban', 'Biasa', 'Aqiqah'];
        $harga = [2800000, 3000000, 3200000, 3500000, 3100000, 2900000];
        $ongkir = [100000, 0, 150000, 200000, 0, 50000];
        $potongan = [0, 0, 0, 100000, 0, 0];
        $hari = [20, 15, 12, 8, 4, 2];
        $status = ['Selesai', 'Selesai', 'Selesai', 'Menunggu pengiriman', 'Menunggu pengiriman', 'Menunggu pengiriman'];

        $pesanan = [];
        $detail = [];
        $transaksi = [];
        foreach ($kode as $i => $ternakId) {
            $total = $harga[$i] + $ongkir[$i] - $potongan[$i];
            $nomor = 'PS-'.str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT);
            $pesanan[] = [
                'kode_pesanan' => $nomor, 'tanggal' => $d($hari[$i]), 'pelanggan_id' => $pelanggan[$i],
                'mitra_id' => $mitra[$i], 'lead_id' => null, 'user_id' => 1, 'sumber' => $sumber[$i],
                'jenis_pesanan' => $jenis[$i], 'kemasan' => 'Hidup', 'ongkir' => $ongkir[$i], 'potongan' => $potongan[$i],
                'alasan_potongan' => $potongan[$i] > 0 ? 'Pelanggan lama' : null,
                'total_harga' => $total, 'status' => $status[$i],
            ];
            $detail[] = ['pesanan_id' => $i + 1, 'ternak_id' => $ternakId, 'harga' => $harga[$i]];
            $transaksi[] = ['user_id' => 3, 'pesanan_id' => $i + 1, 'tanggal' => $d($hari[$i]), 'jenis' => 'Pemasukan', 'kategori' => 'Penjualan', 'jumlah' => $total, 'keterangan' => $nomor];
        }
        $ins('pesanan', $pesanan);
        $ins('detail_pesanan', $detail);

        $transaksi[] = ['user_id' => 3, 'pesanan_id' => null, 'tanggal' => $d(14), 'jenis' => 'Pengeluaran', 'kategori' => 'Pakan', 'jumlah' => 850000, 'keterangan' => 'Pakan hijauan mingguan'];
        $transaksi[] = ['user_id' => 3, 'pesanan_id' => null, 'tanggal' => $d(7), 'jenis' => 'Pengeluaran', 'kategori' => 'Obat', 'jumlah' => 220000, 'keterangan' => 'Vitamin dan obat cacing'];
        $transaksi[] = ['user_id' => 3, 'pesanan_id' => null, 'tanggal' => $d(3), 'jenis' => 'Pengeluaran', 'kategori' => 'Operasional', 'jumlah' => 400000, 'keterangan' => 'Listrik dan air kandang'];
        $ins('transaksi_keuangan', $transaksi);

        $ins('perawatan', [
            ['ternak_id' => 2, 'user_id' => 2, 'jenis' => 'Obat', 'jadwal' => now()->subDay()->setTime(8, 0), 'status' => 'Dijadwalkan', 'tanggal_selesai' => null, 'biaya' => null, 'catatan' => 'Vitamin'],
            ['ternak_id' => 3, 'user_id' => 2, 'jenis' => 'Penimbangan', 'jadwal' => now()->setTime(9, 0), 'status' => 'Dijadwalkan', 'tanggal_selesai' => null, 'biaya' => null, 'catatan' => null],
            ['ternak_id' => 4, 'user_id' => 4, 'jenis' => 'Pemeriksaan', 'jadwal' => now()->setTime(11, 0), 'status' => 'Dijadwalkan', 'tanggal_selesai' => null, 'biaya' => null, 'catatan' => null],
            ['ternak_id' => 7, 'user_id' => 4, 'jenis' => 'Vaksin', 'jadwal' => now()->addDays(3)->setTime(9, 0), 'status' => 'Dijadwalkan', 'tanggal_selesai' => null, 'biaya' => null, 'catatan' => null],
            ['ternak_id' => 9, 'user_id' => 2, 'jenis' => 'Pakan', 'jadwal' => now()->subDays(5)->setTime(7, 0), 'status' => 'Selesai', 'tanggal_selesai' => now()->subDays(5)->setTime(7, 30), 'biaya' => 150000, 'catatan' => 'Pakan fermentasi'],
            ['ternak_id' => 2, 'user_id' => 4, 'jenis' => 'Pemeriksaan', 'jadwal' => now()->subDays(10)->setTime(10, 0), 'status' => 'Selesai', 'tanggal_selesai' => now()->subDays(10)->setTime(10, 45), 'biaya' => 75000, 'catatan' => 'Kondisi sehat'],
        ]);
    }
}
