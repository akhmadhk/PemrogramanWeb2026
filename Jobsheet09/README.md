# Jobsheet 9 — CRUD Penuh (Malang Bike)

Sub-CPMK: Membangun fitur CRUD pada proyek.

## Perubahan dari Jobsheet 8
- Tambah `sepeda/proses_edit.php` dan `pelanggan/proses_edit.php`; `sepeda/edit.php` dan `pelanggan/edit.php` kini hanya menampilkan **form terisi data lama** (di Jobsheet 8 satu file `edit.php` mengerjakan form sekaligus proses simpan). Melengkapi Create+Read (Jobsheet 8) dengan **Update** yang rapi.
- `sepeda/hapus.php`, `pelanggan/hapus.php` — **Delete**, kini hanya menerima `POST` (bukan `GET`) agar tidak terpicu tidak sengaja lewat link/crawler.
- Tombol Hapus di `list.php` sekarang berupa `<form class="form-hapus" method="post">` sungguhan (bukan lagi link `<a href="hapus.php?id=...">`) — `app.js` (`initHapusConfirm`) diubah untuk konfirmasi di event `submit` (bisa `preventDefault()`), bukan `click`.
- `sepeda/list.php` & `pelanggan/list.php`: tambah **pagination** (`LIMIT`/`OFFSET`, 5 baris/halaman). **Pencarian server-side** (`ILIKE`) dari Jobsheet 8 tetap dipakai dan kini ikut terbawa saat pindah halaman.
- `assets/js/sepeda.js` dan `pelanggan.js` (demo `fetch` ke file JSON, tidak dipakai di halaman mana pun) dihapus.

## Cara menjalankan
Siapkan database lebih dulu (lihat `sql/01_malangbike.sql`, nama database harus sama dengan yang ada di `includes/koneksi.php`, yaitu `01_malangbike`). Data awal bisa diisi lewat `migrasi_sepeda.php` dan `migrasi_pelanggan.php`.

**Opsi 1 — PHP built-in server**:
```bash
php -S localhost:8000
```
Buka `http://localhost:8000/index.php`, uji siklus lengkap: tambah → tampil → ubah (Edit) → tampil berubah → hapus → hilang dari list.

**Opsi 2 — Laragon (Apache)**: lewat virtual host langsung ke folder `Jobsheet09/` atau bersarang di bawah domain proyek — path CSS/JS/link sudah relatif otomatis (lihat `includes/header.php`), jadi keduanya jalan.

## Catatan
- Kolom pencarian (`#search-input`) melayani dua peran: filter instan client-side (JS) untuk baris yang sedang tampil di halaman saat ini, dan pencarian penuh lintas-halaman lewat tombol "Cari" (server-side).
- Semua data yang ditampilkan ke HTML (termasuk nilai `q` pada kolom pencarian) sudah dibungkus `htmlspecialchars()` sejak Jobsheet 8. Audit keamanan menyeluruh tetap dilakukan di Jobsheet 11.
