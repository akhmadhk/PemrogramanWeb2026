# 4. Session Fixation

Perbaikan paling ringkas — **satu baris kode** — tapi butuh pemahaman konsep session untuk mengerti kenapa baris itu penting. Di Malang Bike, baris ini **sudah ada sejak Jobsheet 10**; bab ini menjelaskan alasannya dan mengonfirmasi hasil auditnya.

## 4.1 Apa itu Session Fixation?

**Session fixation** adalah serangan di mana penyerang **memaksa** korban memakai **ID sesi yang sudah diketahui penyerang**, lalu menunggu korban login memakai ID itu. Begitu korban login, penyerang (yang sudah tahu ID sesi itu sejak awal) bisa **ikut memakai** sesi yang sama, seolah-olah dia juga sudah login sebagai korban.

## 4.2 Kenapa Ini Bisa Terjadi?

Server memberi setiap pengunjung "tanda pengenal" unik (ID sesi) untuk mengingat mereka antar halaman. Secara default, **satu** ID yang sama bisa dipakai **sebelum** dan **sesudah** login. Kalau seseorang sudah tahu ID sesi seorang pengunjung **sebelum** ia login, ID itu tetap valid dan terhubung ke akun korban setelah korban berhasil login — karena server tidak pernah "mengganti" identitas sesi pada momen penting itu.

## 4.3 Solusi: `session_regenerate_id(true)`

```php
// auth/proses_login.php
if ($cocok) {
    session_regenerate_id(true); // cegah session fixation
    $_SESSION['user_id']  = $user['id'];
    $_SESSION['nama']     = $user['nama'] ?: $user['username'];
    $_SESSION['username'] = $user['username'];

    header('Location: ../index.php');
    exit;
}
```

- **`session_regenerate_id(true)`** mengganti ID sesi saat ini dengan ID **baru** yang acak, sambil **mempertahankan** data `$_SESSION` yang sudah ada.
- Argumen **`true`** **menghapus** data sesi lama di sisi server, sehingga ID lama (yang mungkin sudah diketahui penyerang) tidak bisa dipakai lagi.
- Dipanggil **tepat setelah** pencocokan password berhasil dan **sebelum** `$_SESSION['user_id']` diisi. ID lama dibuang pada momen paling kritis — transisi dari "belum login" ke "sudah login".

## 4.4 Hasil Audit di Malang Bike

Pemeriksaan `auth/proses_login.php` di Jobsheet 10 menunjukkan baris `session_regenerate_id(true)` **sudah ada** dan posisinya sudah benar. Jadi audit ini hanya **mengonfirmasi** (tidak ada perubahan kode untuk poin ini). Token CSRF juga tetap aman setelah ID sesi berganti, karena `session_regenerate_id(true)` mempertahankan isi `$_SESSION`.

## 4.5 Kenapa Baris Ini Tidak Ada di Registrasi/Logout?

- **Registrasi** tidak langsung membuat pengguna **login** — setelah mendaftar, pengguna diarahkan ke halaman Login, jadi tidak ada transisi "belum login → sudah login" di titik ini.
- **Logout** memanggil `session_destroy()` yang **menghapus total** data sesi; sesi itu sudah berakhir.

Kadang solusi keamanan yang tepat justru **sangat kecil**, asalkan **ditempatkan di titik yang benar** dalam alur program.

Lanjut ke: [Audit SQL Injection & Security Checklist](05-audit-sql-injection-dan-checklist.md)
