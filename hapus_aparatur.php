<?php

include 'koneksi.php';

$id=$_GET['id'];

mysqli_query($koneksi,"DELETE FROM aparatur WHERE id_aparatur='$id'");

header("location:aparatur.php");

?>