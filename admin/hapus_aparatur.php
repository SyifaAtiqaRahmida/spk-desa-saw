<?php
require_once '../includes/auth.php';
require_once '../includes/koneksi.php';
$id = (int) $_GET['id'];
mysqli_query($koneksi, "DELETE FROM aparatur WHERE id_aparatur = $id");
header("Location: aparatur.php?pesan=hapus");
exit;