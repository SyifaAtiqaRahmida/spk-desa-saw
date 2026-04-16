<?php
require_once '../includes/auth.php';
require_once '../includes/koneksi.php';
$id = (int) $_GET['id'];
mysqli_query($koneksi, "DELETE FROM kriteria WHERE id_kriteria = $id");
header("Location: kriteria.php?pesan=hapus");
exit;