# Wireframe & User Flow — MalangBike

Sub-CPMK: Merancang UI/UX aplikasi (proyek).

Halaman yang sudah ada pada website MalangBike meliputi Beranda, Daftar/Tambah Sepeda, dan Daftar/Tambah Lokasi. Dokumen ini merancang wireframe untuk pengembangan fitur Login, Dashboard Petugas, Penyewaan, Pengembalian, dan Riwayat Penyewaan.

## Aktor
- **Tamu**: dapat melihat informasi MalangBike, daftar sepeda, dan lokasi rental tanpa login.
- **Petugas**: login untuk mengakses pengelolaan data sepeda, lokasi, serta transaksi penyewaan dan pengembalian sepeda.
## User Flow — Penyewaan Sepeda
[Petugas Login] -> [Dashboard] -> [Pilih menu "Penyewaan Baru"]
        -> [Pilih Pelanggan] -> [Pilih Sepeda (tersedia)]
        -> [Simpan] -> [Stok sepeda berkurang 1]
        -> [Kembali ke Dashboard]
## User Flow — Pengembalian Sepeda
[Dashboard] -> [Menu "Pengembalian"]
        -> [Cari transaksi aktif (pelanggan/sepeda)]
        -> [Tandai "Dikembalikan"]
        -> [Stok sepeda bertambah 1]
        -> [Kembali ke Dashboard]
## Wireframe: Halaman Login
+--------------------------------------+
|             MalangBike               |
|--------------------------------------|
|                                      |
|          [ Login Petugas ]           |
|                                      |
|   Username : [______________]        |
|   Password : [______________]        |
|                                      |
|          [     Masuk     ]           |
|                                      |
|      Belum punya akun? Daftar        |
+--------------------------------------+
## Wireframe: Dashboard Petugas
+------------------------------------------------------+
| MalangBike   Beranda | Sepeda | Lokasi | Penyewaan   | (Petugas) Logout |
|                                                      |
|------------------------------------------------------|
|                                                      |
| [Total Sepeda] [Total Lokasi] [Sedang Disewa]        |
|                                                      |
| Aksi Cepat:                                          |
|                                                      |
| [ + Penyewaan Baru ]     [ + Pengembalian ]          |
|                                                      |
| Transaksi Terbaru                                    |
| ---------------------------------------------------  |
| Pelanggan | Sepeda | Tgl Sewa | Status               |
| ---------------------------------------------------  |
| Budi      | MTB    | 20/09    | Disewa               |
| Siti      | City   | 19/09    | Dikembalikan        |
+------------------------------------------------------+
## Wireframe: Daftar Sepeda
+------------------------------------------------------+
| MalangBike   Beranda | Sepeda | Lokasi | Penyewaan   |
|------------------------------------------------------|
|                                                      |
| Daftar Sepeda                         [ + Tambah ]    |
|                                                      |
| ---------------------------------------------------  |
| Nama Sepeda | Jenis | Harga/Jam | Status | Aksi      |
| ---------------------------------------------------  |
| City Bike 01| City  | Rp15.000   | Tersedia | Edit   |
| MTB 01      | MTB   | Rp25.000   | Disewa   | Hapus  |
| E-Bike 01   | E-Bike| Rp35.000   | Tersedia | Edit   |
|                                                      |
+------------------------------------------------------+
## Wireframe: Tambah Sepeda
+--------------------------------------+
|         Tambah Sepeda                |
|--------------------------------------|
|                                      |
| Nama Sepeda : [______________]       |
|                                      |
| Jenis : [ Pilih Jenis Sepeda ▼ ]     |
|                                      |
| Harga/Jam : [______________]         |
|                                      |
| Deskripsi :                          |
| [____________________________]       |
| [____________________________]       |
|                                      |
|        [ Simpan Sepeda ]             |
+--------------------------------------+
## Wireframe: Daftar Lokasi
+------------------------------------------------------+
| MalangBike        Beranda | Sepeda | Lokasi | Penyewaan |
+------------------------------------------------------+
|                                                      |
| Daftar Lokasi                               [ + Tambah ] |
|                                                      |
+------------------------------------------------------+
| Nama Lokasi           | Area   | Alamat                | Aksi        |
+------------------------------------------------------+
| Alun-Alun Malang      | Klojen | Jl. Merdeka Selatan   | Edit | Hapus |
| Stasiun Kota Malang   | Klojen | Jl. Trunojoyo No. 10  | Edit | Hapus |
| Ijen Boulevard        | Klojen | Jl. Ijen              | Edit | Hapus |
+------------------------------------------------------+

## Wireframe: Tambah Lokasi
+--------------------------------------+
|          Tambah Lokasi               |
|--------------------------------------|
|                                      |
| Nama Lokasi : [______________]       |
|                                      |
| Area : [____________________]        |
|                                      |
| Alamat :                             |
| [____________________________]       |
| [____________________________]       |
|                                      |
|        [ Simpan Lokasi ]             |
+--------------------------------------+
## Wireframe: Form Penyewaan
+--------------------------------------+
|        Form Penyewaan Sepeda         |
|--------------------------------------|
|                                      |
| Pelanggan : [ Pilih Pelanggan ▼ ]    |
|                                      |
| Sepeda : [ Pilih Sepeda ▼ ]          |
|          (hanya yang tersedia)       |
|                                      |
| Lokasi : [ Pilih Lokasi ▼ ]          |
|                                      |
| Tanggal Sewa : [ auto: hari ini ]    |
|                                      |
| Durasi : [____] Jam                  |
|                                      |
| Total Harga : Rp __________           |
|                                      |
|       [ Simpan Penyewaan ]           |
+--------------------------------------+
## Wireframe: Form Pengembalian
+--------------------------------------+
|       Pengembalian Sepeda            |
|--------------------------------------|
|                                      |
| Cari transaksi aktif:                |
| [ pelanggan / nama sepeda ______ ]   |
|                                      |
| ------------------------------------ |
| Pelanggan | Sepeda | Tgl Sewa | Aksi |
| ------------------------------------ |
| Budi      | MTB 01 | 20/09    |      |
|                         [Kembalikan] |
|                                      |
+--------------------------------------+
## Wireframe: Riwayat Penyewaan
+--------------------------------------+
|       Riwayat Penyewaan              |
|--------------------------------------|
|                                      |
| Pelanggan : Budi Santoso             |
|                                      |
| Sepeda       | Sewa | Kembali | Status|
|--------------------------------------|
| MTB 01       |20/09 | 20/09   | Selesai
| City Bike 02 |21/09 | -       | Disewa
|                                      |
+--------------------------------------+
## Konsistensi dengan Desain yang Sudah Berjalan
- Warna utama tetap menggunakan warna hijau MalangBike yang sudah digunakan pada website.
- Tipografi, navbar, card, tabel, tombol, dan form dibuat mengikuti gaya UI/UX website MalangBike yang sudah dibuat.
- Menu utama disesuaikan menjadi Beranda, Sepeda, Lokasi, dan Penyewaan.
Menu Cara Sewa tidak digunakan sebagai menu utama, sesuai struktur website MalangBike terakhir.
- Sepeda memiliki fitur daftar, tambah, dan hapus.
- Lokasi memiliki fitur daftar, tambah, dan hapus.
- Petugas memiliki indikator status login serta tombol Logout.
- Pada form penyewaan, sepeda yang sedang tidak tersedia tidak dapat dipilih.
- Setelah penyewaan berhasil, jumlah/stok sepeda berkurang.
- Setelah sepeda dikembalikan, jumlah/stok sepeda bertambah.
- Riwayat penyewaan menyimpan informasi pelanggan, sepeda, tanggal sewa, tanggal kembali, dan status transaksi.