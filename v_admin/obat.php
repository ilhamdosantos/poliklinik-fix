<?php
session_start();

// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_poli";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Inisialisasi variabel
$edit_mode = false;
$edit_data = null;

// **Tambah/Edit Obat**
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_obat = $_POST['nama_obat'];
    $kemasan = $_POST['kemasan'] ? $_POST['kemasan'] : NULL;
    $harga = $_POST['harga'];

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update Obat
        $id = $_POST['id'];
        $sql_update = "UPDATE obat SET nama_obat = ?, kemasan = ?, harga = ? WHERE id = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("ssii", $nama_obat, $kemasan, $harga, $id);
        if ($stmt->execute()) {
            echo "<script>alert('Data obat berhasil diperbarui!'); window.location.href='obat.php';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        // Tambah Obat Baru
        $sql_insert = "INSERT INTO obat (nama_obat, kemasan, harga) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql_insert);
        $stmt->bind_param("ssi", $nama_obat, $kemasan, $harga);
        if ($stmt->execute()) {
            echo "<script>alert('Obat berhasil ditambahkan!'); window.location.href='obat.php';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}

// **Edit Obat**
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $sql = "SELECT * FROM obat WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $edit_data = $result->fetch_assoc();
        $edit_mode = true;
    } else {
        echo "Data obat tidak ditemukan.";
    }
}

// **Hapus Obat**
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql_delete = "DELETE FROM obat WHERE id = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<script>alert('Data obat berhasil dihapus!'); window.location.href='obat.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// **Ambil Data Obat**
$sql = "SELECT * FROM obat";
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
              <a href="../v_admin/poli.php" class="nav-link">
                <span class="badge badge-info right">Admin</span>
                <i class="nav-icon fas fa-hospital"></i>
                  <p>Poli</p>
                </a>
              </li>
          </li>
          <li class="nav-item">
            <a href="../v_admin/obat.php" class="nav-link active">
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
  <link rel="stylesheet" href="../style/admin_obat.css">
    <div class="container-fluid mt-5">
        <h2 class="text-center mb-4"><?= $edit_mode ? 'Edit Obat' : 'Tambah Obat'; ?></h2>

        <!-- Form Tambah/Edit -->
        <form method="POST">
            <div class="form-group">
                <label for="nama_obat">Nama Obat</label>
                <input type="text" class="form-control" id="nama_obat" name="nama_obat" value="<?= $edit_data['nama_obat'] ?? ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="kemasan">Kemasan</label>
                <input type="text" class="form-control" id="kemasan" name="kemasan" value="<?= $edit_data['kemasan'] ?? ''; ?>">
            </div>
            <div class="form-group">
                <label for="harga">Harga (Rp)</label>
                <input type="number" class="form-control" id="harga" name="harga" value="<?= $edit_data['harga'] ?? ''; ?>" required>
            </div>
            <?php if ($edit_mode): ?>
                <input type="hidden" name="id" value="<?= $edit_data['id']; ?>">
            <?php endif; ?>
            <button type="submit" class="btn btn-primary w-100"><?= $edit_mode ? 'Simpan Perubahan' : 'Tambah Obat'; ?></button>
        </form>

        <!-- Daftar Obat -->
        <h3 class="mt-5 mb-3">Data Obat</h3>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Obat</th>
                    <th>Kemasan</th>
                    <th>Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php $no = 1; while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $row['nama_obat']; ?></td>
                            <td><?= $row['kemasan']; ?></td>
                            <td>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
                            <td>
                                <a href="obat.php?edit=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                <a href="obat.php?delete=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus obat ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data obat</td>
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
