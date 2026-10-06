<?php
session_start();
$_SESSION = [];
session_destroy();

// Mulai sesi baru hanya untuk membawa pesan flash
session_start();
$_SESSION['flash'] = ['type' => 'success', 'pesan' => 'Anda telah logout.'];
header('Location: login.php');
exit;
