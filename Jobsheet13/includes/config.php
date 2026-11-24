<?php
// Pemisahan kredensial dari kode sumber (includes/koneksi.php).
// Di lingkungan produksi/hosting, isi nilai-nilai ini lewat environment
// variable yang diatur di server, JANGAN menulis kredensial asli di file ini
// lalu meng-commit-nya ke repository.
//
// Urutan prioritas:
//   1. DATABASE_URL (format postgres://user:pass@host:port/namadb) — dipakai
//      layanan hosting seperti Railway; kalau ada, nilainya dipakai lebih dulu.
//   2. DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS — environment variable satuan.
//   3. Nilai cadangan di bawah — hanya untuk pengembangan lokal.

$databaseUrl = getenv('DATABASE_URL');
$dbopts = [];
if (is_string($databaseUrl) && $databaseUrl !== '') {
    $dbopts = parse_url($databaseUrl) ?: [];
}

return [
    'db_host' => $dbopts['host'] ?? (getenv('DB_HOST') ?: 'localhost'),
    'db_port' => $dbopts['port'] ?? (getenv('DB_PORT') ?: '5432'),
    'db_name' => isset($dbopts['path']) ? ltrim($dbopts['path'], '/') : (getenv('DB_NAME') ?: '01_malangbike'),
    'db_user' => isset($dbopts['user']) ? rawurldecode($dbopts['user']) : (getenv('DB_USER') ?: 'postgres'),
    'db_pass' => isset($dbopts['pass']) ? rawurldecode($dbopts['pass']) : (getenv('DB_PASS') ?: '12345678'),
];
