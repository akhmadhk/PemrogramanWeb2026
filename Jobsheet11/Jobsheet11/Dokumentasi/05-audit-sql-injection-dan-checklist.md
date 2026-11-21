# 5. Audit SQL Injection & Security Checklist

Bab ini membahas kerentanan yang **ternyata sudah aman** dari jobsheet sebelumnya, dan bagaimana `docs/security-checklist.md` mendokumentasikan seluruh audit secara terstruktur.

## 5.1 Apa itu SQL Injection?

**SQL Injection** adalah celah yang memungkinkan penyerang **mengubah makna** sebuah query SQL dengan menyisipkan potongan SQL lewat input form. Kalau query dibangun dengan **menggabungkan** teks input langsung (`"... WHERE username = '" . $username . "'"`), input yang berisi tanda kutip dan potongan SQL bisa mengacaukan struktur query aslinya.

## 5.2 Kenapa Sudah Aman Sejak Jobsheet 8?

**Setiap** query di Malang Bike — `INSERT`, `SELECT ... WHERE`, `UPDATE`, `DELETE`, termasuk pencarian `ILIKE` — ditulis memakai **prepared statement** (`$pdo->prepare()` + placeholder `:parameter` + `execute([...])`), bukan menggabung string. Nilai dari `$_POST`/`$_GET` **tidak pernah** menjadi bagian perintah SQL; selalu murni **data**. Audit memeriksa ulang semua query di `sepeda/`, `pelanggan/`, dan `auth/` dan tidak menemukan satu pun yang menyimpang.

Perhatikan pencarian di `sepeda/list.php`:
```php
$stmt = $pdo->prepare("
    SELECT * FROM sepeda
    WHERE merek ILIKE :q1 OR jenis ILIKE :q2 OR kode_sepeda ILIKE :q3
    ORDER BY id DESC
");
$like = "%$keyword%";
$stmt->execute(['q1' => $like, 'q2' => $like, 'q3' => $like]);
```
Tanda `%` ikut menjadi bagian **nilai** yang dikirim, bukan bagian SQL.

## 5.3 Mencoba Sendiri: Uji `' OR '1'='1`

`' OR '1'='1` adalah contoh klasik percobaan SQL injection. Pada query yang tidak aman seperti:
```php
// CONTOH TIDAK AMAN — bukan cara aplikasi ini menulis query
"SELECT * FROM users WHERE username = '" . $username . "'"
```
input itu bisa mengubah query menjadi "cocokkan baris mana pun" (karena `'1'='1'` selalu benar).

Query login Malang Bike ditulis:
```php
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
$stmt->execute(['username' => $username]);
```
Isi `$username` — termasuk `' OR '1'='1` — diperlakukan sebagai **satu nilai teks utuh**. Coba login dengan username itu: hasilnya tetap "Username atau password salah!".

## 5.4 Audit Validasi & Sanitasi Input

Validasi wajib-isi (`=== ''`), angka (`(int)` dan cek `<= 0`), dan minimal password (`strlen`) **sudah ada** sejak jobsheet sebelumnya. Kode juga sudah memakai `(int)` pada `id`:

```php
// sepeda/edit.php
$id = (int)($_GET['id'] ?? 0);
// ...
<input type="hidden" name="id" value="<?php echo $id; ?>">
```

`$id` yang dicetak ke `value="..."` sudah pasti angka murni. Satu perubahan kecil di jobsheet ini: `hapus.php` kini membaca `(int)($_POST['id'] ?? 0)` (sebelumnya `(int)($_GET['id'] ?? 0)`), tetap dengan casting `(int)`.

## 5.5 Membaca `docs/security-checklist.md` Secara Utuh

| Kolom | Isinya |
|---|---|
| `#` | Nomor urut kerentanan |
| `Kerentanan` | Nama celah keamanan yang diperiksa |
| `Ditemukan di` | File-file yang relevan/terdampak |
| `Sebelum` | Kondisi kode **sebelum** audit/perbaikan |
| `Sesudah (perbaikan)` | Apa yang dilakukan, plus **bukti** bahwa perbaikan itu bekerja |

Pola "Sebelum vs Sesudah, disertai bukti pengujian konkret" adalah kebiasaan penting dalam pekerjaan keamanan: klaim "sudah aman" tanpa bukti tidak banyak nilainya. Setiap baris bisa kamu verifikasi ulang sendiri (`' OR '1'='1` untuk SQL injection, `<script>alert(1)</script>` untuk XSS, `curl` tanpa token untuk CSRF).

Bagian "Catatan" di akhir dokumen mencatat **keputusan desain** (urutan `auth.php` sebelum `csrf.php`, tombol Hapus jadi `POST`) dan **temuan tambahan** (#6: password akun `admin` bawaan masih berupa teks biasa) sebagai bagian resmi dari audit.

Lanjut ke: [Rangkuman & Latihan Lanjutan](06-rangkuman-latihan.md)
