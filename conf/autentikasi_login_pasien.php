<?php
session_start();
include ('config.php');
$nama =$_POST['nama'];
$alamat =$_POST['alamat'];

$query = mysqli_query($koneksi,"SELECT * FROM pasien WHERE nama='$nama' AND alamat='$alamat'");
if(mysqli_num_rows($query)==1){
    header('Location:../v_pasien/dashboard.php');
    $user = mysqli_fetch_array($query);
    $_SESSION['nama'] = $user['nama'];

}
else if($username == '' && $password ==''){
    header('Location:../login_pasien.php?error=2');
}
else{
    header('Location:../login_pasien.php?error=1');
}
?>
