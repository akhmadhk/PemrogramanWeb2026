# 2. Skema `penyewaan` & Relasi Antar Tabel

## 2.1 Kode Lengkap `sql/02_penyewaan.sql`

```sql
CREATE TABLE IF NOT EXISTS penyewaan (
    id SERIAL PRIMARY KEY,
    sepeda_id INTEGER NOT NULL REFERENCES sepeda(id),
    pelanggan_id INTEGER NOT NULL REFERENCES pelanggan(id),
    tanggal_sewa DATE NOT NULL DEFAULT CURRENT_DATE,
    tanggal_kembali DATE,
    status VARCHAR(20) NOT NULL DEFAULT 'disewa'
        CHECK (status IN ('disewa', 'dikembalikan'))
);

CREATE UNIQUE INDEX IF NOT EXISTS uq_penyewaan_sepeda_aktif
    ON penyewaan (sepeda_id) WHERE status = 'disewa';
```

Elemen `SERIAL PRIMARY KEY`, `NOT NULL`, `VARCHAR`, `DEFAULT`, dan `CHECK` sudah dikenal dari skema sebelumnya. Mari bedah bagian-bagian yang baru.

## 2.2 Kolom Foreign Key: `sepeda_id` dan `pelanggan_id`

Perhatikan pola penamaan: **`sepeda_id`** = nama tabel yang dirujuk (`sepeda`) + `_id`. Konvensi ini membuat nama kolom saja sudah cukup untuk menebak tabel yang dirujuk. Begitu pula `pelanggan_id` yang merujuk ke `pelanggan(id)`.

## 2.3 Tipe Data `DATE` dan `DEFAULT CURRENT_DATE`

- **`DATE`** — menyimpan **tanggal** saja (tanpa jam), cocok untuk "tanggal berapa".
- **`DEFAULT CURRENT_DATE`** — `CURRENT_DATE` adalah fungsi bawaan PostgreSQL yang bernilai "tanggal hari ini". Kalau `INSERT` tidak menyebut `tanggal_sewa`, PostgreSQL mengisinya otomatis. Ini mewujudkan wireframe: *"Tanggal Sewa: [ auto: hari ini ]"*.
- **`tanggal_kembali DATE`** (tanpa `NOT NULL`) — **boleh kosong** (`NULL`), karena saat sepeda baru disewa tanggal kembalinya belum diketahui. Kolom ini baru diisi saat Pengembalian ([bab 4](04-pengembalian-sepeda.md)).

## 2.4 Kolom `status` dan `CHECK`

```sql
status VARCHAR(20) NOT NULL DEFAULT 'disewa'
    CHECK (status IN ('disewa', 'dikembalikan'))
```

Dua nilai yang mungkin: `'disewa'` (default saat baru dibuat) dan `'dikembalikan'` (diubah saat Pengembalian). **`CHECK (... IN (...))`** membuat database menolak nilai lain (misalnya salah ketik `'disewaa'`). Kolom ini mewujudkan kolom "Status" di wireframe Riwayat.

## 2.5 Diagram Relasi Sederhana

```
sepeda                  penyewaan                    pelanggan
┌─────────────┐         ┌──────────────────┐         ┌────────────┐
│ id (PK)     │◄────────│ sepeda_id (FK)    │         │ id (PK)    │
│ kode_sepeda │         │ pelanggan_id (FK)─┼────────►│ nama       │
│ merek       │         │ tanggal_sewa      │         │ alamat     │
│ harga_sewa  │         │ tanggal_kembali   │         │ no_telepon │
│ ...         │         │ status            │         │ ...        │
└─────────────┘         └──────────────────┘         └────────────┘
```

- **PK** (*Primary Key*) — kunci utama tiap tabel.
- **FK** (*Foreign Key*) — kolom yang **menunjuk** ke Primary Key tabel lain.

Tabel `penyewaan` berada **di tengah**, menjadi "jembatan" antara satu baris `sepeda` dan satu baris `pelanggan` — **relasi banyak-ke-banyak** (*many-to-many*): satu sepeda bisa disewa banyak pelanggan berbeda **dari waktu ke waktu** (satu per satu), dan satu pelanggan bisa menyewa banyak sepeda berbeda.

## 2.6 Indeks Unik Parsial: Pengaman Terakhir

```sql
CREATE UNIQUE INDEX IF NOT EXISTS uq_penyewaan_sepeda_aktif
    ON penyewaan (sepeda_id) WHERE status = 'disewa';
```

- **`UNIQUE INDEX ... ON penyewaan (sepeda_id)`** — melarang dua baris dengan `sepeda_id` yang sama.
- **`WHERE status = 'disewa'`** — larangan itu **hanya berlaku** untuk baris berstatus `'disewa'` (inilah yang disebut indeks *parsial*). Hasilnya: satu sepeda boleh punya **banyak** riwayat `'dikembalikan'`, tapi hanya **satu** penyewaan aktif.

Indeks ini adalah lapisan pertahanan di level database. Kode PHP di [bab 3](03-penyewaan-baru-dan-transaction.md) sudah memeriksa dan mengunci, tetapi kalau suatu hari ada kode lain yang lupa memeriksa, **database tetap menolak** penyewaan ganda.

## 2.7 Efek Foreign Key pada Hapus

Karena `penyewaan` merujuk ke `sepeda` dan `pelanggan`, PostgreSQL menolak `DELETE` sepeda/pelanggan yang masih punya riwayat penyewaan (kode error `23503`). Di `sepeda/hapus.php` dan `pelanggan/hapus.php`, error ini ditangkap dan diganti pesan ramah:

```php
} catch (PDOException $e) {
    // 23503 = pelanggaran FOREIGN KEY: data ini masih dirujuk tabel penyewaan
    $pesan = $e->getCode() === '23503'
        ? 'Data sepeda tidak bisa dihapus karena sudah memiliki riwayat penyewaan.'
        : 'Gagal menghapus: ' . $e->getMessage();
    $_SESSION['flash'] = ['type' => 'danger', 'pesan' => $pesan];
}
```

Perilaku ini disengaja: riwayat transaksi tidak boleh hilang hanya karena sepedanya dihapus.

## 2.8 Menjalankan Skema Tambahan

```bash
psql -d 01_malangbike -f sql/02_penyewaan.sql
```

`CREATE TABLE IF NOT EXISTS` dan `CREATE UNIQUE INDEX IF NOT EXISTS` aman dijalankan kapan pun tanpa mengganggu tabel `sepeda`/`pelanggan`/`users` yang sudah ada. **Jalankan sebelum membuka Beranda**, karena Beranda sekarang membaca tabel `penyewaan`.

Lanjut ke: [Penyewaan Baru & Database Transaction](03-penyewaan-baru-dan-transaction.md)
