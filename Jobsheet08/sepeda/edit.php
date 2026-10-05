<?php
session_start();
require __DIR__ . '/../includes/koneksi.php';

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM sepeda WHERE id = :id");
$stmt->execute(['id' => $id]);
$s = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$s) {
    $_SESSION['flash'] = ['type' => 'danger', 'pesan' => 'Data sepeda tidak ditemukan.'];
    header('Location: list.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode  = trim($_POST['kode_sepeda'] ?? '');
    $merek = trim($_POST['merek'] ?? '');
    $jenis = trim($_POST['jenis'] ?? '');
    $harga = (int)($_POST['harga'] ?? 0);

    if ($kode === '' || $merek === '' || $jenis === '' || $harga <= 0) {
        $_SESSION['flash'] = ['type' => 'danger', 'pesan' => 'Semua bidang form wajib diisi dengan benar!'];
        header("Location: edit.php?id=$id");
        exit;
    }
    try {
        $u = $pdo->prepare("UPDATE sepeda SET kode_sepeda=:kode, merek=:merek, jenis=:jenis, harga_sewa=:harga WHERE id=:id");
        $u->execute(['kode' => $kode, 'merek' => $merek, 'jenis' => $jenis, 'harga' => $harga, 'id' => $id]);
        $_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Data sepeda berhasil diperbarui.'];
        header('Location: list.php');
    } catch (PDOException $e) {
        $pesan = $e->getCode() === '23505'
            ? "Kode sepeda '{$kode}' sudah dipakai sepeda lain."
            : 'Terjadi kesalahan database: ' . $e->getMessage();
        $_SESSION['flash'] = ['type' => 'danger', 'pesan' => $pesan];
        header("Location: edit.php?id=$id");
    }
    exit;
}

$page_title = "Edit Sepeda";
require __DIR__ . '/../includes/header.php';
?>

<section>
    <h2>Edit Sepeda</h2>
    <?php tampil_flash(); ?>

    <form id="form-tambah" method="post" action="edit.php" novalidate>
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <p>
            <label for="kode_sepeda">Kode Sepeda</label>
            <input type="text" id="kode_sepeda" name="kode_sepeda" required value="<?php echo htmlspecialchars($s['kode_sepeda']); ?>">
        </p>
        <p>
            <label for="merek">Merek</label>
            <input type="text" id="merek" name="merek" required value="<?php echo htmlspecialchars($s['merek']); ?>">
        </p>
        <p>
            <label for="jenis">Jenis</label>
            <input type="text" id="jenis" name="jenis" required value="<?php echo htmlspecialchars($s['jenis']); ?>">
        </p>
        <p>
            <label for="harga">Harga Sewa per Hari (Rp)</label>
            <input type="number" id="harga" name="harga" min="1" required value="<?php echo (int)$s['harga_sewa']; ?>">
        </p>
        <p class="form-actions">
            <button type="submit">Simpan Perubahan</button>
            <a href="list.php" class="btn btn-secondary">Batal</a>
        </p>
    </form>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
