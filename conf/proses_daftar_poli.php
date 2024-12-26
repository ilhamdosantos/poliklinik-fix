<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_poli";

// Koneksi ke database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Tangkap data dari form
$id_jadwal = $_POST['id_jadwal'];
$keluhan = $_POST['keluhan'];

// Ambil ID pasien berdasarkan sesi
$nama_pasien = $_SESSION['nama'];
$query_pasien = $conn->query("SELECT id FROM pasien WHERE nama = '$nama_pasien'");
$data_pasien = $query_pasien->fetch_assoc();
$id_pasien = $data_pasien['id'];

// Hitung nomor antrian berdasarkan jumlah data di tabel
$result = $conn->query("SELECT COUNT(id) as total FROM daftar_poli WHERE id_jadwal = '$id_jadwal'");
$row = $result->fetch_assoc();
$no_antrian = $row['total'] + 1;

// Insert ke tabel daftar_poli
$sql = "INSERT INTO daftar_poli (id_pasien, id_jadwal, keluhan, no_antrian) 
        VALUES ('$id_pasien', '$id_jadwal', '$keluhan', '$no_antrian')";

if ($conn->query($sql) === TRUE) {
    header('Location: ../v_pasien/poli.php'); // Redirect ke Riwayat Daftar Poli
    exit();
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>