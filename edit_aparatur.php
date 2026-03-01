<?php
include 'koneksi.php';

$id=$_GET['id'];

$data=mysqli_query($koneksi,"SELECT * FROM aparatur WHERE id_aparatur='$id'");
$d=mysqli_fetch_array($data);
?>

<h2>Edit Aparatur</h2>

<form method="POST">

Nama <br>
<input type="text" name="nama" value="<?php echo $d['nama']; ?>">
<br><br>

Jabatan <br>
<input type="text" name="jabatan" value="<?php echo $d['jabatan']; ?>">
<br><br>

<input type="submit" name="update" value="Update">

</form>

<?php

if(isset($_POST['update'])){

$nama=$_POST['nama'];
$jabatan=$_POST['jabatan'];

mysqli_query($koneksi,"UPDATE aparatur SET
nama='$nama',
jabatan='$jabatan'
WHERE id_aparatur='$id'");

echo "Data berhasil diupdate";

}

?>

<br><br>

<a href="aparatur.php">Kembali</a>