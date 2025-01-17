<?php
// Koneksi database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_poli";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil ID pasien dari parameter GET
$id_pasien = isset($_GET['id']) ? $_GET['id'] : 0;

// Pastikan session id_dokter tersedia
session_start();
if (!isset($_SESSION['id_dokter'])) {
    die("Akses ditolak. Anda harus login sebagai dokter.");
}

$id_dokter = $_SESSION['id_dokter'];

// Query untuk mendapatkan data riwayat pemeriksaan pasien dengan nama "Giselle" yang terkait dengan dokter yang sedang login
$query_riwayat = $conn->query("SELECT dp.id AS id_daftar_poli, pr.id AS id_periksa, pr.tgl_periksa, dp.waktu_periksa, 
           pa.nama AS nama_pasien, d.nama_dokter, dp.keluhan, pr.catatan, pr.biaya_periksa
    FROM daftar_poli dp
    JOIN pasien pa ON dp.id_pasien = pa.id
    JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id
    JOIN dokter d ON jp.id_dokter = d.id
    LEFT JOIN periksa pr ON dp.id = pr.id_daftar_poli
    WHERE dp.id_pasien = '$id_pasien' AND pa.nama = 'Giselle' AND d.id = '$id_dokter'");

if ($query_riwayat->num_rows > 0) {
    echo '<table class="table table-bordered">';
    echo '<tr>
            <th>No</th>
            <th>Tanggal Periksa</th>
            <th>Nama Pasien</th>
            <th>Nama Dokter</th>
            <th>Keluhan</th>
            <th>Catatan</th>
            <th>Obat</th>
            <th>Biaya</th>
          </tr>';
    $no = 1;
    while ($data = $query_riwayat->fetch_assoc()) {
        $id_periksa = $data['id_periksa'];

        // Query untuk mengambil data obat berdasarkan ID periksa
        $query_obat = $conn->query("SELECT o.nama_obat, o.kemasan
            FROM detail_periksa dp
            JOIN obat o ON dp.id_obat = o.id
            WHERE dp.id_periksa = '$id_periksa'");

        $obat_list = [];
        if ($query_obat->num_rows > 0) {
            while ($obat = $query_obat->fetch_assoc()) {
                $obat_list[] = $obat['nama_obat'] . " (" . $obat['kemasan'] . ")";
            }
        }

        echo '<tr>';
        echo '<td>' . $no++ . '</td>';
        echo '<td>' . htmlspecialchars($data['waktu_periksa']) . '</td>';
        echo '<td>' . htmlspecialchars($data['nama_pasien']) . '</td>';
        echo '<td>' . htmlspecialchars($data['nama_dokter']) . '</td>';
        echo '<td>' . htmlspecialchars($data['keluhan']) . '</td>';
        echo '<td>' . htmlspecialchars($data['catatan'] ?: '-') . '</td>';
        echo '<td>' . (!empty($obat_list) ? implode(", ", $obat_list) : '-') . '</td>';
        echo '<td>' . ($data['biaya_periksa'] ? 'Rp ' . number_format($data['biaya_periksa'], 0, ',', '.') : '-') . '</td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo "Tidak ada data riwayat untuk pasien ini.";
}

$conn->close();
?>