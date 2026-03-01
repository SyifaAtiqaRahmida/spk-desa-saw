<?php
include 'koneksi.php';
?>

<!DOCTYPE html>
<html>
<head>
<title>Tambah Aparatur</title>
</head>

<body>

<h2>Tambah Data Aparatur</h2>

<form method="POST">

Nama Aparatur <br>
<input type="text" name="nama" required>
<br><br>

Jabatan <br>
<input type="text" name="jabatan" required>
<br><br>

<input type="submit" name="simpan" value="Simpan">

</form>

<?php

if(isset($_POST['simpan'])){

$nama=$_POST['nama'];
$jabatan=$_POST['jabatan'];

mysqli_query($koneksi,"INSERT INTO aparatur(nama,jabatan) VALUES('$nama','$jabatan')");

echo "Data berhasil disimpan";

}

?>

<br><br>

<a href="aparatur.php">Kembali</a>

</body>
</html>