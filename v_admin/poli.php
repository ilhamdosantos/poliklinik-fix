<?php
session_start();

// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_poli"; // Ganti dengan nama database Anda

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Inisialisasi variabel untuk edit
$edit_mode = false;
$edit_data = null;

// **Tambah/Edit Poli**
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_poli = $_POST['nama_poli'];
    $keterangan = $_POST['keterangan'];

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update Poli
        $id_poli = $_POST['id'];
        $sql_update = "UPDATE poli SET nama_poli = ?, keterangan = ? WHERE id = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("ssi", $nama_poli, $keterangan, $id_poli);
        if ($stmt->execute()) {
            echo "<script>alert('Data poli berhasil diperbarui!'); window.location.href='poli.php';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        // Tambah Poli Baru
        $sql_insert = "INSERT INTO poli (nama_poli, keterangan) VALUES (?, ?)";
        $stmt = $conn->prepare($sql_insert);
        $stmt->bind_param("ss", $nama_poli, $keterangan);
        if ($stmt->execute()) {
            echo "<script>alert('Poli berhasil ditambahkan!'); window.location.href='poli.php';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}

// **Edit Poli**
if (isset($_GET['edit'])) {
    $id_poli = $_GET['edit'];
    $sql = "SELECT * FROM poli WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_poli);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $edit_data = $result->fetch_assoc();
        $edit_mode = true;
    } else {
        echo "Data poli tidak ditemukan.";
    }
}

// **Hapus Poli**
if (isset($_GET['delete'])) {
    $id_poli = $_GET['delete'];
    $sql_delete = "DELETE FROM poli WHERE id = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("i", $id_poli);
    if ($stmt->execute()) {
        echo "<script>alert('Data poli berhasil dihapus!'); window.location.href='poli.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// **Ambil Data Poli**
$sql = "SELECT * FROM poli";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<?php include('../header/header.php'); ?>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <?php include('../preloader/preloader.php'); ?>

  <!-- Navbar -->
  <?php include('../navbar/navbar_admin.php'); ?>
  
  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <?php include('../logo/logo.php'); ?>
    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="../img/giselle.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block"><?php echo $_SESSION['nama'];?></a>
        </div>
      </div>
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item menu-open">
          <a href="../v_admin/dashboard.php" class="nav-link ">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
                <span class="badge badge-info right">Admin</span>
              </p>
            </a>
              <li class="nav-item">
              <a href="../v_admin/dokter.php" class="nav-link ">
                  <span class="badge badge-info right">Admin</span>
                  <i class="nav-icon 	fas fa-user-md"></i>
                  <p>Dokter</p>
                </a>
              </li>
              <li class="nav-item">
              <a href="../v_admin/pasien.php" class="nav-link">
                <span class="badge badge-info right">Admin</span>
                <i class="nav-icon fas fa-wheelchair"></i>
                
                  <p>Pasien</p>
                </a>
              </li>
              <li class="nav-item">
              <a href="../v_admin/poli.php" class="nav-link active">
                <span class="badge badge-info right">Admin</span>
                <i class="nav-icon fas fa-hospital"></i>
                  <p>Poli</p>
                </a>
              </li>
          </li>
          <li class="nav-item">
            <a href="../v_admin/obat.php" class="nav-link">
            <i class="nav-icon fas fa-pills"></i>
              <p>
                Obat
                <span class="badge badge-info right">Admin</span>
              </p>
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
  <link rel="stylesheet" href="../style/admin_poli.css">
    <div class="container-fluid mt-5">
        <h2 class="text-center mb-4"><?= $edit_mode ? 'Edit Poli' : 'Tambah Poli'; ?></h2>

        <!-- Form Tambah/Edit -->
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nama_poli" class="form-label">Nama Poli:</label>
                    <input type="text" class="form-control" id="nama_poli" name="nama_poli" value="<?= $edit_data['nama_poli'] ?? ''; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="keterangan" class="form-label">Keterangan:</label>
                    <input type="text" class="form-control" id="keterangan" name="keterangan" value="<?= $edit_data['keterangan'] ?? ''; ?>" required>
                </div>
            </div>
            <?php if ($edit_mode): ?>
                <input type="hidden" name="id" value="<?= $edit_data['id']; ?>">
            <?php endif; ?>
            <button type="submit" class="btn btn-primary w-100"><?= $edit_mode ? 'Simpan Perubahan' : 'Tambah Poli'; ?></button>
        </form>

        <!-- Daftar Poli -->
        <h3 class="mt-5 mb-3">Daftar Poli</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Poli</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php $no = 1; while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $row['nama_poli']; ?></td>
                            <td><?= $row['keterangan']; ?></td>
                            <td>
                                <a href="poli.php?edit=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="poli.php?delete=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus poli ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada data poli</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
  </div>

  <?php include('../footer/footer.php'); ?>
</div>
</body>
</html>
