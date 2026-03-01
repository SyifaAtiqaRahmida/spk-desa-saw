<?php
include 'koneksi.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Aparatur Desa</title>
</head>

<body>

<h2>Data Aparatur Desa Tatah Mesjid</h2>

<table border="1" cellpadding="10">

<tr>
    <th>No</th>
    <th>Nama</th>
    <th>Jabatan</th>
    <th>Aksi</th>
</tr>

<?php

$no=1;

$data=mysqli_query($koneksi,"SELECT * FROM aparatur");

while($d=mysqli_fetch_array($data)){
?>

<tr>

<td><?php echo $no++; ?></td>

<td><?php echo $d['nama']; ?></td>

<td><?php echo $d['jabatan']; ?></td>
<td>

<a href="edit_aparatur.php?id=<?php echo $d['id_aparatur']; ?>">
Edit
</a>

|

<a href="hapus_aparatur.php?id=<?php echo $d['id_aparatur']; ?>">
Hapus
</a>

</td>
</tr>

<?php
}
?>

</table>

</body>
</html>