# 5. Riwayat Penyewaan & `JOIN`

Halaman paling sederhana secara alur (hanya membaca data, tidak ada transaction), tapi memperkenalkan konsep SQL penting: **`JOIN`**.

## 5.1 Apa itu `JOIN`?

Tabel `penyewaan` hanya menyimpan **angka** (`sepeda_id`, `pelanggan_id`), bukan kode sepeda atau nama pelanggan. Kalau kamu hanya menjalankan `SELECT * FROM penyewaan`, hasilnya deretan angka yang tidak berarti bagi pengguna ("sepeda_id: 3, pelanggan_id: 7").

**`JOIN`** adalah perintah SQL yang **menggabungkan** baris dari dua tabel (atau lebih) berdasarkan kecocokan kolom tertentu — biasanya foreign key dengan primary key yang dirujuknya ([bab 2 §2.2](02-skema-penyewaan-dan-relasi.md#22-kolom-foreign-key-sepeda_id-dan-pelanggan_id)). Hasilnya: satu baris gabungan berisi kolom dari **kedua** tabel sekaligus.

## 5.2 Kode Query Lengkap

```php
$stmt = $pdo->prepare(
    "SELECT s.kode_sepeda, s.merek, s.jenis, p.tanggal_sewa, p.tanggal_kembali, p.status
     FROM penyewaan p
     JOIN sepeda s ON s.id = p.sepeda_id
     WHERE p.pelanggan_id = :id
     ORDER BY p.tanggal_sewa DESC, p.id DESC"
);
```

## 5.3 Alias Tabel: `p` dan `s`

```sql
FROM penyewaan p
JOIN sepeda s ON s.id = p.sepeda_id
```

**`penyewaan p`** dan **`sepeda s`** memberi tabel sebuah **alias** (nama pendek sementara). Ini murni untuk **mempersingkat penulisan**: tanpa alias, setiap kolom harus ditulis lengkap (`penyewaan.tanggal_sewa` alih-alih `p.tanggal_sewa`), yang membuat query panjang sulit dibaca — apalagi `kembali.php` yang menggabungkan **3** tabel (`p`, `s`, `pl`).

## 5.4 Klausa `ON`: Syarat Penggabungan

```sql
JOIN sepeda s ON s.id = p.sepeda_id
```

**`ON s.id = p.sepeda_id`** adalah **syarat** pencocokan: "gabungkan baris `sepeda` dengan baris `penyewaan` **kalau** `id` di tabel `sepeda` sama dengan `sepeda_id` di tabel `penyewaan`." Inilah wujud konkret relasi foreign key — `JOIN ... ON` "menerjemahkan" hubungan antar tabel menjadi data gabungan yang bisa ditampilkan.

## 5.5 Memilih Kolom dari Kedua Tabel

```sql
SELECT s.kode_sepeda, s.merek, s.jenis, p.tanggal_sewa, p.tanggal_kembali, p.status
```

Kolom yang diambil **bercampur**: `s.kode_sepeda`, `s.merek`, `s.jenis` dari tabel `sepeda`, sementara `p.tanggal_sewa`, `p.tanggal_kembali`, `p.status` dari tabel `penyewaan`. Satu baris hasil terlihat seperti "satu tabel gabungan", meski di database aslinya tersimpan terpisah.

## 5.6 Menyaring Berdasarkan Pelanggan Terpilih

```php
$pelangganId = (int)($_GET['pelanggan_id'] ?? 0);
// ...
if ($pelangganId > 0) {
    // ...
    $stmt->execute(['id' => $pelangganId]);
    $riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
```

- **`WHERE p.pelanggan_id = :id`** menyaring hasil `JOIN` supaya **hanya** menampilkan riwayat **satu** pelanggan, sesuai wireframe *"Riwayat Penyewaan — Akhmad"*.
- **`$_GET['pelanggan_id']`** diambil dari URL, dikirim lewat form `method="get"` berisi dropdown pelanggan. Nilainya langsung di-cast `(int)`, sehingga nilai non-angka otomatis menjadi `0`.
- Kalau `$pelangganId` masih `0` (halaman baru dibuka), query `JOIN` **tidak dijalankan** — `$riwayat` tetap array kosong dan halaman hanya menampilkan form pemilihan.
- Pelanggan yang dipilih dicari lebih dulu di tabel `pelanggan`; kalau `id` tidak ada, tabel riwayat tidak ditampilkan.

## 5.7 Mempertahankan Pilihan Dropdown

```php
<option value="<?php echo (int)$pl['id']; ?>" <?php echo $pelangganId === (int)$pl['id'] ? 'selected' : ''; ?>>
```

Atribut **`selected`** menandai opsi yang **sedang dipilih** supaya tetap terpilih setelah halaman dimuat ulang (mirip `value="..."` yang mempertahankan isi kotak pencarian). Perbandingan memakai `===` yang **ketat** (tipe **dan** nilai harus sama). Karena `$pelangganId` sudah di-cast `(int)` dan `$pl['id']` juga di-cast `(int)` di sisi kanan, kedua sisi sama-sama bertipe integer sehingga perbandingan berjalan benar. (Tanpa casting, `"3" === 3` bernilai `false` karena tipenya berbeda, walau nilainya "sama".)

## 5.8 Menampilkan Status yang Ramah Dibaca

```php
<td><?php echo $r['status'] === 'disewa' ? 'Disewa' : 'Selesai'; ?></td>
```

Kolom `status` di database menyimpan nilai teknis (`'disewa'` atau `'dikembalikan'`, lihat [bab 2 §2.4](02-skema-penyewaan-dan-relasi.md#24-kolom-status-dan-check)), tapi ke pengguna ditampilkan label ramah: "Disewa" atau "Selesai" — sesuai wireframe Riwayat. Tanggal juga diformat `d-m-Y` lewat `date('d-m-Y', strtotime(...))`, dan `tanggal_kembali` yang masih kosong ditampilkan sebagai `-`.

Lanjut ke: [Rangkuman & Latihan Lanjutan](06-rangkuman-latihan.md)
