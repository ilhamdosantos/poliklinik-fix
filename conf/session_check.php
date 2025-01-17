<?php
session_start();

// Validasi apakah dokter sudah login
if (!isset($_SESSION['id_dokter']) || !isset($_SESSION['nama_dokter'])) {
    header('Location: ../login_dokter.php?error=4'); // Error code 4: belum login
    exit();
}

// Optional: Anda bisa menambahkan validasi waktu session jika diperlukan
?>
