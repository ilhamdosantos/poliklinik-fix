<?php
session_start();
// Koneksi ke database
$servername = "localhost";
$username = "root"; // Sesuaikan dengan username database Anda
$password = "";
$dbname = "db_poli"; // Sesuaikan dengan nama database Anda

$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Tangkap data dari form
$nama = $_POST['nama'];
$alamat = $_POST['alamat'];
$no_ktp = $_POST['no_ktp'];
$no_hp = $_POST['no_hp'];

// Generate Nomor RM (Rekam Medis)
//$result = $conn->query("SELECT MAX(id) as max_id FROM pasien");
$result = $conn->query("SELECT COUNT(id) as count_id FROM pasien");
$row = $result->fetch_assoc();
//$next_id = isset($row['max_id']) ? $row['max_id'] + 1 : 1;
$next_id = isset($row['count_id']) ? $row['count_id'] + 1 : 1;
$no_rm = date("Ym") . '-' . str_pad($next_id, 3, '0', STR_PAD_LEFT);

// Masukkan data ke tabel pasien
$sql = "INSERT INTO pasien (nama, alamat, no_ktp, no_hp, no_rm) 
        VALUES ('$nama', '$alamat', '$no_ktp', '$no_hp', '$no_rm')";

if ($conn->query($sql) === TRUE) {
    // Set session untuk nama pengguna
    $_SESSION['nama'] = $nama;  // Menyimpan nama dalam session
    echo "<script>alert('Registrasi berhasil! Nomor RM Anda: $no_rm'); window.location.href='../v_pasien/dashboard.php';</script>";
} else {
    echo "<script>alert('Error: " . $conn->error . "'); window.location.href='../register_pasien.php';</script>";
}

// Tutup koneksi
$conn->close();
?>
