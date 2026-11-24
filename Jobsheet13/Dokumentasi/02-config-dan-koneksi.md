# 2. `includes/config.php` & Environment Variable

## 2.1 Kode Lengkap `includes/config.php`

```php
<?php
$databaseUrl = getenv('DATABASE_URL');
$dbopts = [];
if (is_string($databaseUrl) && $databaseUrl !== '') {
    $dbopts = parse_url($databaseUrl) ?: [];
}

return [
    'db_host' => $dbopts['host'] ?? (getenv('DB_HOST') ?: 'localhost'),
    'db_port' => $dbopts['port'] ?? (getenv('DB_PORT') ?: '5432'),
    'db_name' => isset($dbopts['path']) ? ltrim($dbopts['path'], '/') : (getenv('DB_NAME') ?: '01_malangbike'),
    'db_user' => isset($dbopts['user']) ? rawurldecode($dbopts['user']) : (getenv('DB_USER') ?: 'postgres'),
    'db_pass' => isset($dbopts['pass']) ? rawurldecode($dbopts['pass']) : (getenv('DB_PASS') ?: '12345678'),
];
```

(Komentar penjelasan di bagian atas file asli tidak ditampilkan di sini.)

## 2.2 File PHP yang Langsung `return` Sebuah Array

File ini **tidak berisi fungsi** apa pun (beda dari `includes/helpers.php` yang berisi fungsi `e()`). Bagian akhirnya hanyalah **satu pernyataan `return`** yang mengembalikan sebuah array asosiatif. Ini pola PHP yang umum untuk file konfigurasi: file ini tidak dimaksudkan dijalankan demi efek sampingnya (seperti `header.php` yang mencetak HTML) — ia dipanggil untuk **nilai kembaliannya**, dengan cara yang dibahas di [§2.5](#25-cara-memakainya-configphp--pdo-di-koneksiphp).

## 2.3 Tiga Sumber Nilai, dengan Urutan Prioritas

Untuk setiap pengaturan (misalnya `db_host`), nilai dicari berurutan:

1. **`DATABASE_URL`** — satu alamat lengkap berbentuk `postgres://user:pass@host:port/namadb`. Layanan hosting seperti Railway menyediakannya untuk database PostgreSQL. Kalau ada, nilainya dipakai lebih dulu.
2. **Environment variable satuan** — `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`, untuk server yang tidak memakai `DATABASE_URL`.
3. **Nilai cadangan** — `'localhost'`, `'5432'`, `'01_malangbike'`, `'postgres'`, `'12345678'`, khusus pengembangan lokal.

## 2.4 Membedah Satu Baris

```php
'db_host' => $dbopts['host'] ?? (getenv('DB_HOST') ?: 'localhost'),
```

- **`parse_url($databaseUrl)`** memecah alamat `DATABASE_URL` menjadi array berisi `host`, `port`, `user`, `pass`, dan `path` (nama database diawali `/`, jadi dibersihkan dengan `ltrim($dbopts['path'], '/')`).
- **`??`** (null coalescing) — "pakai nilai di kiri kalau ada; kalau tidak ada, pakai yang di kanan." Dipakai untuk `$dbopts['host']` karena kalau `DATABASE_URL` tidak ada, `$dbopts` kosong dan kunci `'host'` memang tidak ada.
- **`getenv('DB_HOST')`** — fungsi PHP bawaan yang membaca environment variable `DB_HOST`. Kalau tidak diset, hasilnya `false`.
- **`?:`** — operator **Elvis**. Beda dari `??`: `??` hanya memeriksa nilai `null`/tidak ada, sedangkan `?:` memeriksa apakah nilai di kiri **"falsy"** (`false`, `0`, string kosong, atau `null`). Karena `getenv()` mengembalikan **`false`** (bukan `null`) saat variabel tidak ada, `?:` yang cocok dipakai di sini.
- Jadi `(getenv('DB_HOST') ?: 'localhost')` berarti: "pakai `DB_HOST` kalau ada isinya; kalau tidak, pakai `'localhost'`."
- **`rawurldecode(...)`** untuk `user` dan `pass` mengembalikan karakter khusus yang di dalam URL ditulis dalam bentuk `%xx` (misalnya `@` ditulis `%40`) ke bentuk aslinya, supaya password yang mengandung karakter khusus tetap benar.
- **`parse_url(...) ?: []`** — kalau alamatnya rusak, `parse_url` mengembalikan `false`; dengan `?: []` hasilnya diganti array kosong sehingga kode tetap aman dan jatuh ke nilai berikutnya.

## 2.5 Cara Memakainya: `config.php` → PDO di `koneksi.php`

```php
// includes/koneksi.php
$config = require __DIR__ . '/config.php';

try {
    $pdo = new PDO(
        "pgsql:host={$config['db_host']};port={$config['db_port']};dbname={$config['db_name']}",
        $config['db_user'],
        $config['db_pass']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
```

- **`$config = require __DIR__ . '/config.php';`** — pemakaian `require` yang **berbeda** dari sebelumnya. Biasanya `require`/`include` dipakai untuk **efek samping** (menyisipkan HTML dari `header.php`). Di sini `require` dipakai untuk **mengambil nilai kembalian**: karena `config.php` berisi `return [...]`, hasil `require`-nya adalah **array itu sendiri**, langsung disimpan ke `$config`.
- **`{$config['db_host']}`** — kurung kurawal diperlukan untuk menyisipkan **elemen array** ke dalam string; tanpanya PHP bisa salah menafsirkan di mana nama variabel berakhir.
- Sisa kode (`try`/`catch`, `PDO::ATTR_ERRMODE`, `die(...)`) sama dengan yang dulu — hanya sumber nilainya yang berubah, dari variabel tertulis langsung menjadi array `$config`.
- Variabel `$pdo` tetap bernama sama, jadi semua halaman yang memakai `require ... koneksi.php` **tidak perlu diubah**.

## 2.6 Cara Mengatur Environment Variable Saat Deployment

Sesuai [README.md](../README.md):

```bash
DB_HOST=127.0.0.1 DB_NAME=01_malangbike DB_USER=produser DB_PASS=rahasia php -S localhost:8000
```

Menulis `NAMA_VARIABEL=nilai` **sebelum** sebuah perintah di terminal mengatur environment variable **khusus untuk proses itu saja**. Server `php -S` yang dijalankan "melihat" `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`, dan `getenv()` di `config.php` membaca nilai itu, **menimpa** nilai cadangan. Di hosting sungguhan, environment variable biasanya diatur lewat panel kontrol layanan (di Railway, `DATABASE_URL` sudah disediakan otomatis untuk database yang ditautkan), bukan diketik manual — tapi **konsepnya sama persis**: nilai rahasia disimpan **di luar** kode sumber.

## 2.7 Peringatan Penting

> Jangan mengubah nilai cadangan di `includes/config.php` menjadi kredensial asli lalu meng-commit-nya ke repository publik.

Memindahkan kredensial ke `config.php` **tidak otomatis** membuatnya aman kalau kamu tetap menuliskan kredensial asli di file itu lalu meng-commit-nya ke Git. Keamanan sesungguhnya baru didapat kalau kredensial asli **hanya** diatur lewat environment variable di server produksi. Nilai cadangan di `config.php` (termasuk password `12345678`) hanya untuk pengembangan lokal — pastikan password database produksimu **berbeda**.

Lanjut ke: [Dokumentasi Proyek: README, ERD, & Manual Pengguna](03-dokumentasi-proyek-final.md)
