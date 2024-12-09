<?php
session_start();

// Hapus semua data session
session_unset();

// Hancurkan session
session_destroy();

// Menghapus cookie jika ada
setcookie('username', '', time() - 86400, '/');  // Hapus cookie 'username'

// Redirect ke halaman login
header("Location: login.php");
exit;
?>
