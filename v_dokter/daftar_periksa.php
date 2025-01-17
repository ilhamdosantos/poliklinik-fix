<?php
session_start();

// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_poli";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data daftar periksa dari tabel 'daftar_poli'
$query_periksa = $conn->query("
    SELECT dp.id, pa.nama AS nama_pasien, dp.keluhan, dp.no_antrian, dp.status, jp.id_dokter, dr.id AS dokter_id
    FROM daftar_poli dp
    JOIN pasien pa ON dp.id_pasien = pa.id
    JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id
    JOIN dokter dr ON jp.id_dokter = dr.id
    WHERE dr.id = " . $_SESSION['id_dokter'] . "
    ORDER BY dp.no_antrian ASC
");
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
  <?php include('../navbar/navbar_dokter.php') ?>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <?php include('../logo/logo.php') ?>

    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="../img/gd.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block"><?php echo $_SESSION['nama_dokter'];?></a>
        </div>
      </div>

      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item menu-open">
            <a href="../v_dokter/dashboard.php" class="nav-link ">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
                <span class="badge badge-danger right">Dokter</span>
              </p>
            </a>
              <li class="nav-item">
                <a href="../v_dokter/jadwal_periksa.php" class="nav-link ">
                  <span class="badge badge-danger right">Dokter</span>
                  <i class="nav-icon fas fa-calendar-alt"></i>
                  <p>Jadwal Periksa</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="../v_dokter/daftar_periksa.php" class="nav-link active">
                <span class="badge badge-danger right">Dokter</span>
                <i class="nav-icon fas fa-stethoscope"></i>
                
                  <p>Periksa Pasien</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="../v_dokter/daftar_riwayat_pasien.php" class="nav-link">
                <span class="badge badge-danger right">Dokter</span>
                <i class="nav-icon fa fa-history"></i>
                  <p>Riwayat Pasien</p>
                </a>
              </li>
          </li>
          <li class="nav-item">
            <a href="../v_dokter/profil.php" class="nav-link">
            <i class="nav-icon fa fa-user-md"></i>
              <p>
                Profil
                <span class="badge badge-danger right">Dokter</span>
              </p>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <?php include('../content_header/content_admin.php') ?>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="container mt-5">
    <link rel="stylesheet" href="../style/dokter_daftar_periksa.css">
    <h1>Daftar Periksa Pasien</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No Urut</th>
                <th>Nama Pasien</th>
                <th>Keluhan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($query_periksa->num_rows > 0): ?>
                <?php while ($row = $query_periksa->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['no_antrian']); ?></td>
                        <td><?php echo htmlspecialchars($row['nama_pasien']); ?></td>
                        <td><?php echo htmlspecialchars($row['keluhan']); ?></td>
                        <td>
                            <?php 
                                if ($row['status'] == 'Sudah diperiksa') {
                                    echo "<span style='color: green; font-weight: bold;'>Sudah Diperiksa</span>";
                                } else {
                                    echo "<span style='color: red; font-weight: bold;'>Belum Diperiksa</span>";
                                }
                            ?>
                        </td>
                        <td>
                            <?php if ($row['status'] == 'Sudah diperiksa'): ?>
                                <a href="edit_periksa_pasien.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-edit">Edit</a>
                            <?php else: ?>
                                <a href="periksa_pasien.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-periksa">Periksa</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">Belum ada pasien yang mendaftar.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
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
<?php
$conn->close();
?>