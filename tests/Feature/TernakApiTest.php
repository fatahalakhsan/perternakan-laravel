<?php

namespace Tests\Feature;

use App\Models\Ternak;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TernakApiTest extends TestCase
{
    use RefreshDatabase;

    /** Pengaman: tes menghapus semua tabel, jadi hanya boleh jalan di database berakhiran _test. */
    protected function beforeRefreshingDatabase(): void
    {
        if (! str_ends_with((string) DB::connection()->getDatabaseName(), '_test')) {
            throw new \RuntimeException('Tes harus memakai database berakhiran _test. Periksa phpunit.xml.');
        }
    }

    private function masuk(string $role = 'pemilik'): User
    {
        $user = User::create([
            'name' => 'Penguji', 'email' => uniqid('u').'@tes.id', 'password' => 'rahasia123', 'role' => $role,
        ]);
        Sanctum::actingAs($user);

        return $user;
    }

    private function ternak(string $kode = 'KB-001', string $status = 'Tersedia'): Ternak
    {
        return Ternak::create([
            'kode' => $kode, 'jenis' => 'Kambing', 'jenis_kelamin' => 'Jantan', 'bobot_kg' => 30, 'status' => $status,
        ]);
    }

    private function payload(array $ubah = []): array
    {
        return array_merge([
            'kode' => 'KB-100', 'jenis' => 'Kambing', 'ras' => 'Etawa', 'jenis_kelamin' => 'Jantan',
            'tanggal_lahir' => '2025-01-10', 'bobot_kg' => 31.5, 'status' => 'Tersedia',
            'harga_beli' => 2000000, 'catatan' => null,
        ], $ubah);
    }

    // ---- Login ----
    public function test_login_berhasil_mengembalikan_token(): void
    {
        User::create(['name' => 'Budi', 'email' => 'budi@tes.id', 'password' => 'rahasia123', 'role' => 'pemilik']);

        $this->postJson('/api/login', ['email' => 'budi@tes.id', 'password' => 'rahasia123'])
            ->assertOk()
            ->assertJsonStructure(['token', 'token_type', 'user' => ['id', 'name', 'email', 'role']]);
    }

    public function test_login_salah_sandi_ditolak_401(): void
    {
        User::create(['name' => 'Budi', 'email' => 'budi@tes.id', 'password' => 'rahasia123', 'role' => 'pemilik']);

        $this->postJson('/api/login', ['email' => 'budi@tes.id', 'password' => 'salah'])->assertUnauthorized();
    }

    // ---- GET ----
    public function test_tanpa_token_ditolak_401(): void
    {
        $this->getJson('/api/ternak')->assertUnauthorized();
    }

    public function test_daftar_ternak_mengembalikan_200_dan_meta(): void
    {
        $this->masuk();
        $this->ternak('KB-001');
        $this->ternak('KB-002');
        $this->ternak('KB-003');

        $this->getJson('/api/ternak')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('meta.total', 3);
    }

    public function test_detail_ternak_200_dan_id_tidak_ada_404(): void
    {
        $this->masuk();
        $t = $this->ternak('KB-001');

        $this->getJson("/api/ternak/{$t->id}")->assertOk()->assertJsonPath('data.kode', 'KB-001');
        $this->getJson('/api/ternak/999999')->assertNotFound();
    }

    // ---- POST ----
    public function test_tambah_ternak_valid_mengembalikan_201(): void
    {
        $this->masuk('pekerja');

        $this->postJson('/api/ternak', $this->payload())
            ->assertCreated()
            ->assertJsonPath('data.kode', 'KB-100');

        $this->assertDatabaseHas('ternak', ['kode' => 'KB-100', 'jenis' => 'Kambing']);
    }

    public function test_tambah_ternak_input_salah_mengembalikan_400(): void
    {
        $this->masuk();

        $this->postJson('/api/ternak', $this->payload(['kode' => 'abc', 'bobot_kg' => 0]))
            ->assertStatus(400)
            ->assertJsonValidationErrorFor('kode', 'errors');
        $this->assertDatabaseCount('ternak', 0);
    }

    public function test_kode_kembar_ditolak_400(): void
    {
        $this->masuk();
        $this->ternak('KB-100');

        $this->postJson('/api/ternak', $this->payload())->assertStatus(400);
    }

    // ---- Hak akses ----
    public function test_dokter_hewan_boleh_melihat_tetapi_tidak_boleh_menambah(): void
    {
        $this->masuk('dokter_hewan');

        $this->getJson('/api/ternak')->assertOk();
        $this->postJson('/api/ternak', $this->payload())->assertForbidden();
    }

    // ---- DELETE ----
    public function test_hapus_ternak_yang_sudah_ada_di_pesanan_ditolak_409(): void
    {
        $user = $this->masuk();
        $t = $this->ternak('KB-001', 'Terjual');
        $now = now();
        $pelanggan = DB::table('pelanggan')->insertGetId(['nama' => 'A', 'no_wa' => '0811', 'created_at' => $now, 'updated_at' => $now]);
        $pesanan = DB::table('pesanan')->insertGetId([
            'kode_pesanan' => 'PS-T001', 'tanggal' => $now->toDateString(), 'pelanggan_id' => $pelanggan,
            'user_id' => $user->id, 'sumber' => 'Langsung', 'created_at' => $now, 'updated_at' => $now,
        ]);
        DB::table('detail_pesanan')->insert(['pesanan_id' => $pesanan, 'ternak_id' => $t->id, 'harga' => 1000000, 'created_at' => $now, 'updated_at' => $now]);

        $this->deleteJson("/api/ternak/{$t->id}")->assertStatus(409);
        $this->assertDatabaseHas('ternak', ['id' => $t->id]);
    }

    public function test_hapus_ternak_bebas_berhasil_204(): void
    {
        $this->masuk();
        $t = $this->ternak('KB-005');

        $this->deleteJson("/api/ternak/{$t->id}")->assertNoContent();
        $this->assertDatabaseMissing('ternak', ['id' => $t->id]);
    }
}
