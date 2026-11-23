# 3. Penyewaan Baru & Database Transaction

Bab paling teknis di jobsheet ini — menggabungkan transaction database dengan penguncian baris (`FOR UPDATE`) untuk mencegah race condition.

## 3.1 `penyewaan/tambah.php`: Menyiapkan Dropdown

```php
$daftarPelanggan = $pdo->query("SELECT * FROM pelanggan ORDER BY nama")->fetchAll(PDO::FETCH_ASSOC);

$daftarSepedaTersedia = $pdo->query("
    SELECT * FROM sepeda s
    WHERE NOT EXISTS (
        SELECT 1 FROM penyewaan p WHERE p.sepeda_id = s.id AND p.status = 'disewa'
    )
    ORDER BY kode_sepeda
")->fetchAll(PDO::FETCH_ASSOC);
```

Kode ini mewujudkan aturan bisnis: *"Sepeda yang sedang disewa tidak boleh dipilih di form penyewaan."*

- **`NOT EXISTS (SELECT 1 FROM penyewaan ...)`** — "ambil sepeda **yang tidak punya** baris penyewaan aktif". Subquery di dalam `EXISTS` hanya dipakai untuk mengecek ada-tidaknya baris, sehingga isi `SELECT 1` tidak penting.
- **`s` dan `p`** adalah alias tabel (dibahas di [bab 5 §5.3](05-riwayat-penyewaan-join.md#53-alias-tabel-p-dan-s)). `p.sepeda_id = s.id` menghubungkan subquery dengan sepeda di query luar.
- Tidak ada kolom `stok`: sepeda dianggap **tersedia** selama tidak sedang disewa.

```php
<?php if (empty($daftarPelanggan)): ?>
    <p class="flash flash-danger">Belum ada data pelanggan. Tambahkan pelanggan terlebih dahulu.</p>
<?php elseif (empty($daftarSepedaTersedia)): ?>
    <p class="flash flash-danger">Tidak ada sepeda yang tersedia saat ini (semua sedang disewa).</p>
<?php else: ?>
    <form>...</form>
<?php endif; ?>
```

Pola percabangan **3 arah** (`if`/`elseif`/`else`). Kalau tidak ada pelanggan, atau tidak ada sepeda tersedia, form **tidak ditampilkan** — hanya pesan yang relevan. Form muncul kalau **kedua** syarat terpenuhi.

## 3.2 Menampilkan Pilihan yang Informatif

```php
<option value="<?php echo (int)$pl['id']; ?>">
    <?php echo e($pl['nama']); ?> (<?php echo e($pl['no_telepon']); ?>)
</option>
```

**`value`** berisi `id` (angka, dipakai server), sementara **teks yang tampil** menggabungkan nama **dan** nomor telepon. Kalau ada dua pelanggan bernama sama, nomor telepon membantu Petugas memilih **orang yang benar**. Dropdown sepeda memakai pola serupa: kode, merek, jenis, dan harga per hari. Setiap data teks dari database dibungkus `e()` (lihat Jobsheet 11).

## 3.3 `proses_tambah.php`: Kerangka Besarnya

```php
session_start();
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/csrf.php';
require __DIR__ . '/../includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: tambah.php');
    exit;
}

csrf_verify();

$pelangganId = (int)($_POST['pelanggan_id'] ?? 0);
$sepedaId    = (int)($_POST['sepeda_id'] ?? 0);

if ($pelangganId <= 0 || $sepedaId <= 0) {
    $_SESSION['flash'] = ['type' => 'danger', 'pesan' => 'Pelanggan dan sepeda wajib dipilih.'];
    header('Location: tambah.php');
    exit;
}

try {
    $pdo->beginTransaction();
    // ...
    $pdo->commit();
    // redirect sukses
} catch (PDOException $e) { /* rollBack, pesan umum */ }
  catch (Exception $e)    { /* rollBack, pesan dari throw */ }
```

Kerangka luarnya sudah dikenal: guard `auth.php`, `csrf_verify()` (urutan guard sebelum token, lihat Jobsheet 11), dan validasi wajib-isi dengan casting `(int)`. Yang baru adalah blok `try { ... beginTransaction ... commit } catch { ... rollBack }`.

## 3.4 Transaction: `beginTransaction`, `commit`, `rollBack`

- **`$pdo->beginTransaction();`** — menandai **awal** sekelompok perintah SQL yang harus berhasil/gagal bersama. Setelah ini, perubahan **belum permanen**; baru "dicoba".
- **`$pdo->commit();`** — membuat seluruh perubahan sejak `beginTransaction()` **permanen sekaligus**. Dipanggil **hanya** kalau semuanya berhasil.
- **`$pdo->rollBack();`** — **membatalkan total** perubahan sejak `beginTransaction()`, seolah tidak pernah terjadi. Dipanggil di blok `catch`.
- **`if ($pdo->inTransaction())`** — pengaman: `rollBack()` hanya dipanggil kalau transaction memang sedang berjalan (kalau `beginTransaction()` sendiri gagal, tidak ada yang perlu dibatalkan, dan memanggil `rollBack()` justru memunculkan error baru).
- **Dua blok `catch`**: `PDOException` (masalah database) ditulis ke `error_log` dan pengguna hanya melihat pesan umum, supaya detail SQL tidak bocor ke layar. `Exception` (yang dilempar dengan `throw` di kode kita sendiri) boleh menampilkan pesan aslinya karena isinya kalimat yang memang ditujukan untuk pengguna.

## 3.5 Mencegah Race Condition: `SELECT ... FOR UPDATE`

```php
$cek = $pdo->prepare("SELECT id FROM sepeda WHERE id = :id FOR UPDATE");
$cek->execute(['id' => $sepedaId]);
if (!$cek->fetch()) {
    throw new Exception('Sepeda tidak ditemukan.');
}

// ... cek pelanggan ...

$aktif = $pdo->prepare("SELECT COUNT(*) FROM penyewaan WHERE sepeda_id = :id AND status = 'disewa'");
$aktif->execute(['id' => $sepedaId]);
if ($aktif->fetchColumn() > 0) {
    throw new Exception('Sepeda ini sedang disewa.');
}
```

- **`FOR UPDATE`** **mengunci** baris sepeda yang diambil, mencegah transaction **lain** mengunci baris yang sama **sampai** transaction ini selesai (`commit`/`rollBack`). Ingat skenario di [bab 1 §1.4](01-konsep-dasar-integrasi-dan-transaksi.md#14-apa-itu-race-condition): dua petugas menyewakan sepeda yang sama bersamaan. Petugas **kedua** harus **menunggu** sampai transaction petugas **pertama** selesai. Begitu giliran petugas kedua, hitungan penyewaan aktif sudah `1`, sehingga ia ditolak dengan pesan "Sepeda ini sedang disewa."
- **`throw new Exception(...)`** — "melempar" error secara manual. PHP langsung **melompat** ke blok `catch`, yang memanggil `rollBack()` sebelum ada data yang berubah.
- Mengunci baris **sepeda** (bukan baris `penyewaan`) tepat karena yang diperebutkan adalah sepeda itu. Baris `penyewaan` baru belum ada saat pemeriksaan dilakukan.

## 3.6 Mencatat Penyewaan

```php
$insert = $pdo->prepare(
    "INSERT INTO penyewaan (sepeda_id, pelanggan_id, tanggal_sewa, status)
     VALUES (:sepeda_id, :pelanggan_id, CURRENT_DATE, 'disewa')"
);
$insert->execute(['sepeda_id' => $sepedaId, 'pelanggan_id' => $pelangganId]);

$pdo->commit();
```

- `INSERT` memakai prepared statement. `CURRENT_DATE` ditulis **langsung** di query (bukan parameter) — kolom `tanggal_sewa` sebenarnya sudah punya `DEFAULT CURRENT_DATE`, tetapi menuliskannya eksplisit membuat maksud kode lebih jelas.
- **`$pdo->commit();`** dipanggil **hanya** setelah seluruh pemeriksaan lolos dan `INSERT` berhasil. Pada saat inilah penyewaan benar-benar permanen dan kunci baris sepeda dilepas.
- Setelah `commit()`, pengguna diarahkan ke Beranda, tempat kartu **"Sedang Disewa"** bertambah dan pesan "Penyewaan berhasil dicatat." tampil (Beranda memanggil `tampil_flash()`).

Lanjut ke: [Pengembalian Sepeda](04-pengembalian-sepeda.md)
