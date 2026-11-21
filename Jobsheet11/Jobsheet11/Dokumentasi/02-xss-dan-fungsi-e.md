# 2. XSS & Fungsi `e()`

## 2.1 Apa itu XSS?

**XSS (Cross-Site Scripting)** adalah celah yang memungkinkan penyerang menyisipkan **kode HTML/JavaScript miliknya sendiri** ke dalam halaman yang dilihat **pengguna lain** — bukan lewat meretas server, melainkan lewat data yang **tampak seperti data biasa** (misalnya merek sepeda), tapi sebenarnya berisi kode berbahaya.

## 2.2 Contoh Konkret Serangannya

Bayangkan seseorang mengisi form Tambah Sepeda dengan merek:
```
<script>alert('halaman ini sudah diretas!')</script>
```

Kalau `sepeda/list.php` mencetak merek seperti ini:
```php
<td><?php echo $s['merek']; ?></td>
```
maka `echo` akan mencetaknya **apa adanya** ke HTML — dan browser akan **menjalankan** tag `<script>` itu sebagai kode sungguhan, bukan menampilkannya sebagai teks. **Setiap** pengunjung yang membuka Daftar Sepeda ikut menjalankan skrip itu di browser mereka sendiri — inilah kenapa disebut "cross-site": satu input dari satu orang memengaruhi **semua** pengunjung lain.

Contoh `alert()` hanya demonstrasi. Skrip berbahaya sungguhan bisa mencuri cookie sesi, mengubah tampilan halaman untuk menipu (*phishing*), atau melakukan aksi atas nama pengguna yang sedang login.

## 2.3 Kondisi Sebelum Jobsheet Ini: Sudah Ada, Tapi Tersebar

Kode Malang Bike di Jobsheet 10 **sudah tidak mencetak data mentah**. Setiap halaman memanggil `htmlspecialchars()` langsung:

```php
<td><?php echo htmlspecialchars($s['merek']); ?></td>
```

Itu sudah mencegah contoh serangan di atas. Tapi ada tiga kelemahan:

1. **Tersebar** — puluhan pemanggilan `htmlspecialchars(...)` ditulis satu per satu. Satu kali lupa di halaman baru berarti satu celah baru.
2. **Pengaturan tidak eksplisit** — tanpa `ENT_QUOTES` dan `'UTF-8'`, hasilnya bergantung pada versi PHP di server. Di PHP lama, tanda kutip tunggal (`'`) tidak di-escape.
3. **Tidak aman terhadap `null`** — kolom opsional (misalnya `alamat`) yang kosong bisa menghasilkan peringatan di PHP versi baru.

## 2.4 Fungsi `e()`: Solusinya

```php
// includes/helpers.php
function e($value)
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}
```

- **`htmlspecialchars(...)`** mengubah karakter HTML khusus (`<`, `>`, `&`, `"`, `'`) menjadi **HTML entity**. `<` jadi `&lt;`, `>` jadi `&gt;`. Teks `<script>...</script>` berubah jadi `&lt;script&gt;...&lt;/script&gt;`, sehingga browser menampilkannya **persis seperti yang diketik**, bukan menjalankannya.
- **`(string) ($value ?? '')`** — `(string)` memaksa nilai apa pun diperlakukan sebagai teks, dan `?? ''` memberi string kosong kalau nilainya `null` (menyelesaikan kelemahan #3).
- **`ENT_QUOTES`** — meng-escape tanda kutip tunggal **dan** ganda. Penting untuk atribut `value="..."` yang juga diapit tanda kutip (lihat [§2.6](#26-kenapa-atribut-value-juga-butuh-e)).
- **`'UTF-8'`** — encoding karakter yang eksplisit, supaya karakter non-ASCII ditangani benar.

Hasilnya: satu fungsi pendek, aturan yang selalu sama, dan kebiasaan yang mudah diingat — **"mau cetak data? bungkus `e()`."**

## 2.5 Di Mana Saja `e()` Dipakai?

Semua pemanggilan `htmlspecialchars(...)` diganti `e(...)`. Bandingkan di `sepeda/list.php`:

**Sebelumnya (Jobsheet 10):**
```php
<td><?php echo htmlspecialchars($s['merek']); ?></td>
```

**Sekarang (Jobsheet 11):**
```php
<td><?php echo e($s['merek']); ?></td>
```

Pola yang sama berlaku di:
- **Daftar Sepeda & Pelanggan**: `kode_sepeda`, `merek`, `jenis`, `nama`, `alamat`, `no_telepon`.
- **Kolom pencarian**: `<input ... value="<?php echo e($keyword); ?>">` (`$keyword` berasal dari `$_GET['q']`).
- **Form Edit**: nilai lama yang diisi ke atribut `value="..."`.
- **Navbar**: `e($_SESSION['nama'] ?? 'Petugas')` — nama ini awalnya berasal dari form Registrasi, jadi tetap harus dianggap data yang berpotensi berbahaya.
- **Judul halaman & pesan flash** di `includes/header.php` (`e($page_title)`, `e($f['pesan'])`).

## 2.6 Kenapa Atribut `value` Juga Butuh `e()`?

```php
<input type="text" id="merek" name="merek" required value="<?php echo e($s['merek']); ?>">
```

Selain berbahaya kalau dicetak sebagai **isi** elemen (kasus `<td>`), teks yang mengandung tanda kutip ganda (`"`) juga berbahaya kalau dicetak **di dalam** atribut `value="..."`. Tanda kutip yang tidak di-escape bisa "menutup" atribut itu lebih awal dan memungkinkan penyerang menyisipkan atribut tambahan, misalnya `onmouseover="kodeJahat()"`. Inilah kenapa `ENT_QUOTES` penting.

## 2.7 Mengapa `harga_sewa` dan `created_at` Tidak Memakai `e()`?

```php
<td>Rp <?php echo number_format($s['harga_sewa'], 0, ',', '.'); ?></td>
<td><?php echo !empty($s['created_at']) ? date('d-m-Y H:i', strtotime($s['created_at'])) : '-'; ?></td>
```

Kolom `harga_sewa` bertipe `INTEGER` dan `created_at` bertipe `TIMESTAMP`. Nilainya **selalu** angka/tanggal murni, dan hasil `number_format()` serta `date()` sudah berupa teks hasil olahan PHP sendiri. Database menolak menyimpan teks seperti `<script>` ke kolom bertipe angka. Jadi `e()` hanya benar-benar penting untuk kolom bertipe teks (`VARCHAR`).

## 2.8 Cara Membuktikannya Sendiri

> tambah sepeda dengan merek `<script>alert(1)</script>` → di Daftar Sepeda harus tampil sebagai teks, bukan pop-up.

Tambahkan sepeda dengan merek persis `<script>alert(1)</script>`, lalu buka `sepeda/list.php`. Kamu akan melihat **teks** itu tertulis apa adanya di kolom Merek (bukan kotak pop-up) — bukti nyata `e()` bekerja.

Lanjut ke: [CSRF & Token Verifikasi](03-csrf-dan-token.md)
