<?php
// Jika variabel DATABASE_URL ada (Railway), pakai itu; jika tidak, pakai koneksi lokal.
$databaseUrl = getenv('DATABASE_URL');

if ($databaseUrl) {
    $o    = parse_url($databaseUrl);
    $host = $o['host'] ?? '';
    $port = $o['port'] ?? 5432;
    $user = $o['user'] ?? '';
    $pass = $o['pass'] ?? '';
    $db   = ltrim($o['path'] ?? '', '/');
} else {
$host = "localhost";
$port = "5432";
$db   = "01_malangbike";
$user = "postgres";
$pass = "12345678";  // Sesuaikan dengan password PostgreSQL lokalmu
}

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
