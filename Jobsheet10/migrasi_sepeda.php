<?php
require __DIR__ . '/includes/koneksi.php';

$jsonFile = __DIR__ . '/data/sepeda.json';
if (!file_exists($jsonFile)) {
    die("File JSON tidak ditemukan di: " . $jsonFile);
}

$dataSepeda = json_decode(file_get_contents($jsonFile), true);
if (empty($dataSepeda)) {
    die("Data JSON kosong atau format tidak valid.");
}

$stmt = $pdo->prepare("
    INSERT INTO sepeda (kode_sepeda, merek, jenis, harga_sewa)
    VALUES (:kode, :merek, :jenis, :harga)
");

$berhasil = 0;
$gagal = 0;

echo "<h2>Proses Migrasi Data Sepeda...</h2><ul>";
foreach ($dataSepeda as $s) {
    try {
        $stmt->execute([
            'kode'  => $s['kode'],
            'merek' => $s['merek'],
            'jenis' => $s['jenis'],
            'harga' => $s['harga']
        ]);
        echo "<li><span style='color:green;'>[BERHASIL]</span> Sepeda " . htmlspecialchars("{$s['merek']} {$s['jenis']} ({$s['kode']})") . " diimpor.</li>";
        $berhasil++;
    } catch (PDOException $e) {
        $gagal++;
        if ($e->getCode() === '23505') {
            echo "<li><span style='color:orange;'>[Dilewati]</span> Kode " . htmlspecialchars($s['kode']) . " sudah ada.</li>";
        } else {
            echo "<li><span style='color:red;'>[GAGAL]</span> " . htmlspecialchars($s['kode'] . ': ' . $e->getMessage()) . "</li>";
        }
    }
}
echo "</ul><p><strong>Selesai!</strong> Berhasil: {$berhasil}, Dilewati/Gagal: {$gagal}.</p>";
echo "<a href='sepeda/list.php'>Lihat Daftar Sepeda</a>";
