<?php
require_once '../includes/auth.php';
require_once '../includes/koneksi.php';
$id = (int) $_GET['id'];
mysqli_query($koneksi, "DELETE FROM penilaian WHERE id_penilaian = $id");
header("Location: penilaian.php?pesan=hapus");
exit;