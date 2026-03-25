<?php
include 'koneksi.php';
?>

<h2>Tambah Kriteria</h2>

<form method="POST">

Nama Kriteria <br>
<input type="text" name="nama_kriteria">
<br><br>

Bobot <br>
<input type="text" name="bobot">
<br><br>

Atribut <br>
<select name="atribut">
<option value="benefit">Benefit</option>
<option value="cost">Cost</option>
</select>

<br><br>

<input type="submit" name="simpan" value="Simpan">

</form>

<?php

if(isset($_POST['simpan'])){

$nama=$_POST['nama_kriteria'];
$bobot=$_POST['bobot'];
$atribut=$_POST['atribut'];

mysqli_query($koneksi,"INSERT INTO kriteria(nama_kriteria,bobot,atribut) VALUES('$nama','$bobot','$atribut')");

echo "Data berhasil disimpan";

}

?>

<br><br>

<a href="kriteria.php">Kembali</a>