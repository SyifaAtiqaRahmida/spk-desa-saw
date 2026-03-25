<?php
include 'koneksi.php';

$id=$_GET['id'];

$data=mysqli_query($koneksi,"SELECT * FROM kriteria WHERE id_kriteria='$id'");
$d=mysqli_fetch_array($data);
?>

<h2>Edit Kriteria</h2>

<form method="POST">

Nama Kriteria <br>
<input type="text" name="nama_kriteria" value="<?php echo $d['nama_kriteria']; ?>">
<br><br>

Bobot <br>
<input type="text" name="bobot" value="<?php echo $d['bobot']; ?>">
<br><br>

Atribut <br>
<select name="atribut">

<option value="benefit">Benefit</option>
<option value="cost">Cost</option>

</select>

<br><br>

<input type="submit" name="update" value="Update">

</form>

<?php

if(isset($_POST['update'])){

$nama=$_POST['nama_kriteria'];
$bobot=$_POST['bobot'];
$atribut=$_POST['atribut'];

mysqli_query($koneksi,"UPDATE kriteria SET
nama_kriteria='$nama',
bobot='$bobot',
atribut='$atribut'
WHERE id_kriteria='$id'");

echo "Data berhasil diupdate";

}

?>

<br><br>

<a href="kriteria.php">Kembali</a>