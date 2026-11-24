# Dokumentasi Jobsheet 13 — Deployment & Dokumentasi

Dokumentasi ini melanjutkan dokumentasi Jobsheet 12 (Integrasi Modul Penyewaan). Jobsheet 13 adalah **jobsheet penutup** seluruh rangkaian Malang Bike — sesuai judulnya, "Deployment & Dokumentasi": bukan menambah fitur baru, melainkan **menyiapkan** aplikasi yang sudah selesai dibangun (Jobsheet 1-12) supaya siap **dijalankan di tempat lain** (server produksi/hosting) dan **dipahami orang lain** (dokumentasi proyek).

## Kabar Baik: Hampir Semua Kode Aplikasi Tidak Berubah

Perbedaan kode dari Jobsheet 12 hanya ada di 3 file: `includes/koneksi.php`, file baru `includes/config.php`, dan `includes/footer.php` (hanya label jobsheet). Satu file lain, `sql/01_malangbike.sql`, hanya berubah di komentar baris atas. Seluruh logika CRUD Sepeda/Pelanggan, autentikasi, CSRF, dan modul Penyewaan **identik persis** dengan Jobsheet 12.

## Apa yang Baru di Jobsheet 13?

Sesuai [README.md](../README.md) jobsheet ini:

1. **`includes/config.php`** — file baru yang memisahkan **kredensial** database dari kode sumber, membaca dari *environment variable* dengan nilai cadangan (fallback) untuk pengembangan lokal.
2. **`includes/koneksi.php`** diubah untuk membaca konfigurasi dari `config.php`, bukan lagi menuliskan kredensial langsung di kodenya. Dua cabang koneksi (Railway dan lokal) menjadi satu.
3. **`docs/manual-pengguna.md`** — panduan penggunaan aplikasi untuk pengguna akhir (bukan dokumentasi kode untuk developer).
4. **`README.md`** (di root proyek) menjadi **snapshot akhir** proyek: ERD final, matriks fitur per role, instruksi instalasi lengkap.

## Daftar Isi

1. [Konsep Dasar: Deployment & Pemisahan Konfigurasi](01-konsep-dasar-deployment-config.md)
2. [`includes/config.php` & Environment Variable](02-config-dan-koneksi.md)
3. [Dokumentasi Proyek: README, ERD, & Manual Pengguna](03-dokumentasi-proyek-final.md)
4. [Rangkuman & Latihan Lanjutan](04-rangkuman-latihan.md)

## Struktur Folder

```
Jobsheet13/
├── index.php, sepeda/, pelanggan/, auth/, penyewaan/   # Identik dengan Jobsheet 12
├── includes/
│   ├── config.php                # BARU — kredensial dari environment variable
│   ├── koneksi.php               # Diubah — membaca config.php
│   └── ...                       # header, footer, auth, csrf, helpers — tidak berubah (footer hanya label)
├── sql/                          # Identik (hanya komentar nama database di 01_malangbike.sql)
├── docs/
│   ├── wireframe.md              # Identik dengan Jobsheet 12
│   ├── security-checklist.md     # Identik dengan Jobsheet 12 (asal dari Jobsheet 11)
│   └── manual-pengguna.md        # BARU — panduan pengguna akhir
├── README.md                     # Ditulis ulang sebagai snapshot akhir proyek
└── Dokumentasi/                  # Folder dokumentasi ini
```

**Catatan penting** dari [README.md](../README.md): perubahan koneksi perlu diuji ulang di lingkungan lokal (tanpa environment variable) dan di hosting (dengan `DATABASE_URL`) sebelum demo UAS.
