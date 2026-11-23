# 1. Konsep Dasar: Relasi Tabel & Transaction

Dua konsep besar yang benar-benar baru di jobsheet ini: **tabel yang saling terhubung** (relasi), dan **transaction** (kelompok perintah SQL yang harus berhasil/gagal bersama-sama).

## 1.1 Kenapa Penyewaan Butuh Tabel Sendiri?

Tabel `sepeda` dan `pelanggan` berdiri sendiri, tidak saling "tahu" satu sama lain. Tapi satu **transaksi penyewaan** menyatukan **dua** informasi sekaligus: *sepeda mana* yang disewa **dan** *pelanggan mana* yang menyewanya — plus informasi baru yang tidak dimiliki keduanya: kapan disewa, kapan (atau belum) dikembalikan, dan statusnya. Tabel `penyewaan` yang baru menyimpan **hubungan** itu.

## 1.2 Foreign Key: Menghubungkan Antar Tabel

```sql
sepeda_id INTEGER NOT NULL REFERENCES sepeda(id),
pelanggan_id INTEGER NOT NULL REFERENCES pelanggan(id),
```

**`REFERENCES sepeda(id)`** adalah **foreign key** (kunci asing): kolom `sepeda_id` **harus** berisi nilai yang benar-benar ada sebagai `id` di tabel `sepeda`. Ini melengkapi `PRIMARY KEY` yang sudah dikenal. Manfaatnya, database **sendiri** menolak pencatatan penyewaan dengan `sepeda_id` yang tidak pernah ada ("penyewaan sepeda hantu"), tanpa kode PHP tambahan. Foreign key juga bekerja ke arah sebaliknya: sepeda atau pelanggan yang **sudah punya riwayat penyewaan tidak bisa dihapus** (lihat [bab 2 §2.7](02-skema-penyewaan-dan-relasi.md#27-efek-foreign-key-pada-hapus)). Detail skema ada di [bab 2](02-skema-penyewaan-dan-relasi.md).

## 1.3 Kenapa Butuh Transaction?

Perhatikan alur mencatat penyewaan baru ([bab 3](03-penyewaan-baru-dan-transaction.md)). Prosesnya bukan sekadar satu `INSERT`, tapi satu **rangkaian** langkah yang harus utuh:
1. Kunci baris sepeda yang akan disewa.
2. Periksa sepeda dan pelanggan benar-benar ada, dan sepeda **belum** disewa orang lain.
3. `INSERT` baris baru ke tabel `penyewaan`.

Bayangkan langkah pemeriksaan (2) lolos, lalu sebelum langkah (3) selesai ada petugas lain yang menyewakan sepeda yang sama. Hasilnya: satu sepeda tercatat disewa **dua orang** sekaligus. Data jadi tidak konsisten.

**Transaction** mengelompokkan beberapa perintah SQL supaya **semuanya berhasil bersama-sama, atau semuanya dibatalkan bersama-sama** — tidak ada kondisi "setengah berhasil". Cara memakainya (`beginTransaction`, `commit`, `rollBack`) dibahas di [bab 3 §3.4](03-penyewaan-baru-dan-transaction.md#34-transaction-begintransaction-commit-rollback).

> Pada sistem yang menyimpan jumlah `stok`, transaction juga dipakai supaya `INSERT` transaksi dan `UPDATE` stok berhasil bersama. Di Malang Bike, setiap sepeda adalah satu unit sehingga ketersediaan **dihitung dari tabel `penyewaan`**, bukan dari kolom stok. Prinsip "semua berhasil atau semua batal" tetap berlaku untuk rangkaian cek-lalu-simpan di atas.

## 1.4 Apa itu Race Condition?

**Race condition** adalah masalah yang muncul ketika **dua proses berjalan hampir bersamaan** dan saling mengganggu hasil satu sama lain. Contoh di aplikasi ini: **dua** petugas (di komputer berbeda) **secara bersamaan** menyewakan sepeda yang **sama** ke pelanggan berbeda. Kalau kedua proses sama-sama membaca "sepeda ini belum disewa" **sebelum** salah satu sempat menyimpan, keduanya menganggap penyewaan itu sah — dan satu sepeda fisik tercatat dipakai dua orang.

Solusinya — **`SELECT ... FOR UPDATE`** — dibahas di [bab 3 §3.5](03-penyewaan-baru-dan-transaction.md#35-mencegah-race-condition-select--for-update).

## 1.5 Peta Modul Penyewaan

```
penyewaan/
├── tambah.php + proses_tambah.php   → mencatat penyewaan baru
├── kembali.php + proses_kembali.php → menandai selesai
└── riwayat.php                       → melihat histori per pelanggan
```

Ketiga halaman ini **sama-sama** butuh data dari **lebih dari satu** tabel (`penyewaan`, `sepeda`, `pelanggan`) — inilah kenapa relasi tabel ([§1.2](#12-foreign-key-menghubungkan-antar-tabel)) dan `JOIN` ([bab 5](05-riwayat-penyewaan-join.md)) menjadi penting, berbeda dari jobsheet sebelumnya yang tiap halaman biasanya hanya bekerja dengan **satu** tabel.

Lanjut ke: [Skema `penyewaan` & Relasi Antar Tabel](02-skema-penyewaan-dan-relasi.md)
