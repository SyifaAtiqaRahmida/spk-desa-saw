<?php

include 'koneksi.php';

$id=$_GET['id'];

mysqli_query($koneksi,"DELETE FROM kriteria WHERE id_kriteria='$id'");

header("location:kriteria.php");

?>