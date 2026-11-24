# 3. Dokumentasi Proyek: README, ERD, & Manual Pengguna

Bab ini membahas dokumen-dokumen **penutup** proyek — bukan kode program, tapi tulisan yang menjelaskan proyek secara keseluruhan untuk pembaca yang berbeda-beda: developer lain, penguji, dan pengguna akhir.

## 3.1 `README.md` sebagai "Kartu Identitas" Proyek

Di setiap jobsheet sebelumnya, `README.md` berisi "catatan perubahan dari jobsheet sebelumnya". Di Jobsheet 13, `README.md` ditulis ulang sebagai **snapshot akhir** proyek: siapa pun yang baru pertama kali membuka proyek ini (tanpa membaca 12 jobsheet sebelumnya) harus bisa memahami **apa** aplikasi ini, **teknologi** apa yang dipakai, dan **bagaimana** menjalankannya, hanya dari membaca `README.md`. Bagian "Perubahan dari Jobsheet 12" tetap ada, tetapi singkat.

## 3.2 ERD Final: Empat Tabel

```
sepeda            pelanggan         users              penyewaan
------            ---------         ------             ---------
id (PK)           id (PK)           id (PK)             id (PK)
kode_sepeda (UQ)  nama              username (UQ)        sepeda_id (FK -> sepeda.id)
merek             alamat            password             pelanggan_id (FK -> pelanggan.id)
jenis             no_telepon        nama                 tanggal_sewa
harga_sewa        created_at                             tanggal_kembali
created_at                                               status
```

**ERD** (*Entity Relationship Diagram*) menggambarkan **seluruh** tabel dalam database dan bagaimana mereka saling terhubung — versi "peta lengkap" dari skema yang kamu bangun **bertahap**: tabel `sepeda` dan `pelanggan` (Jobsheet 8), `users` (Jobsheet 10), dan `penyewaan` (Jobsheet 12). Notasinya:

- **`(PK)`** — *Primary Key*, kunci utama tiap tabel.
- **`(UQ)`** — *Unique*, batasan `UNIQUE` (tidak boleh ada dua baris dengan nilai sama).
- **`(FK -> tabel.kolom)`** — *Foreign Key*, kolom yang menunjuk ke Primary Key tabel lain.

Menuliskan ERD **satu kali secara utuh** di README (dibanding terpencar di berbagai jobsheet) memudahkan siapa pun memahami **struktur data proyek** dalam sekali baca — berguna terutama saat proyek diserahterimakan ke orang lain atau dipresentasikan.

## 3.3 Matriks Fitur per Role

```markdown
| Fitur | Tamu (tanpa login) | Petugas (login) |
|---|---|---|
| Lihat Beranda & statistik | Ya | Ya |
| Lihat Daftar Sepeda | Ya | Ya |
| Tambah/Edit/Hapus Sepeda | Tidak | Ya |
| ...
```

Tabel ini merangkum **satu kali** seluruh aturan otorisasi yang kamu bangun sejak Jobsheet 10 (guard `auth.php`). Alih-alih menelusuri kode satu per satu untuk tahu halaman mana yang butuh login, tabel ini menjawabnya dalam sekali lihat. Ini contoh **dokumentasi yang berfungsi sebagai ringkasan/indeks**, bukan mengulang penjelasan kode secara detail.

Isi tabel ini cocok dengan kode: `sepeda/list.php` dan `pelanggan/list.php` tidak memakai guard (Tamu boleh melihat), sedangkan `tambah.php`, `edit.php`, `hapus.php`, `proses_*.php`, dan seluruh isi folder `penyewaan/` memakai `includes/auth.php`.

## 3.4 Instruksi Instalasi yang Berurutan

```bash
createdb 01_malangbike
psql -d 01_malangbike -f sql/01_malangbike.sql
psql -d 01_malangbike -f sql/02_penyewaan.sql
```

Perhatikan catatan di README: *"urutan penting karena `penyewaan` mereferensikan `sepeda`/`pelanggan`."* `REFERENCES sepeda(id)` di tabel `penyewaan` **mensyaratkan** tabel `sepeda` sudah ada lebih dulu — kalau `02_penyewaan.sql` dijalankan **sebelum** `01_malangbike.sql`, PostgreSQL menolak membuat tabel `penyewaan`. Urutan penomoran file (`01_`, `02_`) sengaja mencerminkan urutan eksekusi yang benar.

Satu hal kecil yang dirapikan: baris komentar di `sql/01_malangbike.sql` dulu menyebut nama database `malangbike`, padahal aplikasi (`config.php`) memakai `01_malangbike`. Komentarnya kini disamakan supaya perintah `createdb` di dokumen dan di kode cocok.

## 3.5 `docs/manual-pengguna.md`: Dokumentasi untuk Pengguna Akhir, Bukan Developer

Jenis dokumentasi ini **berbeda tujuannya** dari dokumentasi jobsheet yang sedang kamu baca. Bandingkan:

| | Dokumentasi Jobsheet (seperti ini) | `manual-pengguna.md` |
|---|---|---|
| Pembaca sasaran | Mahasiswa/developer yang ingin memahami **kode** | Petugas rental sepeda yang memakai **aplikasinya** |
| Isinya | Penjelasan `<?php ... ?>`, query SQL, konsep pemrograman | Langkah klik-per-klik: "Klik menu X, isi Y, klik Z" |
| Contoh | *"`password_hash()` mengubah teks jadi hash..."* | *"Isi Nama, Username, Password, klik **Daftar**."* |

Perhatikan potongan `manual-pengguna.md`:

```markdown
## 4. Menyewakan Sepeda

1. Klik menu **Sewa Baru**.
2. Pilih **Pelanggan** dan **Sepeda** (hanya sepeda yang sedang tidak
   disewa yang muncul di pilihan), klik **Simpan Penyewaan**.
3. Kembali ke **Beranda**: kartu "Sedang Disewa" bertambah.
```

Tidak ada istilah pemrograman di sini — murni instruksi penggunaan dari sudut pandang pengguna, mencerminkan **hasil akhir** dari kode (transaction, `FOR UPDATE`, dan seterusnya) yang sudah kamu bedah di dokumentasi Jobsheet 12, tanpa pengguna perlu tahu apa pun tentang implementasinya.

## 3.6 Placeholder `[Screenshot]`

```markdown
> Catatan: dokumen ini berisi langkah bertekstur lengkap sebagai
> pengganti tangkapan layar. Sisipkan screenshot nyata pada tiap
> langkah bertanda **[Screenshot]** saat didemokan.
```

Manual pengguna yang ideal menyertakan tangkapan layar sungguhan di tiap langkah (memudahkan pengguna mencocokkan apa yang mereka lihat), tapi gambar itu belum ada. Alih-alih berpura-pura sudah lengkap, penanda `[Screenshot: ...]` secara eksplisit menandai **bagian yang belum selesai**, dengan instruksi jelas apa yang perlu dilengkapi nanti — kebiasaan menulis dokumentasi yang jujur dan mudah ditindaklanjuti.

Lanjut ke: [Rangkuman & Latihan Lanjutan](04-rangkuman-latihan.md)
