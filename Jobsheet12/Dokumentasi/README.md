# Dokumentasi Jobsheet 12 — Integrasi Modul Penyewaan (Malang Bike)

Dokumentasi ini melanjutkan Jobsheet 11 (Keamanan Web Dasar). Jobsheet 12 adalah **puncak** dari rangkaian jobsheet Malang Bike: wireframe Penyewaan/Pengembalian yang sudah dirancang sejak awal sekarang **benar-benar dibangun**, menghubungkan semua yang sudah dipelajari — HTML/CSS, PHP, database, sesi, otorisasi, dan keamanan — dalam satu fitur.

## Tentang `docs/wireframe.md` dan `docs/security-checklist.md`

Kedua file ini **identik** dengan versi Jobsheet 11. Tidak ada rancangan UI/UX maupun audit keamanan baru — jobsheet ini murni **mengimplementasikan** apa yang sudah dirancang dan diamankan sebelumnya.

## Apa yang Baru di Jobsheet 12?

Sesuai [README.md](../README.md):

1. **`sql/02_penyewaan.sql`** — tabel `penyewaan` baru, **terhubung** (lewat *foreign key*) ke tabel `sepeda` dan `pelanggan`.
2. **`penyewaan/tambah.php` + `proses_tambah.php`** — mencatat penyewaan baru **dalam satu transaction**, dengan `SELECT ... FOR UPDATE` untuk mencegah *race condition*.
3. **`penyewaan/kembali.php` + `proses_kembali.php`** — daftar transaksi aktif dan tombol Kembalikan.
4. **`penyewaan/riwayat.php`** — histori penyewaan per pelanggan, memakai `JOIN`.
5. **Navbar** menambahkan 3 menu baru (Sewa Baru, Pengembalian, Riwayat), hanya untuk Petugas yang login.
6. **Kartu "Sedang Disewa"** di Beranda dihitung dari database.

## Daftar Isi

1. [Konsep Dasar: Relasi Tabel & Transaction](01-konsep-dasar-integrasi-dan-transaksi.md)
2. [Skema `penyewaan` & Relasi Antar Tabel](02-skema-penyewaan-dan-relasi.md)
3. [Penyewaan Baru & Database Transaction](03-penyewaan-baru-dan-transaction.md)
4. [Pengembalian Sepeda](04-pengembalian-sepeda.md)
5. [Riwayat Penyewaan & `JOIN`](05-riwayat-penyewaan-join.md)
6. [Rangkuman & Latihan Lanjutan](06-rangkuman-latihan.md)

## Struktur Folder

```
Jobsheet12/
├── index.php                       # + kartu "Sedang Disewa" dari database
├── includes/
│   ├── header.php                    # + 3 menu Penyewaan
│   └── ...                            # Tidak berubah lainnya
├── penyewaan/
│   ├── tambah.php, proses_tambah.php  # BARU — transaction + FOR UPDATE
│   ├── kembali.php, proses_kembali.php # BARU — transaction serupa
│   └── riwayat.php                     # BARU — JOIN 2 tabel
├── sql/
│   ├── 01_malangbike.sql
│   └── 02_penyewaan.sql               # BARU — tabel penyewaan + foreign key
├── sepeda/, pelanggan/, auth/           # Hanya hapus.php yang berubah (pesan foreign key)
├── docs/
│   ├── wireframe.md                     # Identik dengan Jobsheet 11
│   └── security-checklist.md            # Identik dengan Jobsheet 11
├── README.md
└── Dokumentasi/                          # Folder dokumentasi ini
```

**Catatan penting** dari [README.md](../README.md): validasi bisnis tambahan (pelanggan yang menyewa lebih dari 7 hari dan belum mengembalikan tidak boleh menyewa lagi) **belum diterapkan** — dijadikan tugas mandiri (lihat [bab 6](06-rangkuman-latihan.md)).
