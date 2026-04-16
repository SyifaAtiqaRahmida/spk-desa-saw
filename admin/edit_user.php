<?php
require_once '../includes/auth.php';
require_once '../includes/koneksi.php';
$active = 'user';
$id     = (int) $_GET['id'];
$data   = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM user WHERE id_user = $id"));
$error  = '';
if (!$data) { header("Location: kelola_user.php"); exit; }
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $cek = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username' AND id_user != $id"));
    if ($cek) {
        $error = 'Username sudah dipakai user lain!';
    } else {
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            mysqli_query($koneksi, "UPDATE user SET nama='$nama', username='$username', password='$password' WHERE id_user=$id");
        } else {
            mysqli_query($koneksi, "UPDATE user SET nama='$nama', username='$username' WHERE id_user=$id");
        }
        header("Location: kelola_user.php?pesan=edit"); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit User</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="layout">
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-brand-name">SPK Desa Tatah Mesjid</div>
      <div class="sidebar-brand-sub">Sistem Pendukung Keputusan</div>
    </div>
    <nav class="sidebar-nav">
      <div class="nav-section">Menu Utama</div>
      <a href="index.php">&#9632; Dashboard</a>
      <a href="aparatur.php">&#9632; Data Aparatur</a>
      <a href="kriteria.php">&#9632; Data Kriteria</a>
      <a href="kehadiran.php">&#9632; Kehadiran</a>
      <a href="penilaian.php">&#9632; Penilaian</a>
      <a href="perhitungan_saw.php">&#9632; Perhitungan SAW</a>
      <a href="kelola_user.php" class="active">&#9632; Kelola User</a>
      <div class="nav-section">Akun</div>
      <a href="../logout.php" style="color:rgba(255,100,100,0.7);">&#9632; Logout</a>
    </nav>
    <div class="sidebar-footer">Login sebagai: <strong style="color:var(--green-light);"><?php echo htmlspecialchars($_SESSION['nama']); ?></strong></div>
  </aside>
  <div class="main">
    <header class="topbar">
      <div class="topbar-title">Edit User</div>
      <div class="topbar-right">
        <span class="topbar-user"><?php echo htmlspecialchars($_SESSION['nama']); ?></span>
        <div class="avatar"><?php echo strtoupper(substr($_SESSION['nama'], 0, 1)); ?></div>
      </div>
    </header>
    <main class="content">
      <div class="breadcrumb">
        <a href="index.php">Beranda</a><span class="breadcrumb-sep">/</span>
        <a href="kelola_user.php">Kelola User</a><span class="breadcrumb-sep">/</span>
        <span class="breadcrumb-active">Edit</span>
      </div>
      <div class="page-header">
        <div><div class="page-title">Edit User</div>
        <div class="page-sub">Perbarui data: <strong><?php echo htmlspecialchars($data['nama']); ?></strong></div></div>
      </div>
      <div class="card" style="max-width:520px;">
        <div class="card-head"><div class="card-head-title">Form Edit User</div></div>
        <div class="card-body">
          <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
          <form method="POST">
            <div class="form-group">
              <label class="form-label">Nama Lengkap</label>
              <input type="text" name="nama" class="form-control" value="<?php echo htmlspecialchars($data['nama']); ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">Username</label>
              <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($data['username']); ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">Password Baru</label>
              <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin ganti password">
              <div class="form-hint">Isi hanya jika ingin mengganti password</div>
            </div>
            <div class="form-actions">
              <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
              <a href="kelola_user.php" class="btn btn-ghost">Batal</a>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</div>
</body>
</html>