<?php
session_start();

// Hapus semua session
$_SESSION = [];
session_unset();
session_destroy();
session_regenerate_id(true);

// Redirect ke halaman login
header("Location: login.php");
exit;
?>
