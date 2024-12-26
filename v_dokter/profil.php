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

// Ambil ID Dokter dari sesi login (contoh: user yang sedang login)
$id_dokter = $_SESSION['id_dokter'] ?? 1; // Gunakan ID dokter dari sesi login. Default 1 untuk testing.

// Query untuk mengambil data dokter berdasarkan ID
$query = $conn->prepare("SELECT nama_dokter, alamat, no_hp FROM dokter WHERE id = ?");
$query->bind_param("i", $id_dokter);
$query->execute();
$result = $query->get_result();

$dokter = $result->fetch_assoc();

// Proses update data dokter
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_dokter = $_POST['nama_dokter'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];

    // Update data ke database
    $update_query = $conn->prepare("UPDATE dokter SET nama_dokter = ?, alamat = ?, no_hp = ? WHERE id = ?");
    $update_query->bind_param("ssii", $nama_dokter, $alamat, $no_hp, $id_dokter);

    if ($update_query->execute()) {
        echo "<script>alert('Profil berhasil diperbarui!');</script>";
        // Reload data setelah update
        header("Refresh:0");
    } else {
        echo "<script>alert('Gagal memperbarui profil!');</script>";
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
  <?php include('../navbar/navbar_dokter.php') ?>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <?php include('../logo/logo.php') ?>

    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
        <img src="../img/gd.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block"><?php echo $_SESSION['nama_dokter'];?></a>
        </div>
      </div>
      
      <!-- Sidebar Menu -->
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
                <a href="../v_dokter/daftar_riwayat_pasien.php" class="nav-link">
                <span class="badge badge-danger right">Dokter</span>
                <i class="nav-icon fa fa-history"></i>
                  <p>Riwayat Pasien</p>
                </a>
              </li>
          </li>
          <li class="nav-item">
            <a href="../v_dokter/profil.php" class="nav-link active">
            <i class="nav-icon fa fa-user-md"></i>
              <p>
                Profil
                <span class="badge badge-danger right">Dokter</span>
              </p>
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
    <link rel="stylesheet" href="../style/dokter_profil.css">
    <h1>Profil Dokter</h1>
    <form method="POST" action="">
        <div class="form-group">
            <label for="nama_dokter">Nama Dokter:</label>
            <input type="text" id="nama_dokter" name="nama_dokter" value="<?php echo htmlspecialchars($dokter['nama_dokter'] ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label for="alamat">Alamat Dokter:</label>
            <textarea id="alamat" name="alamat" rows="3"><?php echo htmlspecialchars($dokter['alamat'] ?? ''); ?></textarea>
        </div>
        <div class="form-group">
            <label for="no_hp">Telepon Dokter:</label>
            <input type="number" id="no_hp" name="no_hp" value="<?php echo htmlspecialchars($dokter['no_hp'] ?? ''); ?>" required>
        </div>
        <button type="submit" class="btn">Simpan Perubahan</button>
    </form>
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
