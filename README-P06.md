# Sistem Informasi Peternakan: Backend REST API & Database (Capstone P06)

Padepokan Geliat masa tua ku. Backend Laravel 12 + MySQL. Modul inti yang dikerjakan: **Data Ternak**.

## 1. Arsitektur berlapis

| Lapisan (slide P06) | Di proyek ini |
|---|---|
| Frontend / tampilan | Blade (`resources/views`), dan nanti halaman yang memanggil API (P7) |
| API (rute / endpoint) | `routes/api.php` + `app/Http/Controllers/Api/*` : terima permintaan, kembalikan JSON + kode status |
| Validasi input | `app/Http/Requests/TernakRequest.php` (di server) |
| Logika bisnis | `app/Models/Ternak.php` + `TernakApiController` (aturan: ternak yang sudah dipesan tidak boleh dihapus, status harus Terjual) |
| Database | MySQL `db_peternakan`, dibuat oleh migrasi (`database/migrations`) / `docs/schema.sql` |

Relasi inti (ERD ringkas): `users 1—N perawatan`, `ternak 1—N perawatan`, `pelanggan 1—N pesanan`, `mitra 1—N pesanan`, `leads 1—1 pesanan`, `pesanan 1—N detail_pesanan`, `ternak 1—1 detail_pesanan`, `kampanye_iklan 1—N leads`, `pesanan 1—N transaksi_keuangan`, `users 1—N pesanan / transaksi_keuangan`.

## 2. Pemasangan

Syarat: XAMPP (PHP 8.2+, Apache dan MySQL menyala), Composer, proyek Laravel 12 yang sudah punya migrasi 10 tabel (tahap 1 sampai 5).

1. **Pasang Sanctum (token API). Lakukan ini PERTAMA**, di folder proyek:

       php artisan install:api

   Jika ditanya "run migrations", jawab `yes` atau `no` (keduanya aman).
2. Salin isi paket ini (folder `bootstrap`, `routes`, `app`, `database`, `tests`, dan file `phpunit.xml`) ke folder proyek. Pilih **Replace**.
   Simpan juga `docs/` dan `README-P06.md` ke repo Anda.
3. Buat database tes di SQL-Front: jalankan `sql/create_db_test.sql`.
4. Isi ulang database utama (ini menghapus lalu membuat ulang tabel):

       php artisan optimize:clear
       php artisan migrate:fresh --seed

5. Jalankan server:

       php artisan serve

API aktif di `http://127.0.0.1:8000/api`.

## 3. Kontrak API

Semua permintaan dan respons berbentuk JSON. Kirim header `Accept: application/json`.
Kecuali `/api/login`, semua endpoint wajib header `Authorization: Bearer <token>`.

| Metode | URL | Fungsi | Peran | Sukses | Gagal |
|---|---|---|---|---|---|
| POST | `/api/login` | Masuk, dapat token (body: email, password) | publik | 200 | 400 input salah, 401 salah sandi, 429 terlalu sering |
| GET | `/api/me` | Data pengguna yang masuk | semua | 200 | 401 |
| POST | `/api/logout` | Cabut token | semua | 200 | 401 |
| GET | `/api/ternak` | Daftar ternak (filter `status`, `q`; `page`, `per_page` maks 50) | pemilik, pekerja, dokter hewan | 200 | 401, 403 |
| GET | `/api/ternak/{id}` | Detail satu ternak | pemilik, pekerja, dokter hewan | 200 | 401, 403, 404 |
| POST | `/api/ternak` | Tambah ternak | pemilik, pekerja | 201 | 400, 401, 403 |
| PUT | `/api/ternak/{id}` | Ubah ternak | pemilik, pekerja | 200 | 400, 401, 403, 404 |
| DELETE | `/api/ternak/{id}` | Hapus ternak | pemilik, pekerja | 204 | 401, 403, 404, 409 |

### Kode status

| Kode | Arti | Kapan dipakai |
|---|---|---|
| 200 / 201 / 204 | Berhasil / berhasil dibuat / berhasil tanpa isi | Baca, buat, hapus |
| 400 | Input salah | Kode bukan format `KB-030`, bobot 0, kode kembar, status tidak valid |
| 401 | Belum masuk | Token tidak ada atau tidak valid, salah sandi |
| 403 | Peran tidak boleh | Dokter hewan mencoba POST/PUT/DELETE |
| 404 | Tidak ditemukan | ID tidak ada |
| 409 | Konflik | Menghapus ternak yang sudah ada di pesanan |
| 429 | Terlalu banyak percobaan | Lebih dari 5 kali login per menit |
| 500 | Error server | Bug di kode backend |

### Contoh

Login:

    POST /api/login
    {"email": "pemilik@geliat.id", "password": "Ganti-Saya-123"}

    200 {"token": "1|abc...", "token_type": "Bearer", "user": {"id": 1, "name": "Pemilik Padepokan", "email": "pemilik@geliat.id", "role": "pemilik"}}

Tambah ternak:

    POST /api/ternak          (Authorization: Bearer 1|abc...)
    {"kode": "KB-050", "jenis": "Kambing", "ras": "Etawa", "jenis_kelamin": "Jantan",
     "tanggal_lahir": "2025-02-01", "bobot_kg": 31.5, "status": "Tersedia", "harga_beli": 2000000}

    201 {"data": {"id": 12, "kode": "KB-050", "jenis": "Kambing", "ras": "Etawa", "jenis_kelamin": "Jantan",
                  "tanggal_lahir": "2025-02-01", "umur": "8 bln", "bobot_kg": 31.5, "status": "Tersedia",
                  "harga_beli": 2000000, "catatan": null, "dibuat": "2026-10-20 09:00:00"}}

Input salah:

    400 {"message": "Input tidak valid.", "errors": {"kode": ["Format kode: dua huruf, strip, tiga angka (contoh KB-030)."], "bobot_kg": ["Bobot harus lebih dari 0."]}}

### Cara menguji
- **Postman:** Import `docs/postman_collection.json`, jalankan request nomor 1 (Login, token tersimpan otomatis), lalu nomor 2 sampai 10. Setiap request punya pengecekan status otomatis.
- **Browser:** tidak bisa untuk endpoint yang butuh token. Gunakan Postman atau tes otomatis di bawah.

## 4. Tes otomatis

    php artisan test

Hasil yang diharapkan: seluruh tes lolos (12 tes: login, token, daftar, detail, 404, tambah, input salah, kode kembar, hak akses, hapus 409, hapus 204, dan satu tes halaman utama).
Tes memakai database **`db_peternakan_test`** (bukan data asli), dan menolak berjalan jika database bukan berakhiran `_test`.

## 5. Definition of Done (checklist P06)

- [x] Sesuai kontrak API (tabel di atas)
- [x] Validasi input di server (`TernakRequest`)
- [x] Query berparameter: memakai Eloquent / query builder (prepared statement), tidak ada SQL yang disambung dari input
- [x] Ada tes otomatis (`tests/Feature/TernakApiTest.php`)
- [ ] Direview lewat pull request (lihat bagian 7)
- [x] README diperbarui (file ini)

Kesalahan umum yang dihindari: SQL disusun dari string input (tidak dilakukan); semua logika di satu fungsi (dipisah: Request, Controller, Resource, Model); selalu 200 (kode status sesuai); skema berubah tanpa memberi tahu tim (semua perubahan lewat migrasi); rahasia masuk repo (`.env` sudah ada di `.gitignore` bawaan Laravel).

## 6. Data contoh (minimal 5 baris per tabel utama)

| Tabel | Jumlah baris |
|---|---|
| ternak | 11 |
| pelanggan | 6 |
| kampanye_iklan | 5 |
| leads | 6 |
| pesanan | 6 |
| detail_pesanan | 6 |
| perawatan | 6 |
| transaksi_keuangan | 9 |
| mitra | 3 |
| users | 4 |

## 7. Alur Git dan pull request

    git checkout -b fitur/api-ternak
    git add .
    git commit -m "Tambah REST API ternak, validasi, dan tes"
    git push origin fitur/api-ternak

Buka GitHub, buat **Pull Request** dari `fitur/api-ternak` ke `main`, tulis hasil `php artisan test` di deskripsi, minta satu anggota tim mereview, lalu merge.

## 8. Pembagian tugas tim (isi nama)

| Anggota | Tugas | Bukti |
|---|---|---|
| ........ | Database: migrasi, seeder, schema.sql | `docs/schema.sql`, tabel terisi |
| ........ | Backend: endpoint dan validasi | kode di branch fitur, hasil Postman |
| ........ | Tes, dokumentasi, dan frontend awal (P7) | `php artisan test`, README |

## 9. Target Pertemuan 7

- [x] Minimal 3 endpoint berfungsi (GET all, GET by id, POST) , sudah ada 5 endpoint ternak
- [x] Database terhubung dan data contoh minimal 5 baris per tabel utama
- [ ] Minimal 1 halaman frontend yang memanggil API
- [ ] Semua anggota punya commit di GitHub

## 10. Catatan

- Frontend Blade yang sudah ada memakai sesi login, sedangkan API memakai token Bearer (Sanctum). Keduanya memakai database dan aturan hak akses yang sama.
- `422` bawaan Laravel diubah menjadi `400` agar sesuai kontrak kelas.
- Endpoint modul lain (perawatan, pesanan, leads, keuangan) menyusul sesuai WBS dengan pola yang sama.
