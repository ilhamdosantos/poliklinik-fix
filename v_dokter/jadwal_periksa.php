<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_poli"; // Ganti dengan nama database Anda

// Koneksi ke database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

// Validasi sesi login
if (!isset($_SESSION['id_dokter']) || !isset($_SESSION['nama_dokter'])) {
    echo "<script>alert('Silakan login terlebih dahulu.'); window.location='../login_dokter.php';</script>";
    exit();
}

// Proses simpan data baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_schedule'])) {
    $id_dokter = $_SESSION['id_dokter'];
    $hari = $_POST['hari'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    $status = "Tidak Aktif"; // Status default saat menambah jadwal baru

    $sql = "INSERT INTO jadwal_periksa (id_dokter, hari, jam_mulai, jam_selesai, status) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $id_dokter, $hari, $jam_mulai, $jam_selesai, $status);

    if ($stmt->execute()) {
        echo "<script>alert('Jadwal berhasil ditambahkan dengan status Tidak Aktif!'); window.location='jadwal_periksa.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Proses edit data
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_schedule'])) {
    $id = $_POST['id'];
    $hari = $_POST['hari'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    $status = $_POST['status'];

    $sql = "UPDATE jadwal_periksa SET hari=?, jam_mulai=?, jam_selesai=?, status=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $hari, $jam_mulai, $jam_selesai, $status, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Jadwal berhasil diperbarui!'); window.location='jadwal_periksa.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Query untuk mengambil data jadwal periksa dan nama dokter
$sql = "SELECT jp.id, d.nama_dokter, jp.hari, jp.jam_mulai, jp.jam_selesai, jp.status 
        FROM jadwal_periksa jp
        JOIN dokter d ON jp.id_dokter = d.id";
$result = $conn->query($sql);

// Ambil data untuk edit jika ada
$edit_data = null;
if (isset($_GET['edit_id'])) {
    $id = $_GET['edit_id'];
    $sql_edit = "SELECT * FROM jadwal_periksa WHERE id = ?";
    $stmt = $conn->prepare($sql_edit);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit_data = $stmt->get_result()->fetch_assoc();
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
                <a href="../v_dokter/jadwal_periksa.php" class="nav-link active">
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
      <!-- /.sidebar-menu -->
    </div>
  </aside>

  <div class="content-wrapper">
    <div class="container-fluid mt-10">
      <link rel="stylesheet" href="../style/dokter_jadwal_periksa.css">
      <?php if ($edit_data): ?>
      <h4 class="mb-3">Edit Jadwal Periksa</h4>
      <form method="POST">
        <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
        <div class="form-group">
          <label for="hari">Hari:</label>
          <input type="text" name="hari" id="hari" class="form-control" value="<?php echo htmlspecialchars($edit_data['hari']); ?>" required>
        </div>
        <div class="form-group">
          <label for="jam_mulai">Jam Mulai:</label>
          <input type="time" name="jam_mulai" id="jam_mulai" class="form-control" value="<?php echo htmlspecialchars($edit_data['jam_mulai']); ?>" required>
        </div>
        <div class="form-group">
          <label for="jam_selesai">Jam Selesai:</label>
          <input type="time" name="jam_selesai" id="jam_selesai" class="form-control" value="<?php echo htmlspecialchars($edit_data['jam_selesai']); ?>" required>
        </div>
        <div class="form-group">
          <label for="status">Status:</label>
          <select name="status" id="status" class="form-control" required>
            <option value="Aktif" <?php echo ($edit_data['status'] == 'Aktif') ? 'selected' : ''; ?>>Aktif</option>
            <option value="Tidak Aktif" <?php echo ($edit_data['status'] == 'Tidak Aktif') ? 'selected' : ''; ?>>Tidak Aktif</option>
          </select>
        </div>
        <button type="submit" name="edit_schedule" class="btn btn-primary">Simpan</button>
      </form>
      <?php else: ?>
      <h4 class="mb-3">Tambah Jadwal Periksa</h4>
      <form method="POST">
        <div class="form-group">
          <label for="hari">Hari:</label>
          <input type="text" name="hari" id="hari" class="form-control" placeholder="Contoh: Senin" required>
        </div>
        <div class="form-group">
          <label for="jam_mulai">Jam Mulai:</label>
          <input type="time" name="jam_mulai" id="jam_mulai" class="form-control" required>
        </div>
        <div class="form-group">
          <label for="jam_selesai">Jam Selesai:</label>
          <input type="time" name="jam_selesai" id="jam_selesai" class="form-control" required>
        </div>
        <button type="submit" name="add_schedule" class="btn btn-primary">Simpan</button>
      </form>
      <?php endif; ?>

      <h2 class="text-center mb-4">Daftar Jadwal Periksa</h2>
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama Dokter</th>
            <th>Hari</th>
            <th>Jam Mulai</th>
            <th>Jam Selesai</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($result->num_rows > 0) {
              $no = 1;
              while ($row = $result->fetch_assoc()) {
                  echo "<tr>";
                  echo "<td>" . $no++ . "</td>";
                  echo "<td>" . htmlspecialchars($row['nama_dokter']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['hari']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['jam_mulai']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['jam_selesai']) . "</td>";
                  echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                  echo "<td><a href='jadwal_periksa.php?edit_id=" . $row['id'] . "' class='btn btn-warning btn-sm'>Edit</a></td>";
                  echo "</tr>";
              }
          } else {
              echo "<tr><td colspan='7'>Tidak ada data jadwal periksa</td></tr>";
          }
          ?>
        </tbody>
      </table>

    </div>
  </div>
  <?php include('../footer/footer.php') ?>
</div>
</body>
</html>
<?php
$conn->close();
?>
