# Jobsheet 13 — Deployment & Dokumentasi (Malang Bike)

Sub-CPMK: Mendeploy dan mendokumentasikan aplikasi.

Ini adalah **snapshot akhir** proyek Malang Bike setelah 13 jobsheet (Jobsheet 1-12), siap didemokan pada UAS.

## Deskripsi Aplikasi

Malang Bike adalah aplikasi web sederhana untuk mengelola penyewaan sepeda: data sepeda, data pelanggan, serta transaksi penyewaan/pengembalian, dengan autentikasi petugas.

**Stack:** HTML5, CSS3, JavaScript, PHP native, PostgreSQL (PDO_PGSQL).

## Perubahan dari Jobsheet 12
- Tambah `includes/config.php` — memisahkan kredensial database dari kode sumber. Kredensial dibaca dari environment variable (`DATABASE_URL`, atau `DB_HOST`/`DB_PORT`/`DB_NAME`/`DB_USER`/`DB_PASS`) dengan nilai cadangan untuk pengembangan lokal.
- `includes/koneksi.php` kini membaca `config.php`. Dukungan `DATABASE_URL` (yang sebelumnya ditulis langsung di `koneksi.php` untuk Railway) dipertahankan dan dipindah ke `config.php`; dua cabang koneksi lama (Railway dan lokal) kini menjadi satu koneksi.
- Tambah `docs/manual-pengguna.md` — panduan penggunaan untuk pengguna akhir.
- `README.md` ditulis ulang sebagai snapshot akhir proyek (ERD final, matriks fitur per role, instalasi lengkap).
- `includes/footer.php` — label berubah menjadi "Jobsheet 13".
- `sql/01_malangbike.sql` — hanya komentar di baris atas yang disamakan dengan nama database yang dipakai aplikasi (`01_malangbike`).
- Seluruh logika CRUD Sepeda/Pelanggan, autentikasi, CSRF, dan modul Penyewaan **tidak berubah** dari Jobsheet 12.

## ERD Final

```
sepeda            pelanggan         users              penyewaan
------            ---------         ------             ---------
id (PK)           id (PK)           id (PK)             id (PK)
kode_sepeda (UQ)  nama              username (UQ)        sepeda_id (FK -> sepeda.id)
merek             alamat            password             pelanggan_id (FK -> pelanggan.id)
jenis             no_telepon        nama                 tanggal_sewa
harga_sewa        created_at                             tanggal_kembali
created_at                                               status
```

## Fitur per Role

| Fitur | Tamu (tanpa login) | Petugas (login) |
|---|---|---|
| Lihat Beranda & statistik | Ya | Ya |
| Lihat Daftar Sepeda | Ya | Ya |
| Lihat Daftar Pelanggan | Ya | Ya |
| Tambah/Edit/Hapus Sepeda | Tidak | Ya |
| Tambah/Edit/Hapus Pelanggan | Tidak | Ya |
| Sewa Baru | Tidak | Ya |
| Pengembalian | Tidak | Ya |
| Riwayat Penyewaan | Tidak | Ya |

## Instalasi & Menjalankan

1. **Salin folder ini** ke server (lokal atau hosting yang mendukung PHP + PostgreSQL).
2. **Buat database & impor skema** (urutan penting karena `penyewaan` mereferensikan `sepeda`/`pelanggan`):
   ```bash
   createdb 01_malangbike
   psql -d 01_malangbike -f sql/01_malangbike.sql
   psql -d 01_malangbike -f sql/02_penyewaan.sql
   ```
3. **Konfigurasi koneksi**: kredensial database dibaca dari `includes/config.php` dengan prioritas:
   - `DATABASE_URL` (mis. `postgres://user:pass@host:5432/namadb`) — kalau tersedia, dipakai lebih dulu. Layanan seperti Railway menyediakan variabel ini untuk database PostgreSQL-nya.
   - `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS` — kalau `DATABASE_URL` tidak ada.
   - Nilai cadangan lokal (`localhost`, `5432`, `01_malangbike`, `postgres`, `12345678`) — kalau tidak ada environment variable sama sekali.

   Contoh mengatur environment variable sebelum menjalankan server:
   ```bash
   DB_HOST=127.0.0.1 DB_NAME=01_malangbike DB_USER=produser DB_PASS=rahasia php -S localhost:8000
   ```
   Jangan mengubah nilai cadangan di `includes/config.php` menjadi kredensial produksi lalu meng-commit-nya ke repository publik.
4. **Jalankan** — path CSS/JS/link/redirect login dihitung relatif otomatis di `includes/header.php` & `includes/auth.php` berdasarkan kedalaman folder halaman, jadi tidak harus dijalankan dari root server:
   - **Opsi 1 — PHP built-in server**:
     ```bash
     php -S localhost:8000
     ```
   - **Opsi 2 — Laragon (Apache)**: lewat virtual host langsung ke folder `Jobsheet13/`, atau bersarang di bawah domain proyek — dua-duanya jalan.
5. Buka `http://localhost:8000/index.php` (atau URL Laragon yang dipakai). Akun bawaan dari `sql/01_malangbike.sql`: username `admin`, password `admin123` — atau registrasi akun petugas lewat `auth/register.php`.

## Struktur Folder

```
Jobsheet13/
├── index.php                  Beranda (statistik real-time dari DB)
├── includes/                  koneksi.php, config.php, header.php, footer.php, auth.php, csrf.php, helpers.php
├── assets/css, assets/js      styling & interaktivitas
├── sepeda/                    CRUD Sepeda
├── pelanggan/                 CRUD Pelanggan
├── auth/                      Register, Login, Logout
├── penyewaan/                 Sewa Baru, Pengembalian, Riwayat
├── sql/                       skema database (01-02, jalankan berurutan)
└── docs/                      wireframe.md, security-checklist.md, manual-pengguna.md
```

## Dokumen Pendukung
- [`docs/wireframe.md`](docs/wireframe.md) — rancangan UX
- [`docs/security-checklist.md`](docs/security-checklist.md) — audit keamanan (Jobsheet 11)
- [`docs/manual-pengguna.md`](docs/manual-pengguna.md) — panduan penggunaan aplikasi

## Catatan
- Perubahan koneksi di jobsheet ini perlu diuji ulang di dua lingkungan sebelum demo UAS: lokal (tanpa environment variable) dan hosting (dengan `DATABASE_URL`).
- Akun `admin` bawaan masih menyimpan password teks biasa (lihat temuan #6 di `docs/security-checklist.md`); ganti sebelum aplikasi dipakai di produksi.
- Untuk presentasi UAS, siapkan penjelasan alasan desain teknis: mengapa struktur tabel dan alur transaksi penyewaan dirancang seperti ini (lihat dokumentasi Jobsheet 12 untuk detail transaksi penyewaan).
