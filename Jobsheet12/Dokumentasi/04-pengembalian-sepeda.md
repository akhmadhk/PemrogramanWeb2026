# 4. Pengembalian Sepeda

Modul ini adalah **kebalikan** logis dari Penyewaan Baru ([bab 3](03-penyewaan-baru-dan-transaction.md)) — memakai pola transaction yang sama, hanya arah perubahannya dibalik.

## 4.1 `penyewaan/kembali.php`: Daftar Transaksi Aktif

```php
$sqlDasar = "SELECT p.id, s.kode_sepeda, s.merek, pl.nama, p.tanggal_sewa
             FROM penyewaan p
             JOIN sepeda s ON s.id = p.sepeda_id
             JOIN pelanggan pl ON pl.id = p.pelanggan_id
             WHERE p.status = 'disewa'";
```

Query ini memakai **`JOIN`** (dibahas di [bab 5](05-riwayat-penyewaan-join.md)) untuk menggabungkan **3 tabel** sekaligus (`penyewaan`, `sepeda`, `pelanggan`) supaya bisa menampilkan **kode/merek sepeda** dan **nama pelanggan**, padahal tabel `penyewaan` hanya menyimpan `sepeda_id`/`pelanggan_id` berupa angka. **`WHERE p.status = 'disewa'`** memastikan **hanya** transaksi yang belum dikembalikan yang muncul.

Pencarian di halaman ini memakai tiga kolom sekaligus:

```php
$sqlDasar . " AND (s.kode_sepeda ILIKE :q1 OR s.merek ILIKE :q2 OR pl.nama ILIKE :q3)"
```

Tiga placeholder berbeda (`:q1`, `:q2`, `:q3`) dipakai untuk nilai yang sama, karena driver PostgreSQL PDO tidak mengizinkan satu nama placeholder dipakai berulang dalam satu query.

Sisa halaman — form pencarian `method="get"`, tombol "Kembalikan" dibungkus `<form method="post">` dengan `csrf_field()` dan `<input type="hidden" name="id">` — identik dengan pola tombol Hapus dari Jobsheet 9 dan 11. Gaya tombolnya diatur oleh kelas `form-kembali` dan `btn-kembali` di CSS.

## 4.2 `proses_kembali.php`: Transaction Kebalikan

```php
try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("SELECT status FROM penyewaan WHERE id = :id FOR UPDATE");
    $stmt->execute(['id' => $id]);
    $trx = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$trx || $trx['status'] !== 'disewa') {
        throw new Exception('Transaksi tidak ditemukan atau sudah dikembalikan.');
    }

    $update = $pdo->prepare(
        "UPDATE penyewaan SET status = 'dikembalikan', tanggal_kembali = CURRENT_DATE WHERE id = :id"
    );
    $update->execute(['id' => $id]);

    $pdo->commit();
    $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Sepeda berhasil dikembalikan.'];
} catch (PDOException $e) {
    // rollBack + pesan umum
} catch (Exception $e) {
    // rollBack + pesan dari throw
}
```

Bandingkan dengan `proses_tambah.php`:

| | Penyewaan Baru | Pengembalian |
|---|---|---|
| Baris yang dikunci `FOR UPDATE` | Baris **sepeda** (cek belum disewa) | Baris **penyewaan** (cek status) |
| Syarat kegagalan | Sepeda tidak ada / pelanggan tidak ada / sudah ada penyewaan aktif | `!$trx \|\| $trx['status'] !== 'disewa'` |
| Perubahan pada tabel `penyewaan` | `INSERT` baris baru, `status = 'disewa'` | `UPDATE status = 'dikembalikan'`, isi `tanggal_kembali` |
| Ketersediaan sepeda | Hilang dari dropdown (punya penyewaan aktif) | Muncul lagi di dropdown (tidak ada lagi penyewaan aktif) |

- **`FOR UPDATE` di sini mengunci baris `penyewaan`.** Bayangkan **dua** klik "Kembalikan" untuk transaksi yang **sama** hampir bersamaan (misalnya klik dua kali tidak sengaja). Tanpa penguncian, keduanya bisa lolos pengecekan status. Dengan kunci, klik kedua menunggu, lalu melihat status sudah `'dikembalikan'` dan ditolak.
- **`$trx['status'] !== 'disewa'`** — mencegah satu pengembalian diproses dua kali, sehingga `tanggal_kembali` tidak tertimpa.
- **`UPDATE ... SET status = ..., tanggal_kembali = CURRENT_DATE`** — dua kolom diubah dalam **satu** perintah. Status berubah **dan** tanggal kembali terisi tanggal hari ini.
- Tidak ada langkah "menambah stok": ketersediaan sepeda otomatis pulih karena pencarian sepeda tersedia di [bab 3 §3.1](03-penyewaan-baru-dan-transaction.md#31-penyewaantambahphp-menyiapkan-dropdown) hanya melihat penyewaan berstatus `'disewa'`.

## 4.3 Kenapa Guard Method Masih Diperiksa Padahal Sudah Ada CSRF?

```php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: kembali.php');
    exit;
}

csrf_verify();
```

Pemeriksaan `$_SERVER['REQUEST_METHOD']` mencegah operasi ini terpicu lewat `GET` (tautan/crawler). Token CSRF menutup celah pemalsuan permintaan `POST` dari situs lain. Kedua lapisan **melengkapi**, bukan menggantikan: pemeriksaan method menangkap kasus sederhana, token CSRF menangkap kasus yang lebih canggih. Melapisi beberapa pertahanan sekaligus seperti ini disebut **defense in depth** — jangan bergantung pada satu lapisan saja.

Lanjut ke: [Riwayat Penyewaan & `JOIN`](05-riwayat-penyewaan-join.md)
