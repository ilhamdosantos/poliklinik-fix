<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_poli";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil ID daftar poli
$id_daftar_poli = isset($_GET['id']) ? $_GET['id'] : 0;

// Query pasien
$query_pasien = $conn->query("
    SELECT dp.id, pa.nama AS nama_pasien 
    FROM daftar_poli dp
    JOIN pasien pa ON dp.id_pasien = pa.id
    WHERE dp.id = '$id_daftar_poli'
");
if (!$query_pasien) die("Error query pasien: " . $conn->error);

$data_pasien = $query_pasien->fetch_assoc();

// Query obat
$query_obat = $conn->query("SELECT id, nama_obat, kemasan, harga FROM obat");
if (!$query_obat) {
    die("Error query obat: " . $conn->error);
}

// Proses simpan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_daftar_poli = $_POST['id_daftar_poli'];
    $tgl_periksa = $_POST['tgl_periksa'];
    $catatan = $_POST['catatan'];
    $total_harga = $_POST['total_harga']; // Ambil langsung dari input yang sudah dihitung
    $obat_terpilih = isset($_POST['obat']) ? $_POST['obat'] : [];

    // Simpan ke tabel periksa
    $sql = "INSERT INTO periksa (id_daftar_poli, tgl_periksa, catatan, biaya_periksa) 
            VALUES ('$id_daftar_poli', '$tgl_periksa', '$catatan', '$total_harga')";
    if ($conn->query($sql) === TRUE) {
        $id_periksa = $conn->insert_id;

        // Simpan data obat ke tabel relasi
        foreach ($obat_terpilih as $id_obat) {
            $conn->query("INSERT INTO detail_periksa (id_periksa, id_obat) VALUES ('$id_periksa', '$id_obat')");
        }

        // Update status di tabel daftar_poli menggunakan tgl_periksa
        $conn->query("UPDATE daftar_poli SET status = 'Sudah diperiksa', waktu_periksa = '$tgl_periksa' WHERE id = '$id_daftar_poli'");

        echo "<script>alert('Data berhasil disimpan dan status diperbarui!'); window.location.href = 'daftar_periksa.php';</script>";
        exit();
    } else {
        die("Error insert: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Dashboard</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../app/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="../app/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="../app/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="../app/plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../app/dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="../app/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="../app/plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="../app/plugins/summernote/summernote-bs4.min.css">
  <link rel="stylesheet" href="../style/navbar.css"> <!-- Pastikan path ke CSS benar -->
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        input, textarea, select, button { width: 100%; margin-bottom: 10px; padding: 8px; }
        button { background-color: #28a745; color: #fff; border: none; cursor: pointer; }
        button:hover { background-color: #218838; }
        label { font-weight: bold; }
        #total_display { font-size: 1.2em; font-weight: bold; }
    .nav-link.active {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    
        <h1>Periksa Pasien</h1>
        <form method="POST">
            <label for="nama_pasien">Nama Pasien:</label>
            <input type="text" value="<?php echo htmlspecialchars($data_pasien['nama_pasien']); ?>" readonly>
            <input type="hidden" name="id_daftar_poli" value="<?php echo $data_pasien['id']; ?>">

            <label for="tgl_periksa">Tanggal dan Waktu Periksa:</label>
            <input type="datetime-local" name="tgl_periksa" required>

            <label for="catatan">Catatan:</label>
            <textarea name="catatan" rows="4" required></textarea>

            <label for="obat">Obat:</label>
            <select name="obat[]" id="obat" multiple="multiple" style="width: 100%;">
                <?php while ($obat = $query_obat->fetch_assoc()): ?>
                    <option value="<?php echo $obat['id']; ?>" data-harga="<?php echo $obat['harga']; ?>">
                        <?php echo htmlspecialchars($obat['nama_obat'] . " - " . $obat['kemasan'] . " (Rp " . number_format($obat['harga'], 0, ',', '.') . ")"); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Total Harga:</label>
            <span id="total_display">Rp 0</span>
            <input type="hidden" name="total_harga" id="total_harga" value="0">

            <button type="submit">Simpan</button>
        </form>

    <script>
       $(document).ready(function() {
    $('#obat').select2({
        placeholder: "Pilih Obat",
        allowClear: true
    });

    $('#obat').on('change', function() {
        let total = 200000;
        $('#obat option:selected').each(function() {
            total += parseInt($(this).data('harga')) || 0;
        });
        $('#total_harga').val(total);
        $('#total_display').text('Rp ' + total.toLocaleString());
    });
});
    </script>
</body>
</html>
<?php $conn->close(); ?>
