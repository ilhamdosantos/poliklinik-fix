<?php
session_start();

// Periksa apakah pasien sudah login
if (!isset($_SESSION['nama'])) {
    header('Location: ../login_pasien.php');
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_poli";

// Koneksi ke database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil ID riwayat poli dari URL
$id_daftar_poli = isset($_GET['id']) ? $_GET['id'] : 0;

// Query untuk mendapatkan data detail riwayat termasuk catatan dan biaya periksa
$query_riwayat = $conn->query("
    SELECT dp.id, p.nama_poli, d.nama_dokter, jp.hari, jp.jam_mulai, jp.jam_selesai, 
           dp.no_antrian, dp.status, pr.tgl_periksa, dp.keluhan, pr.catatan, pr.biaya_periksa
    FROM daftar_poli dp
    JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id
    JOIN dokter d ON jp.id_dokter = d.id
    JOIN poli p ON d.id_poli = p.id
    LEFT JOIN periksa pr ON dp.id = pr.id_daftar_poli
    WHERE dp.id = '$id_daftar_poli'
");

if ($query_riwayat->num_rows == 0) {
    die("Riwayat tidak ditemukan.");
}

$data = $query_riwayat->fetch_assoc();

// Query untuk mendapatkan daftar obat
$query_obat = $conn->query("
    SELECT o.nama_obat, o.kemasan 
    FROM detail_periksa do
    JOIN obat o ON do.id_obat = o.id
    WHERE do.id_periksa = (SELECT id FROM periksa WHERE id_daftar_poli = '$id_daftar_poli')
");

$obat_list = [];
if ($query_obat) {
    while ($obat = $query_obat->fetch_assoc()) {
        $obat_list[] = $obat['nama_obat'] . " (" . $obat['kemasan'] . ")";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php 
include('../header/header.php') ?>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <?php include('../preloader/preloader.php')?> 

  <!-- Navbar -->
  <?php include('../navbar/navbar_pasien.php') ?>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <?php include('../logo/logo.php') ?>

    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="../app/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block"><?php echo $_SESSION['nama'];?></a>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item menu-open">
            <a href="../v_pasien/dashboard.php" class="nav-link active">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
                <span class="badge badge-warning right">Pasien</span>
              </p>
            </a>
              <li class="nav-item">
                <a href="../v_pasien/poli.php" class="nav-link">
                  <span class="badge badge-warning right">Pasien</span>
                  <i class="nav-icon fas fa-hospital"></i>
                  <p>Poli</p>
                </a>
              </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <?php include('../content_header/content_admin.php') ?>
    <!-- /.content-header -->

    <!-- Main content -->
    <link rel="stylesheet" href="../style/pasien_riwayat.css">
    <h1>Detail Riwayat Daftar Poli</h1>
    <table>
        <tr>
            <th>Poli</th>
            <td><?php echo htmlspecialchars($data['nama_poli']); ?></td>
        </tr>
        <tr>
            <th>Dokter</th>
            <td><?php echo htmlspecialchars($data['nama_dokter']); ?></td>
        </tr>
        <tr>
            <th>Hari</th>
            <td><?php echo htmlspecialchars($data['hari']); ?></td>
        </tr>
        <tr>
            <th>Jam Mulai</th>
            <td><?php echo htmlspecialchars($data['jam_mulai']); ?></td>
        </tr>
        <tr>
            <th>Jam Selesai</th>
            <td><?php echo htmlspecialchars($data['jam_selesai']); ?></td>
        </tr>
        <tr>
            <th>Nomor Antrian</th>
            <td><?php echo htmlspecialchars($data['no_antrian']); ?></td>
        </tr>
        <tr>
            <th>Status</th>
            <td>
                <?php 
                if ($data['status'] == 'Sudah diperiksa') {
                    echo "Sudah diperiksa<br>";
                    echo "<small>" . date('Y-m-d H:i:s', strtotime($data['tgl_periksa'])) . "</small>";
                } else {
                    echo "Belum diperiksa";
                }
                ?>
            </td>
        </tr>
        <tr>
            <th>Keluhan</th>
            <td><?php echo htmlspecialchars($data['keluhan']); ?></td>
        </tr>
        <tr>
            <th>Catatan</th>
            <td><?php echo htmlspecialchars($data['catatan'] ?: '-'); ?></td>
        </tr>
        <tr>
            <th>Obat</th>
            <td>
                <?php 
                if (!empty($obat_list)) {
                    echo implode(", ", $obat_list);
                } else {
                    echo "-";
                }
                ?>
            </td>
        </tr>
        <tr>
            <th>Biaya Periksa</th>
            <td><?php echo $data['biaya_periksa'] ? 'Rp ' . number_format($data['biaya_periksa'], 0, ',', '.') : '-'; ?></td>
        </tr>
        
    </table>

    <a href="poli.php" class="btn-back">Kembali</a>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <?php include('../footer/footer.php') ?>
  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
  </aside>
</div>
<!-- ./wrapper -->
</body>
</html>
<?php $conn->close(); ?>
