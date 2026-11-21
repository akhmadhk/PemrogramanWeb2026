# 3. CSRF & Token Verifikasi

Ini bab paling teknis di jobsheet ini — melibatkan mekanisme baru: **token** rahasia per sesi.

## 3.1 Apa itu CSRF?

**CSRF (Cross-Site Request Forgery)** adalah celah yang memungkinkan **situs lain** memicu sebuah permintaan (misalnya menghapus sepeda) ke aplikasi ini, **atas nama** pengguna yang sedang login — tanpa pengguna itu sadar atau bermaksud melakukannya.

## 3.2 Contoh Konkret Serangannya

Bayangkan kamu sedang login di Malang Bike, lalu tanpa sengaja membuka situs jahat di tab lain.

**Serangan 1 — terhadap kode Jobsheet 10.** Di Jobsheet 10, tombol Hapus adalah tautan `GET`: `sepeda/hapus.php?id=1`. Situs jahat cukup menaruh satu gambar tak terlihat:

```html
<img src="http://localhost:8000/sepeda/hapus.php?id=1" width="0" height="0">
```

Saat browser memuat "gambar" itu, ia mengirim permintaan ke `hapus.php` **beserta cookie sesi login kamu** — dan sepeda nomor 1 terhapus tanpa satu klik pun.

**Serangan 2 — kalau hanya diubah jadi `POST`.** Mengubah `hapus.php` agar hanya menerima `POST` menutup serangan 1, tapi situs jahat tinggal memakai form:

```html
<form action="http://localhost:8000/sepeda/hapus.php" method="post" id="jahat">
    <input type="hidden" name="id" value="1">
</form>
<script>document.getElementById('jahat').submit();</script>
```

Browser tidak peduli form itu berasal dari situs mana — selama kamu login, cookie sesi tetap ikut terkirim. Pemeriksaan `$_SERVER['REQUEST_METHOD'] !== 'POST'` **tidak cukup**. Itulah kenapa dibutuhkan lapisan baru: **token CSRF**.

## 3.3 Ide Dasar Token CSRF

Setiap form `POST` yang **sah** (dibuat oleh aplikasi ini sendiri) disisipi **token rahasia** — deretan karakter acak yang hanya diketahui server dan halaman yang benar-benar dibuka dari aplikasi ini. Saat form di-submit, server memeriksa apakah token yang dikirim **cocok**. Situs jahat **tidak mungkin tahu** token ini, sehingga form palsunya **ditolak** walau cookie sesinya valid.

## 3.4 Membuat Token: `csrf_token()`

```php
function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
```

- **`random_bytes(32)`** menghasilkan 32 byte acak yang **kriptografis aman** (beda dari `rand()` biasa yang bisa ditebak).
- **`bin2hex(...)`** mengubah data biner itu jadi teks heksadesimal supaya aman dipakai sebagai nilai atribut HTML.
- **`empty($_SESSION['csrf_token'])`** — token **dibuat sekali** per sesi lalu dipakai ulang, jadi tidak berubah-ubah tiap halaman.
- Disimpan di **`$_SESSION`**, sehingga **unik per pengunjung**.

## 3.5 Menyisipkan Token ke Form: `csrf_field()`

```php
function csrf_field()
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}
```

Menghasilkan satu `<input type="hidden">` yang tidak terlihat pengguna tapi **wajib** ikut terkirim. Dipakai dengan `<?php echo csrf_field(); ?>` di dalam setiap `<form method="post">`:

- Form **Tambah** dan **Edit** Sepeda & Pelanggan
- Form **Hapus** di `list.php` (kini berupa form `POST`, bukan tautan)
- Form **Login** dan **Registrasi**

## 3.6 Memverifikasi Token: `csrf_verify()`

```php
function csrf_verify()
{
    $token = $_POST['csrf_token'] ?? '';
    if ($token === '' || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('Permintaan ditolak: token CSRF tidak valid atau kedaluwarsa.');
    }
}
```

- **`hash_equals(...)`** membandingkan dua string dengan waktu eksekusi yang **selalu sama**, sehingga tidak membocorkan informasi lewat selisih waktu proses (praktik yang baik untuk data sensitif seperti token).
- **`http_response_code(403)`** mengirim status **403 Forbidden**: "permintaan dipahami tapi ditolak".
- **`die(...)`** menghentikan eksekusi seketika, sehingga `INSERT`/`UPDATE`/`DELETE` setelahnya **tidak sempat jalan**.

## 3.7 Perubahan di Tombol Hapus

Karena token hanya bisa dikirim lewat `POST`, tombol Hapus harus berubah bentuk:

**Sebelumnya (Jobsheet 10):**
```php
<a href="hapus.php?id=<?php echo (int)$s['id']; ?>" class="btn btn-danger btn-hapus">Hapus</a>
```

**Sekarang (Jobsheet 11):**
```php
<form class="form-hapus" method="post" action="hapus.php">
    <input type="hidden" name="id" value="<?php echo (int)$s['id']; ?>">
    <?php echo csrf_field(); ?>
    <button type="submit" class="btn btn-danger btn-hapus">Hapus</button>
</form>
```

Dua akibat lain dari perubahan ini:
- `hapus.php` menolak `GET` (langsung kembali ke daftar) dan membaca `id` dari `$_POST`, bukan `$_GET`.
- Konfirmasi "Yakin ingin menghapus?" di `app.js` dipasang pada event **`submit`** form (bukan `click` pada tautan), sehingga `preventDefault()` bisa membatalkan pengiriman form.
- CSS ditambah aturan `td form.form-hapus` agar tombol Hapus di dalam form tetap tampil merah dan sejajar dengan tombol Edit.

## 3.8 Memanggil `csrf_verify()` di Awal Setiap Proses

```php
// sepeda/proses_tambah.php
session_start();
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/csrf.php';
require __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: list.php');
    exit;
}

csrf_verify();
```

Perhatikan **urutannya**: `auth.php` (guard login) dipanggil **lebih dulu**, baru `csrf.php`, baru `csrf_verify()`. Pengguna yang belum login langsung diarahkan ke Login tanpa sempat memicu pengecekan CSRF. Di Jobsheet 10, urutan `require` adalah `koneksi.php` lalu `auth.php`; sekarang `auth.php` dipindah ke paling atas (setelah `session_start()`).

`csrf_verify()` dipasang di: `proses_tambah.php`, `proses_edit.php`, `hapus.php` (Sepeda & Pelanggan), serta `auth/proses_login.php` dan `auth/proses_register.php`.

## 3.9 Cara Membuktikannya Sendiri

> login, lalu coba kirim `curl -X POST http://localhost:8000/sepeda/proses_tambah.php -d "merek=x"` dengan cookie sesi login tetapi tanpa `csrf_token` → harus mendapat HTTP 403.

`curl` mensimulasikan "situs jahat": mengirim `POST` tanpa token yang benar. Kamu menerima pesan "Permintaan ditolak: token CSRF tidak valid atau kedaluwarsa." dengan status 403, **bukan** data sepeda baru yang tersimpan. Uji juga: buka `sepeda/hapus.php?id=1` langsung di browser — kamu hanya dikembalikan ke daftar, tidak ada yang terhapus.

Lanjut ke: [Session Fixation](04-session-fixation.md)
