# 6. Rangkuman & Latihan Lanjutan

## 6.1 Rangkuman Keseluruhan Jobsheet 12

| Bagian | Konsep yang Dipelajari |
|---|---|
| [Konsep Dasar Integrasi & Transaction](01-konsep-dasar-integrasi-dan-transaksi.md) | Kenapa butuh tabel relasi, foreign key, transaction, race condition |
| [Skema `penyewaan`](02-skema-penyewaan-dan-relasi.md) | `REFERENCES`, tipe `DATE`, `DEFAULT CURRENT_DATE`, `CHECK`, indeks unik parsial, relasi many-to-many |
| [Penyewaan Baru](03-penyewaan-baru-dan-transaction.md) | `beginTransaction`/`commit`/`rollBack`, `SELECT ... FOR UPDATE`, `NOT EXISTS` |
| [Pengembalian](04-pengembalian-sepeda.md) | Transaction kebalikan, defense in depth (method + CSRF) |
| [Riwayat & `JOIN`](05-riwayat-penyewaan-join.md) | `JOIN ... ON`, alias tabel, perbandingan `(int) === (int)` |

## 6.2 Konsep Inti yang Perlu Diingat

1. **Tabel relasi (seperti `penyewaan`) menjembatani dua entitas** lewat foreign key, memungkinkan satu sepeda disewa banyak pelanggan dari waktu ke waktu, dan sebaliknya ([bab 1 §1.1-1.2](01-konsep-dasar-integrasi-dan-transaksi.md#11-kenapa-penyewaan-butuh-tabel-sendiri)).
2. **Transaction memastikan rangkaian perintah SQL berhasil/gagal bersama-sama** — mencegah data setengah-jadi ([bab 3 §3.4](03-penyewaan-baru-dan-transaction.md#34-transaction-begintransaction-commit-rollback)).
3. **`SELECT ... FOR UPDATE` mencegah race condition** dengan mengunci baris yang sedang diperiksa/diubah sampai transaction selesai ([bab 3 §3.5](03-penyewaan-baru-dan-transaction.md#35-mencegah-race-condition-select--for-update)).
4. **Aturan penting sebaiknya dijaga di dua tempat**: di kode PHP (pemeriksaan + kunci) **dan** di database (indeks unik parsial, `CHECK`, foreign key) ([bab 2 §2.6](02-skema-penyewaan-dan-relasi.md#26-indeks-unik-parsial-pengaman-terakhir)).
5. **`JOIN` menggabungkan data dari beberapa tabel** menjadi satu hasil query ([bab 5](05-riwayat-penyewaan-join.md)).
6. **Defense in depth** — pemeriksaan method HTTP **dan** token CSRF **dan** guard login lebih aman daripada satu lapisan saja ([bab 4 §4.3](04-pengembalian-sepeda.md#43-kenapa-guard-method-masih-diperiksa-padahal-sudah-ada-csrf)).

## 6.3 Cara Mencoba Sendiri

Ikuti alur pengujian end-to-end dari [README.md](../README.md) secara berurutan:

1. Jalankan skema tambahan dan server:
   ```bash
   psql -d 01_malangbike -f sql/02_penyewaan.sql
   php -S localhost:8000
   ```
2. **Registrasi** petugas baru → **Login**.
3. **Tambah Sepeda** dan **Tambah Pelanggan** (kalau belum ada).
4. Klik **Sewa Baru**, pilih pelanggan dan sepeda, simpan — amati sepeda itu **hilang dari dropdown** Sewa Baru.
5. Kembali ke **Beranda**, amati kartu "Sedang Disewa" **bertambah** (dihitung dari `COUNT(*) FROM penyewaan WHERE status = 'disewa'`).
6. Klik **Pengembalian**, cari transaksi yang baru dibuat, klik "Kembalikan" — transaksi **hilang** dari daftar aktif dan sepeda **muncul lagi** di dropdown Sewa Baru.
7. Klik **Riwayat**, pilih pelanggan yang sama — transaksi tadi **muncul**, berstatus **"Selesai"**.
8. Coba **hapus** sepeda yang pernah disewa — amati pesan "tidak bisa dihapus karena sudah memiliki riwayat penyewaan."
9. **Logout**, coba akses `/penyewaan/tambah.php` langsung — amati diarahkan ke Login (guard `auth.php` berlaku juga di modul baru ini).

## 6.4 Ide Latihan Tambahan (Opsional)

1. **Terapkan validasi bisnis yang belum ada** — pelanggan yang punya penyewaan aktif **lebih dari 7 hari** tidak boleh menyewa sepeda baru. Petunjuk: hitung `CURRENT_DATE - tanggal_sewa` pada penyewaan aktif pelanggan itu sebelum `INSERT` di `proses_tambah.php`.
2. **Hitung biaya sewa** — tambah kolom `total_biaya INTEGER` yang diisi saat pengembalian: `harga_sewa` sepeda dikali lama sewa (minimal 1 hari). Petunjuk: ambil `harga_sewa` lewat `JOIN` di `proses_kembali.php`, lalu tampilkan di Riwayat.
3. **Uji race condition secara manual** — buka dua tab/sesi berbeda, coba sewakan sepeda yang **sama** dari kedua tab hampir bersamaan. Amati hanya **satu** yang berhasil, yang lain mendapat pesan "Sepeda ini sedang disewa." (bukti `FOR UPDATE` bekerja). Lalu coba hapus pemeriksaan `COUNT(*)` di kode dan lihat indeks unik parsial tetap menolak penyewaan ganda.
4. **Tambah kolom di `JOIN`** — sertakan juga `no_telepon` pelanggan di daftar penyewaan aktif (`kembali.php`).
5. **Tampilkan status di Daftar Sepeda** — tambahkan kolom "Tersedia/Disewa" di `sepeda/list.php` memakai subquery `EXISTS`.

Kalau ada bagian yang masih membingungkan, terutama soal transaction dan `FOR UPDATE` di [bab 3](03-penyewaan-baru-dan-transaction.md), praktikkan latihan uji race condition di poin 3 — melihat sendiri permintaan kedua "menunggu" lalu ditolak adalah cara paling nyata untuk memahami kenapa penguncian baris itu penting.
