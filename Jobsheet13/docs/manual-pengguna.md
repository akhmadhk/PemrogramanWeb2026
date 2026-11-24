# Manual Pengguna — Malang Bike

> Catatan: dokumen ini berisi langkah bertekstur lengkap sebagai pengganti tangkapan layar. Sisipkan screenshot nyata pada tiap langkah bertanda **[Screenshot]** saat didemokan.
>
> URL contoh di bawah pakai `http://localhost:8000/...` (PHP built-in server). Kalau menjalankan lewat Laragon atau layanan hosting, ganti bagian domainnya saja — path di tiap langkah sama persis.

## 1. Registrasi & Login Petugas

1. Buka `http://localhost:8000/auth/register.php`.
2. Isi Nama, Username, Password (minimal 6 karakter), klik **Daftar**. **[Screenshot: form registrasi]**
3. Setelah berhasil, kamu diarahkan ke halaman Login. Masukkan username dan password yang baru dibuat, klik **Masuk**. **[Screenshot: form login]**
4. Setelah berhasil login, navbar menampilkan nama petugas dan tombol **Logout**, serta menu tambahan (Tambah Sepeda, Tambah Pelanggan, Sewa Baru, Pengembalian, Riwayat) yang tidak terlihat oleh Tamu. **[Screenshot: navbar setelah login]**

## 2. Mengelola Data Sepeda

1. Klik menu **Tambah Sepeda**, isi Kode Sepeda, Merek, Jenis, dan Harga Sewa per Hari, klik **Simpan**. **[Screenshot: form tambah sepeda]**
2. Sepeda baru langsung tampil di **Daftar Sepeda**. Gunakan kolom pencarian untuk menyaring berdasarkan kode, merek, atau jenis. **[Screenshot: daftar sepeda]**
3. Klik **Edit** pada baris sepeda untuk mengubah data, atau **Hapus** untuk menghapus (akan muncul konfirmasi sebelum data benar-benar dihapus). Sepeda yang sudah pernah disewa tidak bisa dihapus karena riwayatnya harus tetap tersimpan.

## 3. Mengelola Data Pelanggan

Alurnya sama dengan Sepeda: menu **Tambah Pelanggan** → isi Nama, Alamat, dan No. Telepon → tampil di **Daftar Pelanggan** → Edit/Hapus tersedia per baris. Pelanggan yang sudah punya riwayat sewa tidak bisa dihapus.

## 4. Menyewakan Sepeda

1. Klik menu **Sewa Baru**.
2. Pilih **Pelanggan** dan **Sepeda** (hanya sepeda yang sedang tidak disewa yang muncul di pilihan), klik **Simpan Penyewaan**. **[Screenshot: form penyewaan]**
3. Kembali ke **Beranda**: kartu "Sedang Disewa" bertambah, dan sepeda tadi tidak muncul lagi di pilihan **Sewa Baru**.

## 5. Mengembalikan Sepeda

1. Klik menu **Pengembalian**.
2. Cari transaksi berdasarkan nama pelanggan, kode sepeda, atau merek (opsional), lalu klik **Kembalikan** pada baris yang sesuai. **[Screenshot: daftar pengembalian]**
3. Transaksi hilang dari daftar penyewaan aktif, dan sepeda itu muncul kembali di pilihan **Sewa Baru**.

## 6. Melihat Riwayat Penyewaan

1. Klik menu **Riwayat**.
2. Pilih nama pelanggan dari dropdown, klik **Tampilkan**.
3. Tabel menampilkan seluruh riwayat penyewaan pelanggan tersebut beserta status (Disewa/Selesai). **[Screenshot: riwayat penyewaan]**

## 7. Logout

Klik **Logout** di navbar kanan atas untuk mengakhiri sesi. Setelah logout, menu yang membutuhkan login (Tambah Sepeda, Tambah Pelanggan, Sewa Baru, Pengembalian, Riwayat) hilang dari navbar dan halamannya tidak bisa diakses langsung (otomatis diarahkan ke halaman Login).
