# 4. Rangkuman & Latihan Lanjutan

## 4.1 Rangkuman Keseluruhan Jobsheet 13

| Bagian | Konsep yang Dipelajari |
|---|---|
| [Konsep Dasar Deployment](01-konsep-dasar-deployment-config.md) | Apa itu deployment, kenapa kredensial harus dipisah dari kode |
| [`config.php` & Environment Variable](02-config-dan-koneksi.md) | `getenv()`, `DATABASE_URL`, `parse_url()`, operator Elvis `?:`, `require` untuk mengambil nilai kembalian |
| [Dokumentasi Proyek Final](03-dokumentasi-proyek-final.md) | ERD, matriks fitur per role, manual pengguna vs dokumentasi developer |

## 4.2 Konsep Inti yang Perlu Diingat

1. **Kredensial produksi tidak boleh menempel di kode sumber** — environment variable memisahkan **konfigurasi** dari **kode**, memungkinkan kode yang sama dijalankan di lingkungan berbeda tanpa diedit ([bab 1](01-konsep-dasar-deployment-config.md)).
2. **Satu logika koneksi, banyak sumber konfigurasi** — `config.php` memilih sumber nilai (`DATABASE_URL`, lalu `DB_*`, lalu cadangan), `koneksi.php` cukup memakainya ([bab 2 §2.3](02-config-dan-koneksi.md#23-tiga-sumber-nilai-dengan-urutan-prioritas)).
3. **Operator Elvis (`?:`) berbeda dari null coalescing (`??`)** — `?:` memeriksa nilai "falsy" (termasuk `false` yang dikembalikan `getenv()`), `??` hanya memeriksa `null` ([bab 2 §2.4](02-config-dan-koneksi.md#24-membedah-satu-baris)).
4. **`require`/`include` bisa dipakai untuk mengambil nilai kembalian**, tidak melulu untuk efek samping seperti mencetak HTML ([bab 2 §2.5](02-config-dan-koneksi.md#25-cara-memakainya-configphp--pdo-di-koneksiphp)).
5. **Dokumentasi punya banyak bentuk untuk pembaca berbeda** — README untuk gambaran teknis, manual pengguna untuk pemakai akhir, dokumentasi jobsheet untuk pembelajar kode ([bab 3 §3.5](03-dokumentasi-proyek-final.md#35-docsmanual-penggunamd-dokumentasi-untuk-pengguna-akhir-bukan-developer)).

## 4.3 Melihat Kembali: Perjalanan 13 Jobsheet

| Jobsheet | Yang Ditambahkan |
|---|---|
| 01 | HTML5 semantic — kerangka halaman statis |
| 02 | CSS3 — Flexbox, Grid, styling |
| 03 | Responsive design — media query, hamburger menu |
| 04 | Rancangan UI/UX — wireframe, user flow, aktor |
| 05 | JavaScript DOM & Event — interaktivitas di browser |
| 06 | Fetch API & JSON — data dinamis dari file |
| 07 | PHP dasar — server-side, `$_SESSION`, form handling |
| 08 | PostgreSQL — data yang benar-benar persisten |
| 09 | CRUD penuh — Update, Delete, pagination |
| 10 | Autentikasi — Login, Register, otorisasi |
| 11 | Keamanan — XSS, CSRF, session fixation |
| 12 | Integrasi Penyewaan — transaction, relasi tabel |
| **13** | **Deployment & dokumentasi — siap dipakai orang lain** |

Setiap jobsheet **membangun di atas** fondasi yang sudah ada, jarang menulis ulang dari nol. Tag semantic dari Jobsheet 1 masih dipakai sampai Jobsheet 13, dan validasi server-side dari Jobsheet 7 masih relevan sampai form Sewa Baru di Jobsheet 12.

## 4.4 Cara Mencoba Sendiri

1. Ikuti seluruh langkah **Instalasi & Menjalankan** di [README.md](../README.md) dari awal, seolah-olah ini pertama kalinya kamu melihat proyek ini.
2. Jalankan server **dengan** environment variable kustom ([bab 2 §2.6](02-config-dan-koneksi.md#26-cara-mengatur-environment-variable-saat-deployment)) memakai nama database yang **berbeda** dari `01_malangbike`: buat database baru dengan nama lain, jalankan kedua skema SQL ke sana, lalu `DB_NAME=nama_lain php -S localhost:8000`. Buktikan aplikasi tetap berfungsi tanpa mengedit kode.
3. Uji jalur `DATABASE_URL`: `DATABASE_URL="postgres://postgres:12345678@localhost:5432/01_malangbike" php -S localhost:8000`. Aplikasi harus tetap terhubung ke database yang sama.
4. Buka [`docs/manual-pengguna.md`](../docs/manual-pengguna.md) dan ikuti setiap langkahnya **sebagai pengguna**, bukan sebagai developer — apakah instruksinya cukup jelas tanpa membaca kode?
5. Kalau PHP ada di komputermu, jalankan linter sintaks pada beberapa file:
   ```bash
   php -l includes/config.php
   php -l includes/koneksi.php
   ```
   Output "No syntax errors detected" adalah cara cepat memeriksa kesalahan penulisan tanpa menjalankan aplikasinya.

## 4.5 Ide Latihan Tambahan (Opsional)

1. **Tambah `.env.example`** — buat file berisi daftar nama environment variable yang dibutuhkan (`DATABASE_URL`, `DB_HOST`, `DB_PORT`, dst.) **tanpa** nilai sungguhan, sebagai template untuk siapa pun yang men-deploy aplikasi ini.
2. **Jangan tampilkan detail error koneksi ke pengunjung** — `die("Koneksi database gagal: " . $e->getMessage())` bisa membocorkan nama host atau database. Ganti dengan pesan umum dan catat detailnya lewat `error_log()`.
3. **Hash password akun `admin`** — selesaikan temuan #6 di `docs/security-checklist.md` sebelum aplikasi dipakai di produksi.
4. **Lengkapi validasi bisnis yang masih tertunda** — aturan "pelanggan yang menyewa lebih dari 7 hari dan belum mengembalikan tidak boleh menyewa lagi" (lihat dokumentasi Jobsheet 12) menggabungkan hampir semua konsep: query SQL, transaction, validasi, flash message.
5. **Sisipkan screenshot sungguhan** ke `manual-pengguna.md` pada setiap langkah bertanda `[Screenshot]`.
6. **Tulis "lessons learned"-mu sendiri** — ringkasan satu halaman tentang konsep pemrograman web yang paling menantang dari 13 jobsheet ini, dan kenapa. Latihan reflektif yang bagus sebelum presentasi UAS.

Selamat — kamu sudah menyelesaikan seluruh perjalanan Malang Bike, dari kerangka HTML statis di Jobsheet 1 sampai aplikasi web lengkap yang siap di-deploy di Jobsheet 13.
