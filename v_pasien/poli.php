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

// Ambil data pasien (Nomor RM)
$nama_pasien = $_SESSION['nama'];
$query_pasien = $conn->query("SELECT id, no_rm FROM pasien WHERE nama = '$nama_pasien'");
$data_pasien = $query_pasien->fetch_assoc();
$id_pasien = $data_pasien['id'];

// Ambil data Poli
$query_poli = $conn->query("SELECT id, nama_poli FROM poli");

// Ambil semua jadwal aktif
$query_jadwal = $conn->query("SELECT jp.id, jp.hari, jp.jam_mulai, jp.jam_selesai, d.nama_dokter, p.nama_poli, d.id_poli 
        FROM jadwal_periksa jp
        JOIN dokter d ON jp.id_dokter = d.id
        JOIN poli p ON d.id_poli = p.id
        WHERE jp.status = 'Aktif'");

// Proses pendaftaran Poli
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_jadwal'])) {
    $id_jadwal = $_POST['id_jadwal'];
    $keluhan = $_POST['keluhan'];

    // Hitung nomor antrian
    $result = $conn->query("SELECT COUNT(id) as total FROM daftar_poli WHERE id_jadwal = '$id_jadwal'");
    $row = $result->fetch_assoc();
    $no_antrian = $row['total'] + 1;

    // Insert ke database
    $sql = "INSERT INTO daftar_poli (id_pasien, id_jadwal, keluhan, no_antrian) 
            VALUES ('$id_pasien', '$id_jadwal', '$keluhan', '$no_antrian')";
    if ($conn->query($sql) === TRUE) {
        header('Location: poli.php'); // Redirect ke halaman yang sama
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

// Ambil riwayat daftar poli untuk pasien yang sedang login
$query_riwayat = $conn->query("SELECT p.id AS poli_id, dp.id AS detail_periksa_id, d.id AS dokter_id, jp.id AS jadwal_periksa_id, 
           p.nama_poli, d.nama_dokter, jp.hari, jp.jam_mulai, jp.jam_selesai, 
           dp.no_antrian, dp.status, dp.waktu_periksa
    FROM daftar_poli dp
    JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id
    JOIN dokter d ON jp.id_dokter = d.id
    JOIN poli p ON d.id_poli = p.id
    WHERE dp.id_pasien = '$id_pasien'
    ORDER BY dp.id DESC");
if (!$query_riwayat) {
    die("Error query riwayat: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include('../header/header.php') ?>
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
            <a href="../v_pasien/dashboard.php" class="nav-link ">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
                <span class="badge badge-warning right">Pasien</span>
              </p>
            </a>
              <li class="nav-item">
                <a href="../v_pasien/poli.php" class="nav-link active">
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
    <link rel="stylesheet" href="../style/pasien_poli.css">
    <h1>Daftar Poli</h1>
    <form method="POST" action="poli.php">
        <label for="no_rm">Nomor Rekam Medis:</label>
        <input type="text" id="no_rm" value="<?php echo $data_pasien['no_rm']; ?>" readonly>

        <label for="poli">Pilih Poli:</label>
        <select name="id_poli" id="poli" onchange="filterJadwal()">
            <option value="">-- Pilih Poli --</option>
            <?php while ($poli = $query_poli->fetch_assoc()): ?>
                <option value="<?php echo $poli['id']; ?>"><?php echo htmlspecialchars($poli['nama_poli']); ?></option>
            <?php endwhile; ?>
        </select>

        <label for="jadwal">Pilih Jadwal (Poli - Dokter - Hari - Waktu):</label>
        <select name="id_jadwal" id="jadwal" required>
            <option value="">-- Pilih Jadwal --</option>
            <?php while ($jadwal = $query_jadwal->fetch_assoc()): ?>
                <option value="<?php echo $jadwal['id']; ?>" data-poli="<?php echo $jadwal['id_poli']; ?>">
                    <?php echo htmlspecialchars($jadwal['nama_poli'] . ' - ' . $jadwal['nama_dokter'] . ' - ' . $jadwal['hari'] . ' (' . $jadwal['jam_mulai'] . ' - ' . $jadwal['jam_selesai'] . ')'); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="keluhan">Keluhan:</label>
        <textarea name="keluhan" id="keluhan" rows="4" placeholder="Tuliskan keluhan Anda..." required></textarea>

        <button type="submit">Daftar</button>
    </form>

    <h1>Riwayat Daftar Poli</h1>
    <table>
        <tr>
            <th>No</th>
            <th>Poli</th>
            <th>Dokter</th>
            <th>Hari</th>
            <th>Mulai</th>
            <th>Selesai</th>
            <th>Antrian</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        <?php if ($query_riwayat->num_rows > 0): ?>
            <?php $no = 1; while ($row = $query_riwayat->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($row['nama_poli']); ?></td>
                    <td><?php echo htmlspecialchars($row['nama_dokter']); ?></td>
                    <td><?php echo htmlspecialchars($row['hari']); ?></td>
                    <td><?php echo htmlspecialchars($row['jam_mulai']); ?></td>
                    <td><?php echo htmlspecialchars($row['jam_selesai']); ?></td>
                    <td><?php echo $row['no_antrian']; ?></td>
                    <td>
                        <?php 
                        if ($row['status'] == 'Sudah diperiksa') {
                            echo "<span class='status-lengkap'>Sudah diperiksa</span><br>";
                            echo "<small>" . date('Y-m-d H:i:s', strtotime($row['waktu_periksa'])) . "</small>";
                        } else {
                            echo "<span class='status-pending'>Belum diperiksa</span>";
                        }
                        ?>
                    </td>
                    <td>
                        <?php if ($row['status'] == 'Sudah diperiksa'): ?>
                            <a href="riwayat_pasien.php?id=<?php echo htmlspecialchars($row['detail_periksa_id']); ?>" class="btn-detail">Riwayat</a>
                        <?php else: ?>
                            <a href="detail_pasien.php?id=<?php echo htmlspecialchars($row['detail_periksa_id']); ?>" class="btn-detail">Detail</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="9">Belum ada riwayat pendaftaran.</td></tr>
        <?php endif; ?>
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

<script>
function filterJadwal() {
    const poliSelect = document.getElementById('poli');
    const jadwalSelect = document.getElementById('jadwal');
    const selectedPoli = poliSelect.value;

    for (let option of jadwalSelect.options) {
        if (option.value === "") {
            option.style.display = "";
        } else if (option.getAttribute('data-poli') === selectedPoli || selectedPoli === "") {
            option.style.display = "";
        } else {
            option.style.display = "none";
        }
    }
    jadwalSelect.value = "";
}
</script>

</body>
</html>
<?php $conn->close(); ?>
