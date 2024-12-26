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

// Query untuk mendapatkan data periksa
$query_periksa = $conn->query("
    SELECT p.id AS id_periksa, p.tgl_periksa, p.catatan, p.biaya_periksa, dp.id AS id_daftar_poli, pa.nama AS nama_pasien
    FROM periksa p
    JOIN daftar_poli dp ON p.id_daftar_poli = dp.id
    JOIN pasien pa ON dp.id_pasien = pa.id
    WHERE p.id_daftar_poli = '$id_daftar_poli'
");

if ($query_periksa->num_rows == 0) {
    die("Data periksa tidak ditemukan.");
}

$data = $query_periksa->fetch_assoc();

// Query obat
$query_obat = $conn->query("SELECT id, nama_obat, kemasan, harga FROM obat");

// Proses Update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tgl_periksa = $_POST['tgl_periksa'];
    $catatan = $_POST['catatan'];
    $total_harga = $_POST['total_harga'];
    $obat_terpilih = isset($_POST['obat']) ? $_POST['obat'] : [];

    // Update tabel periksa
    $conn->query("UPDATE periksa SET tgl_periksa = '$tgl_periksa', catatan = '$catatan', biaya_periksa = '$total_harga' WHERE id_daftar_poli = '$id_daftar_poli'");

    // Update relasi obat: Hapus dulu obat lama, lalu masukkan yang baru
    $conn->query("DELETE FROM periksa_obat WHERE id_periksa = '{$data['id_periksa']}'");
    foreach ($obat_terpilih as $id_obat) {
        $conn->query("INSERT INTO periksa_obat (id_periksa, id_obat) VALUES ('{$data['id_periksa']}', '$id_obat')");
    }

    echo "<script>alert('Data berhasil diperbarui!'); window.location.href = 'daftar_periksa.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Pemeriksaan</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        input, textarea, select, button { width: 100%; margin-bottom: 10px; padding: 8px; }
        button { background-color: #007bff; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        label { font-weight: bold; }
        #total_display { font-size: 1.2em; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Edit Pemeriksaan</h1>
    <form method="POST">
        <!-- Nama Pasien -->
        <label for="nama_pasien">Nama Pasien:</label>
        <input type="text" value="<?php echo htmlspecialchars($data['nama_pasien']); ?>" readonly>

        <!-- Tanggal Periksa -->
        <label for="tgl_periksa">Tanggal Periksa:</label>
        <input type="datetime-local" name="tgl_periksa" value="<?php echo $data['tgl_periksa']; ?>" required>

        <!-- Catatan -->
        <label for="catatan">Catatan:</label>
        <textarea name="catatan" rows="4" required><?php echo htmlspecialchars($data['catatan']); ?></textarea>

        <!-- Obat -->
        <label for="obat">Obat:</label>
        <select name="obat[]" id="obat" multiple="multiple" style="width: 100%;">
            <?php while ($obat = $query_obat->fetch_assoc()): ?>
                <option value="<?php echo $obat['id']; ?>" data-harga="<?php echo $obat['harga']; ?>">
                    <?php echo htmlspecialchars($obat['nama_obat'] . " - " . $obat['kemasan'] . " (Rp " . number_format($obat['harga'], 0, ',', '.') . ")"); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <!-- Total Harga -->
        <label>Total Harga:</label>
        <span id="total_display">Rp <?php echo number_format($data['biaya_periksa'], 0, ',', '.'); ?></span>
        <input type="hidden" name="total_harga" id="total_harga" value="<?php echo $data['biaya_periksa']; ?>">

        <!-- Tombol Simpan -->
        <button type="submit">Simpan Perubahan</button>
    </form>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#obat').select2({ placeholder: "Pilih Obat", allowClear: true });

            // Hitung total biaya termasuk dokter
            $('#obat').on('change', function() {
                let total = 200000; // Biaya jasa dokter
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
