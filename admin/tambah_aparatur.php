<?php
require_once '../includes/auth.php';
require_once '../includes/koneksi.php';
$active = 'aparatur';
$error  = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jabatan  = mysqli_real_escape_string($koneksi, $_POST['jabatan']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $cek = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username'"));
    if ($cek) {
        $error = 'Username sudah dipakai! Gunakan username lain.';
    } else {
        mysqli_query($koneksi, "INSERT INTO aparatur (nama, jabatan) VALUES ('$nama', '$jabatan')");
        $id_aparatur = mysqli_insert_id($koneksi);
        mysqli_query($koneksi, "INSERT INTO user (nama, username, password, role, id_aparatur) VALUES ('$nama', '$username', '$password', 'aparatur', $id_aparatur)");
        header("Location: aparatur.php?pesan=tambah"); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Aparatur</title>
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
      <a href="aparatur.php" class="active">&#9632; Data Aparatur</a>
      <a href="kriteria.php">&#9632; Data Kriteria</a>
      <a href="kehadiran.php">&#9632; Ketidakhadiran</a>
      <a href="penilaian.php">&#9632; Rekap Penilaian</a>
      <a href="perhitungan_saw.php">&#9632; Perhitungan SAW</a>
      <a href="kelola_user.php">&#9632; Kelola User</a>
      <div class="nav-section">Akun</div>
      <a href="../logout.php" style="color:rgba(255,100,100,0.7);">&#9632; Logout</a>
    </nav>
    <div class="sidebar-footer">Login sebagai: <strong style="color:var(--green-light);"><?php echo htmlspecialchars($_SESSION['nama']); ?></strong></div>
  </aside>
  <div class="main">
    <header class="topbar">
      <div class="topbar-title">Tambah Aparatur</div>
      <div class="topbar-right">
        <span class="topbar-user"><?php echo htmlspecialchars($_SESSION['nama']); ?></span>
        <div class="avatar"><?php echo strtoupper(substr($_SESSION['nama'], 0, 1)); ?></div>
      </div>
    </header>
    <main class="content">
      <div class="breadcrumb">
        <a href="index.php">Beranda</a><span class="breadcrumb-sep">/</span>
        <a href="aparatur.php">Data Aparatur</a><span class="breadcrumb-sep">/</span>
        <span class="breadcrumb-active">Tambah</span>
      </div>
      <div class="page-header">
        <div>
          <div class="page-title">Tambah Aparatur</div>
          <div class="page-sub">Data aparatur + akun login akan dibuat sekaligus</div>
        </div>
      </div>
      <div class="card" style="max-width:520px;">
        <div class="card-head"><div class="card-head-title">Form Data Aparatur</div></div>
        <div class="card-body">
          <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
          <form method="POST">
            <div class="form-group">
              <label class="form-label">Nama Aparatur</label>
              <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap" required
                     value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>">
            </div>
            <div class="form-group">
              <label class="form-label">Jabatan</label>
              <input type="text" name="jabatan" class="form-control" placeholder="Contoh: Sekdes, Kasi, Kaur..." required
                     value="<?php echo isset($_POST['jabatan']) ? htmlspecialchars($_POST['jabatan']) : ''; ?>">
            </div>
            <div style="border-top:1px solid #f0f0f0; padding-top:16px; margin-top:4px;">
              <div style="font-size:12px; font-weight:600; color:var(--gray-500); margin-bottom:12px;">AKUN LOGIN APARATUR</div>
              <div class="form-group">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Buat username untuk aparatur" required
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
              </div>
              <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Buat password" required>
              </div>
            </div>
            <div class="form-actions">
              <button type="submit" class="btn btn-primary">Simpan Data</button>
              <a href="aparatur.php" class="btn btn-ghost">Batal</a>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</div>
</body>
</html>