<?php
session_start();

// Validasi apakah sesi sudah diset
if (isset($_SESSION['nama'])) {
    session_destroy(); // Hapus semua sesi
    header('Location: ../login_admin.php');
    exit;
} else {
    header('Location: ../login_admin.php'); // Jika sudah logout, tetap arahkan ke login
    exit;
}
?>
