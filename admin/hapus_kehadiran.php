<?php
require_once '../includes/auth.php';
require_once '../includes/koneksi.php';
$id = (int) $_GET['id'];
mysqli_query($koneksi, "DELETE FROM kehadiran WHERE id_kehadiran = $id");
header("Location: kehadiran.php?pesan=hapus");
exit;