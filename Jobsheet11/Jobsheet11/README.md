# Jobsheet 11 — Keamanan Web Dasar (Malang Bike)

Sub-CPMK: Menerapkan prinsip keamanan web dasar.

## Perubahan dari Jobsheet 10
- Tambah `includes/helpers.php` (`e()` untuk `htmlspecialchars`) dan `includes/csrf.php` (`csrf_token()`, `csrf_field()`, `csrf_verify()`), keduanya di-`require_once` dari `includes/header.php`.
- **XSS**: seluruh output data dari database/`$_GET` (kode sepeda, merek, jenis, nama, alamat, no. telepon, nilai pencarian, nama petugas di navbar, judul halaman, pesan flash) kini dibungkus `e()` — menggantikan pemanggilan `htmlspecialchars()` yang sebelumnya tersebar tanpa `ENT_QUOTES`/`UTF-8` eksplisit.
- **CSRF**: token tersembunyi ditambahkan ke semua form POST (Tambah/Edit/Hapus Sepeda & Pelanggan, Login, Register); setiap `proses_*.php` dan `hapus.php` memanggil `csrf_verify()` sebelum menyentuh database.
- **Hapus via POST**: tombol Hapus di `sepeda/list.php` dan `pelanggan/list.php` berubah dari tautan `hapus.php?id=...` (GET) menjadi `<form class="form-hapus" method="post">` yang membawa token; `hapus.php` hanya menerima `POST`. `app.js` (`initHapusConfirm`) dipindah dari event `click` ke `submit`.
- **Urutan guard**: di `proses_*.php`, `hapus.php`, dan `edit.php`, `includes/auth.php` kini di-`require` lebih dulu daripada `koneksi.php`/`csrf.php`.
- **Session fixation**: `session_regenerate_id(true)` di `auth/proses_login.php` sudah ada sejak Jobsheet 10 — diaudit ulang, tidak ada perubahan kode.
- **SQL Injection**: diaudit ulang (tidak ada perubahan kode — semua query sudah prepared statement).
- Tambah `docs/security-checklist.md` — dokumen audit lengkap dengan bukti before/after per kerentanan, termasuk satu temuan tambahan (#6).
- `assets/js/sepeda.js` dan `pelanggan.js` (demo `fetch` JSON, tidak dipakai di halaman mana pun) dihapus.

## Cara menjalankan
Siapkan database lebih dulu (`sql/01_malangbike.sql`; nama database harus sama dengan yang ada di `includes/koneksi.php`, yaitu `01_malangbike`).

**Opsi 1 — PHP built-in server**:
```bash
php -S localhost:8000
```

**Opsi 2 — Laragon (Apache)**: lewat virtual host langsung ke folder `Jobsheet11/`, atau bersarang di bawah domain proyek — path CSS/JS/link/redirect login sudah relatif otomatis (lihat `includes/header.php` & `includes/auth.php`), jadi keduanya jalan.

## Cara menguji
- **CSRF**: login lewat browser, salin cookie sesi (`PHPSESSID`), lalu kirim `curl -X POST http://localhost:8000/sepeda/proses_tambah.php -b "PHPSESSID=<nilai>" -d "merek=x"` tanpa `csrf_token` → harus mendapat HTTP 403.
- **XSS**: tambah sepeda dengan merek `<script>alert(1)</script>` → di Daftar Sepeda harus tampil sebagai teks, bukan pop-up.
- **Guard order**: akses `proses_tambah.php` lewat POST tanpa login sama sekali → tetap redirect ke Login (guard `auth.php` jalan lebih dulu daripada `csrf_verify()`).
- **Hapus via GET**: buka `http://localhost:8000/sepeda/hapus.php?id=1` langsung di browser → hanya dikembalikan ke daftar, data tidak terhapus.

## Catatan
- Lihat `docs/security-checklist.md` untuk rincian audit dan pemetaan tiap kerentanan ke perbaikannya.
