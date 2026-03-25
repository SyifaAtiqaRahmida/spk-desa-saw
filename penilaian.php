<?php
include 'koneksi.php';
?>

<h2>Input Penilaian Aparatur</h2>

<form method="POST">

Pilih Aparatur <br>

<select name="id_aparatur">

<?php
$aparatur=mysqli_query($koneksi,"SELECT * FROM aparatur");

while($a=mysqli_fetch_array($aparatur)){
?>

<option value="<?php echo $a['id_aparatur']; ?>">
<?php echo $a['nama']; ?>
</option>

<?php } ?>

</select>

<br><br>

Pilih Kriteria <br>

<select name="id_kriteria">

<?php
$kriteria=mysqli_query($koneksi,"SELECT * FROM kriteria");

while($k=mysqli_fetch_array($kriteria)){
?>

<option value="<?php echo $k['id_kriteria']; ?>">
<?php echo $k['nama_kriteria']; ?>
</option>

<?php } ?>

</select>

<br><br>

Nilai <br>

<input type="number" name="nilai" required>

<br><br>

<input type="submit" name="simpan" value="Simpan">

</form>

<?php

if(isset($_POST['simpan'])){

$id_aparatur=$_POST['id_aparatur'];
$id_kriteria=$_POST['id_kriteria'];
$nilai=$_POST['nilai'];

// INSERT yang benar (tanpa id kosong)
mysqli_query($koneksi,"INSERT INTO penilaian(id_aparatur,id_kriteria,nilai) VALUES('$id_aparatur','$id_kriteria','$nilai')");

echo "<br>Penilaian berhasil disimpan";

}

?>

<hr>

<h3>Data Penilaian</h3>

<table border="1" cellpadding="10">

<tr>
<th>No</th>
<th>Nama Aparatur</th>
<th>Kriteria</th>
<th>Nilai</th>
</tr>

<?php
$no=1;

$data=mysqli_query($koneksi,"
SELECT penilaian.*, aparatur.nama, kriteria.nama_kriteria
FROM penilaian
JOIN aparatur ON penilaian.id_aparatur=aparatur.id_aparatur
JOIN kriteria ON penilaian.id_kriteria=kriteria.id_kriteria
");

while($d=mysqli_fetch_array($data)){
?>

<tr>
<td><?php echo $no++; ?></td>
<td><?php echo $d['nama']; ?></td>
<td><?php echo $d['nama_kriteria']; ?></td>
<td><?php echo $d['nilai']; ?></td>
</tr>

<?php } ?>

</table>