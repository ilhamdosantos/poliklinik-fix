<?php
  $title = "Landing Page Poliklinik"; // Variabel untuk judul halaman
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="style/index.css"> <!-- Pastikan file CSS tersimpan dengan nama style.css -->
</head>
<body>

    <header class="header">
        <div class="logo">
            <h1>Poliklinik</h1> <!-- Judul Poliklinik di pojok kiri atas -->
        </div>
    </header>

    <section class="hero">
        <h2>SISTEM TEMU JANJI <br> PASIEN - DOKTER</h2>
        <p>Bimbingan Karir 2024</p>
    </section>

    <section class="menu">
        <div class="menu-item">
        <img src="img/pasien2.jpg" alt="Product Image" style="width: 90px; height: auto;">
            <h3>Registrasi Sebagai Pasien</h3>
            <p>Apabila anda adalah seorang Pasien, Silahkan Registrasi terlebih dahulu untuk mulai berobat !!</p>
            <a href="register_pasien.php">
                <button class="btn">REGISTRASI</button>
            </a>
        </div>
        <div class="menu-item">
        <img src="img/dokter4.jpg" alt="Product Image" style="width: 90px; height: auto;">
            <h3>Login Sebagai Dokter</h3>
            <p>Apabila anda adalah seorang Dokter, silahkan Login terlebih dahulu untuk mulai melayani Pasien!</p>
            <a href="login_dokter.php">
                <button class="btn">LOG IN</button>
            </a>
        </div>
        <div class="menu-item">
        <img src="img/admin3.png" alt="Product Image" style="width: 90px; height: auto;">
            <h3>Login Sebagai Admin</h3>
            <p>Apabila anda adalah seorang Admin, Silahkan login terlebih dahulu untuk mengelola semuanya!!</p>
            <a href="login_admin.php">
                <button class="btn">LOG IN</button>
            </a>
        </div>
    </section>

</body>
</html>