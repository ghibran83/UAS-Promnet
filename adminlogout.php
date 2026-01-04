<?php

// Mulai sesi
session_start();

// Hapus semua jejak sesi
session_unset();
session_destroy();

// Redirect ke halaman login
header("Location: adminlogin.php");

exit();
?>