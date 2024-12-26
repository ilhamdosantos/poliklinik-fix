<?php
session_start();
include ('config.php');

// Tangkap data dari form
$nama_dokter = $_POST['nama_dokter'];
$alamat = $_POST['alamat'];

// Cek apakah data sudah diisi
if (empty($nama_dokter) || empty($alamat)) {
    // Jika data tidak lengkap, redirect kembali ke halaman login dengan error
    header('Location:../login_dokter.php?error=3'); // Misalnya error=3 berarti data tidak lengkap
    exit();
}

// Query untuk validasi dokter
$query = mysqli_query($koneksi, "SELECT * FROM dokter WHERE nama_dokter='$nama_dokter' AND alamat='$alamat'");

if (mysqli_num_rows($query) == 1) {
    // Ambil data dokter dari database
    $user = mysqli_fetch_array($query);
    
    // Set session untuk id_dokter dan nama_dokter
    $_SESSION['id_dokter'] = $user['id']; // Ambil 'id' dari kolom database
    $_SESSION['nama_dokter'] = $user['nama_dokter'];

    // Redirect ke dashboard dokter
    header('Location:../v_dokter/dashboard.php');
    exit();
} else {
    // Jika nama dokter atau alamat salah
    header('Location:../login_dokter.php?error=1'); // Error code 1: login gagal
    exit();
}
?>