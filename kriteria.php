<?php
include 'koneksi.php';
?>

<!DOCTYPE html>
<html>
<head>
<title>Data Kriteria</title>
</head>

<body>

<h2>Data Kriteria Penilaian</h2>

<a href="tambah_kriteria.php">Tambah Kriteria</a>

<br><br>

<table border="1" cellpadding="10">

<tr>
<th>No</th>
<th>Nama Kriteria</th>
<th>Bobot</th>
<th>Atribut</th>
<th>Aksi</th>
</tr>

<?php

$no=1;
$data=mysqli_query($koneksi,"SELECT * FROM kriteria");

while($d=mysqli_fetch_array($data)){

?>

<tr>

<td><?php echo $no++; ?></td>
<td><?php echo $d['nama_kriteria']; ?></td>
<td><?php echo $d['bobot']; ?></td>
<td><?php echo $d['atribut']; ?></td>

<td>

<a href="edit_kriteria.php?id=<?php echo $d['id_kriteria']; ?>">Edit</a>

|

<a href="hapus_kriteria.php?id=<?php echo $d['id_kriteria']; ?>">Hapus</a>

</td>

</tr>

<?php
}
?>

</table>

</body>
</html>