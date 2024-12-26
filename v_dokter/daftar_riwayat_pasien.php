<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Koneksi database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_poli";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Query untuk mengambil data pasien
$query_pasien = $conn->query("SELECT id, nama, alamat, no_ktp, no_hp, no_rm FROM pasien");
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
                <a href="../v_dokter/daftar_periksa.php" class="nav-link">
                <span class="badge badge-danger right">Dokter</span>
                <i class="nav-icon fas fa-stethoscope"></i>
                
                  <p>Periksa Pasien</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="../v_dokter/daftar_riwayat_pasien.php" class="nav-link active">
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
    <link rel="stylesheet" href="../style/dokter_daftar_riwayat_pasien.css">
    <h1>Daftar Riwayat Pasien</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Pasien</th>
                <th>Alamat</th>
                <th>No. KTP</th>
                <th>No. Telepon</th>
                <th>No. RM</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($query_pasien->num_rows > 0): ?>
            <?php $no = 1; while ($row = $query_pasien->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($row['nama']); ?></td>
                    <td><?php echo htmlspecialchars($row['alamat']); ?></td>
                    <td><?php echo htmlspecialchars($row['no_ktp']); ?></td>
                    <td><?php echo htmlspecialchars($row['no_hp']); ?></td>
                    <td><?php echo htmlspecialchars($row['no_rm'] ?? '-'); ?></td>
                    <td>
                        <button 
                            class="btn btn-primary btn-detail" 
                            data-bs-toggle="modal" 
                            data-bs-target="#detailModal" 
                            data-id="<?php echo $row['id']; ?>">
                            Detail Riwayat Periksa
                        </button>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">Tidak ada data pasien.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Detail Riwayat Periksa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="riwayatDetail">
                <!-- Data detail riwayat akan dimuat di sini -->
                <div id="modalContent">Memuat...</div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const buttons = document.querySelectorAll('.btn-detail');
        const modalContent = document.getElementById('modalContent');

        buttons.forEach(button => {
            button.addEventListener('click', function () {
                const idPasien = this.getAttribute('data-id');
                modalContent.innerHTML = "Memuat...";

                // Fetch data detail riwayat dari server
                fetch('../v_dokter/detail_riwayat_pasien.php?id=' + idPasien)
                    .then(response => response.text())
                    .then(data => {
                        modalContent.innerHTML = data;
                    })
                    .catch(error => {
                        modalContent.innerHTML = "Gagal memuat data.";
                        console.error(error);
                    });
            });
        });
    });
</script>
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