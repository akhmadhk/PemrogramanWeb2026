# Wireframe & User Flow — Malang Bike

Sub-CPMK: Merancang UI/UX aplikasi (proyek).

Halaman yang sudah ada (Beranda, Daftar/Tambah Sepeda, Daftar/Tambah Pelanggan) belum mencakup fitur Login, Dashboard Petugas, dan Penyewaan/Pengembalian. Dokumen ini merancang wireframe untuk halaman-halaman tersebut sebelum diimplementasikan di jobsheet berikutnya.

## Aktor
- **Tamu**: hanya bisa melihat daftar sepeda (Beranda, Daftar Sepeda) tanpa login.
- **Petugas**: login untuk mengakses seluruh fitur CRUD dan transaksi penyewaan.

## User Flow — Penyewaan Sepeda

```
[Petugas Login] -> [Dashboard] -> [Pilih menu "Sewa Baru"]
        -> [Pilih Pelanggan] -> [Pilih Sepeda (yang tersedia)]
        -> [Simpan] -> [Sepeda ditandai disewa] -> [Kembali ke Dashboard]
```

## User Flow — Pengembalian Sepeda

```
[Dashboard] -> [Menu "Pengembalian"] -> [Cari transaksi aktif (pelanggan/sepeda)]
        -> [Tandai "Dikembalikan"] -> [Sepeda tersedia lagi]
        -> [Kembali ke Dashboard]
```

## Wireframe: Halaman Login

```
+--------------------------------------+
|             MALANG BIKE              |
|--------------------------------------|
|                                      |
|         [ Login Petugas ]            |
|                                      |
|   Username : [______________]        |
|   Password : [______________]        |
|                                      |
|          [   Masuk   ]               |
+--------------------------------------+
```

## Wireframe: Dashboard Petugas

```
+-------------------------------------------------------------------+
| MALANG BIKE   Beranda | Sepeda | Pelanggan | Sewa | (Petugas) Logout |
|-------------------------------------------------------------------|
|  [Total Sepeda]   [Total Pelanggan]   [Sedang Disewa]              |
|                                                                   |
|  Aksi Cepat:                                                      |
|  [ + Sewa Baru ]   [ + Pengembalian ]                             |
|                                                                   |
|  Transaksi Terbaru                                                |
|  ------------------------------------------------------------     |
|  Pelanggan | Sepeda | Tgl Sewa | Status                            |
+-------------------------------------------------------------------+
```

## Wireframe: Form Penyewaan

```
+--------------------------------------+
|  Form Penyewaan Sepeda               |
|--------------------------------------|
|  Pelanggan : [ dropdown pelanggan ]  |
|  Sepeda    : [ dropdown, hanya yang  |
|                tersedia ]            |
|  Tanggal Sewa : [ auto: hari ini ]   |
|                                      |
|        [  Simpan Penyewaan  ]        |
+--------------------------------------+
```

## Wireframe: Form Pengembalian

```
+--------------------------------------+
|  Pengembalian Sepeda                 |
|--------------------------------------|
|  Cari transaksi aktif:               |
|  [ nama pelanggan / kode sepeda ____ ]|
|                                      |
|  Pelanggan | Sepeda | Tgl Sewa | [Kembalikan] |
+--------------------------------------+
```

## Wireframe: Riwayat Penyewaan per Pelanggan

```
+--------------------------------------------------+
|  Riwayat Penyewaan — Akhmad                      |
|--------------------------------------------------|
|  Sepeda        | Sewa   | Kembali | Status       |
|  MB-001 Polygon | 01/10  | 02/10   | Selesai      |
|  MB-004 Selis   | 15/10  | -       | Disewa       |
+--------------------------------------------------+
```

## Konsistensi dengan Desain yang Sudah Berjalan
- Warna aksen hijau, tipografi navbar, dan gaya tabel/kartu mengikuti `assets/css/style.css` yang sudah dibangun sejak awal.
- Navbar akan ditambah menu **Sewa** dan indikator status login (nama petugas / tombol Logout) saat diimplementasikan.
- Edge case yang perlu ditangani saat implementasi: sepeda yang sedang disewa tidak boleh dipilih di form penyewaan; pelanggan dengan tunggakan denda divalidasi di tahap lanjut (tugas mandiri).
