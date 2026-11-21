# Dokumentasi Jobsheet 11 — Keamanan Web Dasar (Malang Bike)

Dokumentasi ini melanjutkan Jobsheet 10 (Autentikasi & Manajemen Sesi). Jobsheet 11 melakukan **audit keamanan** terhadap kode yang sudah dibangun: tombol Hapus yang masih berupa tautan `GET`, `htmlspecialchars()` yang tersebar di banyak tempat, dan form `POST` yang belum punya token.

## Tentang `docs/wireframe.md`

File ini berisi rancangan UI/UX untuk fitur yang akan datang (Login, Dashboard Petugas, Penyewaan). Tidak ada perubahan tampilan yang bergantung padanya di jobsheet ini.

## Apa yang Baru di Jobsheet 11?

Sesuai [README.md](../README.md), ada **audit keamanan menyeluruh** yang mencakup 5 kerentanan (detailnya di [`docs/security-checklist.md`](../docs/security-checklist.md)):

1. **SQL Injection** — diaudit ulang, sudah aman sejak Jobsheet 8 (tidak ada perubahan kode).
2. **XSS (Cross-Site Scripting)** — seluruh `htmlspecialchars()` yang tersebar diganti fungsi terpusat `e()`.
3. **CSRF (Cross-Site Request Forgery)** — token tersembunyi ditambahkan ke semua form `POST`; tombol Hapus diubah dari tautan `GET` menjadi form `POST`.
4. **Validasi & Sanitasi Input** — diaudit ulang, sudah memakai `(int)` dan validasi server-side.
5. **Session Fixation** — `session_regenerate_id(true)` sudah ada di `proses_login.php` sejak Jobsheet 10; diaudit dan dikonfirmasi.

Dua file baru menjadi pusat perubahan: **`includes/helpers.php`** (fungsi `e()`) dan **`includes/csrf.php`** (`csrf_token()`, `csrf_field()`, `csrf_verify()`).

## Daftar Isi

1. [Konsep Dasar Keamanan Web](01-konsep-dasar-keamanan-web.md)
2. [XSS & Fungsi `e()`](02-xss-dan-fungsi-e.md)
3. [CSRF & Token Verifikasi](03-csrf-dan-token.md)
4. [Session Fixation](04-session-fixation.md)
5. [Audit SQL Injection & Security Checklist](05-audit-sql-injection-dan-checklist.md)
6. [Rangkuman & Latihan Lanjutan](06-rangkuman-latihan.md)

## Struktur Folder

```
Jobsheet11/
├── includes/
│   ├── helpers.php               # BARU — fungsi e() untuk XSS
│   ├── csrf.php                   # BARU — token CSRF
│   ├── auth.php                   # Tidak berubah dari Jobsheet 10
│   └── header.php                 # require_once helpers.php & csrf.php; htmlspecialchars → e()
├── auth/
│   ├── proses_login.php            # + csrf_verify() (session_regenerate_id sudah ada)
│   └── ...                          # + csrf_field() di form
├── sepeda/, pelanggan/
│   ├── list.php                      # Output dibungkus e(); Hapus jadi form POST + token
│   ├── tambah.php, edit.php           # + csrf_field() di form
│   └── proses_*.php, hapus.php        # + csrf_verify(); hapus.php hanya menerima POST
├── assets/js/app.js                    # Konfirmasi hapus: click → submit
├── docs/
│   ├── wireframe.md
│   └── security-checklist.md          # BARU — audit lengkap
├── README.md
└── Dokumentasi/                        # Folder dokumentasi ini
```

**Catatan penting** dari [README.md](../README.md): bagian "Cara menguji" berisi langkah verifikasi konkret (uji CSRF lewat `curl`, uji XSS dengan menyimpan `<script>alert(1)</script>`, uji urutan guard, uji Hapus lewat `GET`) — semuanya dibahas ulang di bab-bab berikutnya dan dirangkum di [bab 6](06-rangkuman-latihan.md).
