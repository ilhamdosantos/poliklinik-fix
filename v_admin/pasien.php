<?php
$conn = new mysqli("localhost", "root", "", "db_poli"); // Ganti dengan kredensial database Anda

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Generate No RM
$result = $conn->query("SELECT COUNT(*) as total FROM pasien");
$row = $result->fetch_assoc();
$next_id = isset($row['total']) ? $row['total'] + 1 : 1; // Total data + 1
$no_rm = date("Ym") . '-' . str_pad($next_id, 3, '0', STR_PAD_LEFT);

// Tambah/Edit Pasien
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? $_POST['id'] : '';
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $no_ktp = $_POST['no_ktp'];
    $no_hp = $_POST['no_hp'];
    $no_rm = $_POST['no_rm'];

    if (!empty($id)) {
        // Edit Pasien
        $sql = "UPDATE pasien SET nama=?, alamat=?, no_ktp=?, no_hp=?, no_rm=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $nama, $alamat, $no_ktp, $no_hp, $no_rm, $id);
    } else {
        // Tambah Pasien Baru
        $sql = "INSERT INTO pasien (nama, alamat, no_ktp, no_hp, no_rm) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $nama, $alamat, $no_ktp, $no_hp, $no_rm);
    }

    if ($stmt->execute()) {
        header("Location: pasien.php");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}

// Hapus Pasien
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM pasien WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<script>alert('Data pasien berhasil dihapus!'); window.location.href='pasien.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "'); window.location.href='pasien.php';</script>";
    }
}

// Ambil Data Pasien
$pasien = $conn->query("SELECT * FROM pasien");
$edit_data = null;

// Ambil data untuk edit jika ada
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $sql = "SELECT * FROM pasien WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_data = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<?php
session_start(); 
include('../header/header.php') ?>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  <?php include('../preloader/preloader.php')?>

  <!-- Navbar -->
  <?php include('../navbar/navbar_admin.php') ?>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <?php include('../logo/logo.php') ?>

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
                  <i class="nav-icon  fas fa-user-md"></i>
                  <p>Dokter</p>
                </a>
              </li>
              <li class="nav-item">
              <a href="../v_admin/pasien.php" class="nav-link active">
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

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
  <link rel="stylesheet" href="../style/admin_pasien.css">
    <!-- Main content -->
    <div class="container-fluid mt-10">
    <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Pasien</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard v1</li>
            </ol>
          </div>
        </div>
        <h2 class="text-center mb-4">Manajemen Pasien</h2>

        <form method="POST">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="nama" class="form-label">Nama Pasien:</label>
            <input type="text" class="form-control" id="nama" name="nama" value="<?php echo $edit_data['nama'] ?? ''; ?>" required>
        </div>
        <div class="col-md-6 mb-3">
            <label for="no_hp" class="form-label">Nomor HP:</label>
            <input type="text" class="form-control" id="no_hp" name="no_hp" value="<?php echo $edit_data['no_hp'] ?? ''; ?>" required>
        </div>
    </div>

    <div class="mb-3">
        <label for="alamat" class="form-label">Alamat:</label>
        <input type="text" class="form-control" id="alamat" name="alamat" value="<?php echo $edit_data['alamat'] ?? ''; ?>" required>
    </div>

    <div class="mb-3">
        <label for="no_ktp" class="form-label">Nomor KTP:</label>
        <input type="text" class="form-control" id="no_ktp" name="no_ktp" value="<?php echo $edit_data['no_ktp'] ?? ''; ?>" required>
    </div>

    <div class="mb-3">
        <label for="no_rm" class="form-label">Nomor RM:</label>
        <input type="text" class="form-control" id="no_rm" name="no_rm" value="<?php echo $edit_data['no_rm'] ?? $no_rm; ?>" readonly>
    </div>

    <?php if ($edit_data): ?>
        <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
    <?php endif; ?>

    <button type="submit" class="btn btn-success w-100">Simpan</button>
</form>

        <h3 class="mt-5 mb-3">Daftar Pasien</h3>
                <table class="table table-striped">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Alamat</th>
                            <th>No. KTP</th>
                            <th>No. HP</th>
                            <th>No. RM</th>
                            <th>Aksi</th>
                        </tr>
                    <tbody>
                    <?php 
    $no = 1; 
    if ($pasien->num_rows > 0): 
        while ($row = $pasien->fetch_assoc()): 
    ?>
        <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo $row['nama']; ?></td>
            <td><?php echo $row['alamat']; ?></td>
            <td><?php echo $row['no_ktp']; ?></td>
            <td><?php echo $row['no_hp']; ?></td>
            <td><?php echo $row['no_rm']; ?></td>
            <td>
                <a href="pasien.php?edit=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="pasien.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</a>
            </td>
        </tr>
    <?php 
        endwhile; 
    else: 
    ?>
        <tr>
            <td colspan="7" class="text-center">Tidak ada data pasien.</td>
        </tr>
    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <?php include('../footer/footer.php') ?>
  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->
</body>
</html>