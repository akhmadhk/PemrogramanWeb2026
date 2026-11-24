# 1. Konsep Dasar: Deployment & Pemisahan Konfigurasi

## 1.1 Apa itu Deployment?

**Deployment** adalah proses memindahkan/menjalankan aplikasi yang sudah selesai dikembangkan di komputermu sendiri (disebut *lingkungan pengembangan/development*) ke tempat lain — server produksi, hosting, atau komputer lab untuk demo — yang disebut *lingkungan produksi (production)*. Sepanjang Jobsheet 1 sampai 12, kamu menjalankan Malang Bike di komputer sendiri lewat `php -S localhost:8000`. Jobsheet ini mempersiapkan aplikasi supaya bisa dijalankan di **tempat lain** yang punya konfigurasi berbeda (nama database, kredensial login PostgreSQL, dan sebagainya).

## 1.2 Masalahnya: Kredensial yang "Menempel" di Kode

Di `includes/koneksi.php` versi Jobsheet 12, kredensial ditulis langsung di dalam kode, bahkan dalam **dua cabang**:

```php
$databaseUrl = getenv('DATABASE_URL');

if ($databaseUrl) {
    // === KONEKSI RAILWAY ===
    $dbopts = parse_url($databaseUrl);
    $host = $dbopts["host"] ?? '';
    // ... port, user, pass, db dari URL
} else {
    // === KONEKSI LOKAL ===
    $pdo = new PDO("pgsql:host=localhost;port=5432;dbname=01_malangbike", "postgres", "12345678");
}
```

Cabang Railway sudah membaca kredensial dari environment variable (`DATABASE_URL`) — itu praktik yang benar. Tapi cabang lokal menulis nama database, username, dan password **langsung di kode**, dan logika koneksi PDO diulang dua kali. Ini bekerja untuk belajar di komputer sendiri, tapi punya masalah nyata begitu aplikasi **dipindahkan**:

1. **Kredensial berbeda di tiap lingkungan** — server sungguhan biasanya punya username/password PostgreSQL yang berbeda dari yang dipakai untuk belajar. Kalau kredensial ditulis di kode, kamu harus **mengedit file kode sumber** setiap berpindah lingkungan — merepotkan dan rawan salah edit.
2. **Risiko keamanan kalau kode di-commit ke repository publik** — password database yang ditulis di kode ikut terbaca siapa pun yang bisa melihat repository (misalnya di GitHub).
3. **Logika yang berulang** — membuat koneksi PDO dan menangani error ditulis dua kali, sehingga perubahan harus dilakukan di dua tempat.

## 1.3 Solusi: Environment Variable

**Environment variable** (variabel lingkungan) adalah nilai yang disimpan **di luar** kode sumber — di tingkat sistem operasi/server tempat aplikasi dijalankan, bukan di dalam file `.php` mana pun. Setiap server bisa punya nilai berbeda tanpa perlu mengubah satu baris kode pun. Cara PHP membacanya (`getenv()`) dibahas di [bab 2](02-config-dan-koneksi.md).

## 1.4 Kenapa Nilai Cadangannya Tetap Ada?

`includes/config.php` **tetap** menyediakan nilai cadangan (`'localhost'`, `'01_malangbike'`, `'postgres'`, dan seterusnya) — bukan **mengharuskan** environment variable selalu diisi. Keputusan ini disengaja: untuk **belajar/pengembangan lokal**, nilai cadangan yang sama seperti sebelumnya tetap membuat aplikasi langsung jalan tanpa konfigurasi tambahan. Hanya saat **benar-benar di-deploy** (dengan kredensial sungguhan), environment variable diisi untuk **menimpa** nilai cadangan itu.

## 1.5 Kenapa Dipisah ke File Baru (`config.php`), Bukan Ditambah di `koneksi.php`?

Prinsip pemisahan tanggung jawab (*separation of concerns*): `config.php` **hanya** bertanggung jawab menentukan **nilai apa** yang dipakai (dari environment atau cadangan), sementara `koneksi.php` **hanya** bertanggung jawab **menggunakan** nilai itu untuk membuat koneksi PDO. Dengan begitu logika koneksi cukup ditulis **satu kali** (tidak lagi dua cabang), dan `config.php` bisa dipakai ulang kalau suatu saat ada bagian lain aplikasi yang butuh nilai konfigurasi yang sama.

Lanjut ke: [`includes/config.php` & Environment Variable](02-config-dan-koneksi.md)
