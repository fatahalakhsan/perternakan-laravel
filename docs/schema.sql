-- =====================================================================
-- schema.sql : DDL basis data Sistem Informasi Peternakan (10 tabel utama)
-- Setara dengan migrasi Laravel di database/migrations (bukti P06, langkah 1).
-- Cara pakai (opsional, tanpa Laravel): jalankan seluruh file ini di SQL-Front / phpMyAdmin.
-- Catatan: tabel bawaan Laravel (sessions, cache, jobs, personal_access_tokens) dibuat oleh migrasi, tidak ditulis di sini.
-- =====================================================================
CREATE DATABASE IF NOT EXISTS db_peternakan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_peternakan;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS transaksi_keuangan, detail_pesanan, pesanan, leads, kampanye_iklan, mitra, pelanggan, perawatan, ternak, users;
SET FOREIGN_KEY_CHECKS = 1;

-- Pengguna sistem (4 peran)
CREATE TABLE users (
  id                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name              VARCHAR(100) NOT NULL,
  email             VARCHAR(120) NOT NULL UNIQUE,
  email_verified_at TIMESTAMP NULL,
  password          VARCHAR(255) NOT NULL,
  role              ENUM('pemilik','pekerja','admin_keuangan','dokter_hewan') NOT NULL DEFAULT 'pekerja',
  remember_token    VARCHAR(100) NULL,
  created_at        TIMESTAMP NULL,
  updated_at        TIMESTAMP NULL
) ENGINE=InnoDB;

CREATE TABLE ternak (
  id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  kode          VARCHAR(10) NOT NULL UNIQUE,
  jenis         ENUM('Kambing','Sapi','Domba') NOT NULL,
  ras           VARCHAR(60) NULL,
  jenis_kelamin ENUM('Jantan','Betina') NOT NULL,
  tanggal_lahir DATE NULL,
  bobot_kg      DECIMAL(6,1) NOT NULL,
  status        ENUM('Tersedia','Dirawat','Terjual') NOT NULL DEFAULT 'Tersedia',
  harga_beli    INT UNSIGNED NULL,
  catatan       TEXT NULL,
  created_at    TIMESTAMP NULL,
  updated_at    TIMESTAMP NULL,
  CONSTRAINT chk_ternak_1 CHECK (bobot_kg > 0),
  INDEX idx_ternak_status (status)
) ENGINE=InnoDB;

CREATE TABLE perawatan (
  id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ternak_id       BIGINT UNSIGNED NOT NULL,
  user_id         BIGINT UNSIGNED NOT NULL,
  jenis           ENUM('Vaksin','Pakan','Obat','Penimbangan','Pemeriksaan') NOT NULL,
  jadwal          DATETIME NOT NULL,
  status          ENUM('Dijadwalkan','Selesai') NOT NULL DEFAULT 'Dijadwalkan',
  tanggal_selesai DATETIME NULL,
  biaya           INT UNSIGNED NULL,
  catatan         TEXT NULL,
  created_at      TIMESTAMP NULL,
  updated_at      TIMESTAMP NULL,
  CONSTRAINT fk_perawatan_ternak FOREIGN KEY (ternak_id) REFERENCES ternak(id) ON DELETE CASCADE,
  CONSTRAINT fk_perawatan_user   FOREIGN KEY (user_id)   REFERENCES users(id)  ON DELETE RESTRICT,
  INDEX idx_perawatan_status_jadwal (status, jadwal)
) ENGINE=InnoDB;

CREATE TABLE pelanggan (
  id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama       VARCHAR(100) NOT NULL,
  no_wa      VARCHAR(20) NOT NULL,
  alamat     TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
) ENGINE=InnoDB;

CREATE TABLE mitra (
  id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama       VARCHAR(100) NOT NULL,
  no_wa      VARCHAR(20) NULL,
  keterangan VARCHAR(255) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
) ENGINE=InnoDB;

CREATE TABLE kampanye_iklan (
  id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama_kampanye   VARCHAR(100) NOT NULL,
  tanggal_mulai   DATE NOT NULL,
  tanggal_selesai DATE NULL,
  biaya           INT UNSIGNED NOT NULL DEFAULT 0,
  tayangan        INT UNSIGNED NOT NULL DEFAULT 0,
  created_at      TIMESTAMP NULL,
  updated_at      TIMESTAMP NULL
) ENGINE=InnoDB;

CREATE TABLE leads (
  id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  kampanye_id   BIGINT UNSIGNED NULL,
  id_lead_meta  VARCHAR(50) NOT NULL UNIQUE,            -- mencegah lead ganda saat impor CSV
  nama          VARCHAR(100) NOT NULL,
  no_wa         VARCHAR(20) NOT NULL,
  tanggal_masuk DATE NOT NULL,
  status        ENUM('Baru','Dihubungi','Menjadi pesanan','Tidak jadi') NOT NULL DEFAULT 'Baru',
  catatan       TEXT NULL,
  created_at    TIMESTAMP NULL,
  updated_at    TIMESTAMP NULL,
  CONSTRAINT fk_leads_kampanye FOREIGN KEY (kampanye_id) REFERENCES kampanye_iklan(id) ON DELETE SET NULL,
  INDEX idx_leads_status (status)
) ENGINE=InnoDB;

CREATE TABLE pesanan (
  id               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  kode_pesanan     VARCHAR(20) NOT NULL UNIQUE,
  tanggal          DATE NOT NULL,
  pelanggan_id     BIGINT UNSIGNED NOT NULL,
  mitra_id         BIGINT UNSIGNED NULL,
  lead_id          BIGINT UNSIGNED NULL UNIQUE,         -- satu lead hanya jadi satu pesanan (1:1)
  user_id          BIGINT UNSIGNED NOT NULL,
  sumber           ENUM('Meta Ads','Mitra','Langsung','Lainnya') NOT NULL,   -- wajib diisi
  jenis_pesanan    ENUM('Qurban','Aqiqah','Biasa') NOT NULL DEFAULT 'Biasa',
  kemasan          ENUM('Hidup','Dipotong','Dikemas') NOT NULL DEFAULT 'Hidup',
  ongkir           INT UNSIGNED NOT NULL DEFAULT 0,
  potongan         INT UNSIGNED NOT NULL DEFAULT 0,
  alasan_potongan  VARCHAR(255) NULL,
  total_harga      INT UNSIGNED NOT NULL DEFAULT 0,
  status           ENUM('Menunggu pengiriman','Selesai','Dibatalkan') NOT NULL DEFAULT 'Menunggu pengiriman',
  created_at       TIMESTAMP NULL,
  updated_at       TIMESTAMP NULL,
  CONSTRAINT fk_pesanan_pelanggan FOREIGN KEY (pelanggan_id) REFERENCES pelanggan(id) ON DELETE RESTRICT,
  CONSTRAINT fk_pesanan_mitra     FOREIGN KEY (mitra_id)     REFERENCES mitra(id)     ON DELETE SET NULL,
  CONSTRAINT fk_pesanan_lead      FOREIGN KEY (lead_id)      REFERENCES leads(id)     ON DELETE SET NULL,
  CONSTRAINT fk_pesanan_user      FOREIGN KEY (user_id)      REFERENCES users(id)     ON DELETE RESTRICT,
  CONSTRAINT chk_pesanan_1 CHECK (ongkir >= 0),
  CONSTRAINT chk_pesanan_2 CHECK (potongan >= 0),
  CONSTRAINT chk_pesanan_3 CHECK (potongan = 0 OR alasan_potongan IS NOT NULL),  -- potongan wajib beralasan
  INDEX idx_pesanan_tanggal (tanggal)
) ENGINE=InnoDB;

CREATE TABLE detail_pesanan (
  id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  pesanan_id BIGINT UNSIGNED NOT NULL,
  ternak_id  BIGINT UNSIGNED NOT NULL UNIQUE,           -- satu ternak hanya bisa terjual sekali
  harga      INT UNSIGNED NOT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT fk_detail_pesanan FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE,
  CONSTRAINT fk_detail_ternak  FOREIGN KEY (ternak_id)  REFERENCES ternak(id)  ON DELETE RESTRICT,   -- ternak yang sudah dipesan tidak bisa dihapus
  CONSTRAINT chk_detail_1 CHECK (harga > 0)
) ENGINE=InnoDB;

CREATE TABLE transaksi_keuangan (
  id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id    BIGINT UNSIGNED NOT NULL,
  pesanan_id BIGINT UNSIGNED NULL,
  tanggal    DATE NOT NULL,
  jenis      ENUM('Pemasukan','Pengeluaran') NOT NULL,
  kategori   ENUM('Penjualan','Pakan','Obat','Operasional','Lainnya') NOT NULL,
  jumlah     INT UNSIGNED NOT NULL,
  keterangan VARCHAR(255) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT fk_transaksi_user    FOREIGN KEY (user_id)    REFERENCES users(id)   ON DELETE RESTRICT,
  CONSTRAINT fk_transaksi_pesanan FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE SET NULL,
  CONSTRAINT chk_transaksi_1 CHECK (jumlah > 0),
  INDEX idx_transaksi_tanggal (tanggal)
) ENGINE=InnoDB;
