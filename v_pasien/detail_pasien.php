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

// Periksa apakah parameter ID tersedia
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('ID tidak valid'); window.history.back();</script>";
    exit();
}

$id_daftar_poli = $_GET['id'];

// Ambil detail data dari database berdasarkan ID pendaftaran
$query = "
    SELECT p.nama_poli, d.nama_dokter, jp.hari, jp.jam_mulai, jp.jam_selesai, dp.no_antrian
    FROM daftar_poli dp
    JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id
    JOIN dokter d ON jp.id_dokter = d.id
    JOIN poli p ON d.id_poli = p.id
    WHERE dp.id = '$id_daftar_poli'
";
$result = $conn->query($query);

if ($result->num_rows === 0) {
    echo "<script>alert('Data tidak ditemukan'); window.history.back();</script>";
    exit();
}

$data = $result->fetch_assoc();
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

      <!-- SidebarSearch Form -->
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item menu-open">
            <a href="../pasien/dashboard.php" class="nav-link ">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
                <span class="badge badge-warning right">Pasien</span>
              </p>
            </a>
              <li class="nav-item">
                <a href="../pasien/poli.php" class="nav-link ">
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
    <div class="container">
    <link rel="stylesheet" href="../style/pasien_detail.css">
        <h1>Detail Poli</h1>
        <p>Nama Poli: <span><?php echo htmlspecialchars($data['nama_poli']); ?></span></p>
        <p>Nama Dokter: <span><?php echo htmlspecialchars($data['nama_dokter']); ?></span></p>
        <p>Hari: <span><?php echo htmlspecialchars($data['hari']); ?></span></p>
        <p>Mulai: <span><?php echo htmlspecialchars($data['jam_mulai']); ?></span></p>
        <p>Selesai: <span><?php echo htmlspecialchars($data['jam_selesai']); ?></span></p>
        <p>Nomor Antrian: <span><?php echo htmlspecialchars($data['no_antrian']); ?></span></p>
        <a href="poli.php" class="btn-back">Kembali</a>
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <?php include('../footer/footer.php') ?>
  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->
</body>
</html>
<?php $conn->close(); ?>    