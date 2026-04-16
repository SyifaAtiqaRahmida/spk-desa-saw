<?php
require_once '../includes/auth.php';
require_once '../includes/koneksi.php';
$id = (int) $_GET['id'];
if ($id == $_SESSION['user']) { header("Location: kelola_user.php"); exit; }
mysqli_query($koneksi, "DELETE FROM user WHERE id_user = $id");
header("Location: kelola_user.php?pesan=hapus");
exit;