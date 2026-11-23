# Jobsheet 12 — Integrasi Modul Penyewaan (Malang Bike)

Sub-CPMK: Mengintegrasikan front-end dan back-end proyek secara utuh.

## Perubahan dari Jobsheet 11
- Tambah `sql/02_penyewaan.sql` — tabel `penyewaan` (relasi ke `sepeda` dan `pelanggan`), menghubungkan dua tabel yang sudah dibangun sejak Jobsheet 8.
- Tambah modul **Penyewaan** (menghubungkan seluruh entitas yang sudah dibangun sejak Jobsheet 8-11 sekaligus):
  - `penyewaan/tambah.php` + `proses_tambah.php`: pilih pelanggan + sepeda (dropdown hanya sepeda yang **tidak sedang disewa**), simpan transaksi dalam satu **transaction** (`beginTransaction`/`commit`/`rollBack`) dengan `SELECT ... FOR UPDATE` pada baris sepeda untuk mencegah satu sepeda disewa dua kali.
  - `penyewaan/kembali.php` + `proses_kembali.php`: daftar transaksi aktif (`status = 'disewa'`), tombol Kembalikan mengubah status menjadi `dikembalikan` dan mengisi `tanggal_kembali` dalam transaction serupa.
  - `penyewaan/riwayat.php`: histori penyewaan per pelanggan (JOIN `penyewaan` + `sepeda`).
- `includes/header.php`: navbar menambahkan menu Sewa Baru, Pengembalian, Riwayat (hanya saat login).
- `index.php`: kartu baru **"Sedang Disewa"** yang dihitung dari `COUNT(*) FROM penyewaan WHERE status = 'disewa'`; Beranda juga kini menampilkan pesan flash (agar notifikasi "Penyewaan berhasil dicatat." langsung terlihat setelah redirect).
- `sepeda/hapus.php` dan `pelanggan/hapus.php`: kalau data masih punya riwayat penyewaan, database menolak penghapusan (foreign key); pesan error kini dibuat ramah ("tidak bisa dihapus karena sudah memiliki riwayat penyewaan") alih-alih pesan SQL mentah.
- `assets/css/style.css`: navbar boleh turun baris (menunya bertambah), dan tombol Kembalikan di dalam form dibuat sama gayanya dengan tombol lain.

## Cara menjalankan
Jalankan skema tambahan **sebelum** membuka aplikasi (Beranda sekarang membaca tabel `penyewaan`):
```bash
psql -d 01_malangbike -f sql/02_penyewaan.sql
```
(nama database harus sama dengan yang ada di `includes/koneksi.php`).

**Opsi 1 — PHP built-in server**:
```bash
php -S localhost:8000
```

**Opsi 2 — Laragon (Apache)**: lewat virtual host langsung ke folder `Jobsheet12/`, atau bersarang di bawah domain proyek — path CSS/JS/link/redirect login sudah relatif otomatis (lihat `includes/header.php` & `includes/auth.php`), jadi keduanya jalan.

## Pengujian end-to-end yang disarankan
Registrasi petugas → Login → Tambah Sepeda & Pelanggan → Sewa Baru → cek kartu "Sedang Disewa" di Beranda bertambah dan sepeda itu hilang dari dropdown Sewa Baru → Pengembalian → cek transaksi hilang dari daftar aktif dan sepeda muncul lagi di dropdown → Riwayat (pilih pelanggan) → transaksi muncul berstatus "Selesai" → coba hapus sepeda/pelanggan yang punya riwayat (harus ditolak dengan pesan ramah) → Logout.

## Catatan
- Setiap sepeda di Malang Bike adalah **satu unit** (tidak ada kolom `stok`), jadi ketersediaan tidak disimpan sebagai angka: sepeda dianggap tersedia kalau **tidak punya** penyewaan berstatus `disewa`. Karena itu transaction di sini melindungi proses "cek lalu simpan" agar atomik, dan indeks unik parsial `uq_penyewaan_sepeda_aktif` menjadi pengaman terakhir di level database.
- Validasi bisnis tambahan (pelanggan yang menyewa lebih dari 7 hari dan belum mengembalikan tidak boleh menyewa sepeda lagi) belum diterapkan — jadi tugas mandiri.
- `docs/wireframe.md` dan `docs/security-checklist.md` tidak berubah dari Jobsheet 11. Halaman baru mengikuti pola yang sama: `e()` untuk output, `csrf_field()`/`csrf_verify()` untuk form `POST`, dan `auth.php` lebih dulu daripada `csrf.php`.
