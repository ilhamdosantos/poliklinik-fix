
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_poli"; // ganti dengan nama database Anda

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Proses penghapusan dokter
if (isset($_GET['hapus_id'])) {
    $id_dokter = $_GET['hapus_id'];

    $sql_hapus = "DELETE FROM dokter WHERE id = ?";
    $stmt = $conn->prepare($sql_hapus);
    $stmt->bind_param("i", $id_dokter);

    if ($stmt->execute()) {
        echo "<script>alert('Dokter berhasil dihapus'); window.location.href='dokter.php';</script>";
    } else {
        echo "<div class='alert alert-danger mt-3'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
}

// Proses pengeditan dokter
if (isset($_GET['edit_id'])) {
    $id_dokter = $_GET['edit_id'];

    $sql = "SELECT * FROM dokter WHERE id = $id_dokter";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        echo "Data tidak ditemukan.";
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nama_dokter = $_POST['nama_dokter'];
        $no_hp = $_POST['no_hp'];
        $alamat = $_POST['alamat'];
        $id_poli = $_POST['id_poli'];

        $sql_update = "UPDATE dokter SET nama_dokter='$nama_dokter', no_hp='$no_hp', alamat='$alamat', id_poli='$id_poli' WHERE id=$id_dokter";

        if ($conn->query($sql_update) === TRUE) {
            echo "<script>alert('Data berhasil diperbarui'); window.location.href='dokter.php';</script>";
        } else {
            echo "<div class='alert alert-danger mt-3'>Error: " . $conn->error . "</div>";
        }
    }
}

// Ambil data dokter dari database
$sql = "SELECT dokter.id, dokter.nama_dokter, dokter.alamat, dokter.no_hp, poli.nama_poli 
        FROM dokter
        JOIN poli ON dokter.id_poli = poli.id";
$result = $conn->query($sql);

// Ambil data poli untuk dropdown
$sql_poli = "SELECT * FROM poli";
$result_poli = $conn->query($sql_poli);

// Proses form untuk menambah dokter
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_GET['edit_id'])) {
    if (isset($_POST['nama_dokter'], $_POST['alamat'], $_POST['no_hp'], $_POST['id_poli'])) {
        $nama_dokter = $_POST['nama_dokter'];
        $alamat = $_POST['alamat'];
        $no_hp = $_POST['no_hp'];
        $id_poli = $_POST['id_poli'];

        $sql_insert = "INSERT INTO dokter (nama_dokter, alamat, no_hp, id_poli) 
                       VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql_insert);
        $stmt->bind_param("sssi", $nama_dokter, $alamat, $no_hp, $id_poli);

        if ($stmt->execute()) {
            echo "<script>alert('Dokter berhasil ditambahkan'); window.location.href='dokter.php';</script>";
        } else {
            echo "<div class='alert alert-danger mt-3'>Error: " . $stmt->error . "</div>";
        }

        $stmt->close();
    } else {
        echo "<div class='alert alert-warning mt-3'>Semua field harus diisi!</div>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<?php
session_start(); 
include('../header/header.php') ?>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

 <?php include('../preloader/preloader.php')?>
<?php include('../navbar/navbar_admin.php') ?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <?php include('../logo/logo.php') ?>

    <div class="sidebar">
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
        <img src="../img/giselle.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block"><?php echo $_SESSION['nama'];?></a>
        </div>
      </div>

      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item menu-open">
          <a href="../v_admin/dashboard.php" class="nav-link ">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard <span class="badge badge-info right">Admin</span></p>
            </a>
          <li class="nav-item">
              <a href="../v_admin/dokter.php" class="nav-link active">
                  <span class="badge badge-info right">Admin</span>
                  <i class="nav-icon fas fa-user-md"></i>
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
          <li class="nav-item">
            <a href="../v_admin/obat.php" class="nav-link">
            <i class="nav-icon fas fa-pills"></i>
              <p>Obat <span class="badge badge-info right">Admin</span></p>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </aside>

  <div class="content-wrapper">
    <link rel="stylesheet" href="../style/admin_dokter.css">
    <div class="container-fluid mt-10">
    <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"><?php echo isset($_GET['edit_id']) ? 'Edit Dokter' : 'Tambah Dokter'; ?></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard v1</li>
            </ol>
          </div>
        </div>

    <h2 class="text-center mb-4"><?php echo isset($_GET['edit_id']) ? 'Edit Dokter' : 'Tambah Dokter'; ?></h2>
    <form method="POST">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="nama_dokter" class="form-label">Nama Dokter:</label>
                <input type="text" class="form-control" id="nama_dokter" name="nama_dokter" value="<?php echo isset($row['nama_dokter']) ? $row['nama_dokter'] : ''; ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="no_hp" class="form-label">No HP:</label>
                <input type="text" class="form-control" id="no_hp" name="no_hp" value="<?php echo isset($row['no_hp']) ? $row['no_hp'] : ''; ?>" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat:</label>
            <textarea class="form-control" id="alamat" name="alamat" rows="3" required><?php echo isset($row['alamat']) ? $row['alamat'] : ''; ?></textarea>
        </div>

        <div class="mb-3">
            <label for="id_poli" class="form-label">Pilih Poli:</label>
            <select class="form-select" id="id_poli" name="id_poli" required>
                <option value="">Pilih Poli</option>
                <?php
                $conn = new mysqli($servername, $username, $password, $dbname);
                $sql_poli = "SELECT * FROM poli";
                $result_poli = $conn->query($sql_poli);
                while ($row_poli = $result_poli->fetch_assoc()) {
                    $selected = (isset($row['id_poli']) && $row['id_poli'] == $row_poli['id']) ? 'selected' : '';
                    echo "<option value='" . $row_poli['id'] . "' $selected>" . $row_poli['nama_poli'] . "</option>";
                }
                ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary w-100">Simpan</button>
    </form>

    <h3 class="mt-5 mb-3">Daftar Dokter</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>No. HP</th>
                <th>Poli</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
    if ($result->num_rows > 0) {
        $no = 1;
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $no . "</td>";
            echo "<td>" . $row['nama_dokter'] . "</td>";
            echo "<td>" . $row['alamat'] . "</td>";
            echo "<td>" . $row['no_hp'] . "</td>";
            echo "<td>" . $row['nama_poli'] . "</td>";
            echo "<td>
                    <a href='dokter.php?edit_id=" . $row['id'] . "' class='btn btn-warning btn-sm'>Edit</a>
                    <a href='dokter.php?hapus_id=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Apakah Anda yakin ingin menghapus dokter ini?\")'>Hapus</a>
                  </td>";
            echo "</tr>";
            $no++;
        }
    } else {
        echo "<tr><td colspan='6' class='text-center'>Tidak ada dokter yang terdaftar</td></tr>";
    }
?>
        </tbody>
    </table>
</div>
  </div>
  <?php include('../footer/footer.php') ?>
  <aside class="control-sidebar control-sidebar-dark">
  </aside>
</div>
</body>
</html>
