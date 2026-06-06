<?php
require_once '../includes/auth.php';
require_once '../includes/koneksi.php';

$active = 'aparatur';
$id     = (int) $_GET['id'];
$data   = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM aparatur WHERE id_aparatur=$id"));
$error  = '';
$sukses = '';

if (!$data) { header("Location: aparatur.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Cek username tidak dobel
    $cek = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM aparatur WHERE username='$username' AND id_aparatur != $id"));
    if ($cek) {
        $error = 'Username sudah dipakai aparatur lain!';
    } else {
        mysqli_query($koneksi, "UPDATE aparatur SET username='$username', password='$password' WHERE id_aparatur=$id");
        $sukses = 'Username & password berhasil diset!';
        $data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM aparatur WHERE id_aparatur=$id"));
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Set Password Aparatur</title>
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
      <a href="kehadiran.php">&#9632; Kehadiran</a>
      <a href="penilaian.php">&#9632; Penilaian</a>
      <a href="perhitungan_saw.php">&#9632; Perhitungan SAW</a>
      <a href="kelola_user.php">&#9632; Kelola User</a>
      <div class="nav-section">Akun</div>
      <a href="../logout.php" style="color:rgba(255,100,100,0.7);">&#9632; Logout</a>
    </nav>
    <div class="sidebar-footer">Login sebagai: <strong style="color:var(--green-light);"><?php echo htmlspecialchars($_SESSION['nama']); ?></strong></div>
  </aside>
  <div class="main">
    <header class="topbar">
      <div class="topbar-title">Set Akses Aparatur</div>
      <div class="topbar-right">
        <span class="topbar-user"><?php echo htmlspecialchars($_SESSION['nama']); ?></span>
        <div class="avatar"><?php echo strtoupper(substr($_SESSION['nama'], 0, 1)); ?></div>
      </div>
    </header>
    <main class="content">
      <div class="breadcrumb">
        <a href="index.php">Beranda</a>
        <span class="breadcrumb-sep">/</span>
        <a href="aparatur.php">Data Aparatur</a>
        <span class="breadcrumb-sep">/</span>
        <span class="breadcrumb-active">Set Password</span>
      </div>
      <div class="page-header">
        <div>
          <div class="page-title">Set Username & Password</div>
          <div class="page-sub">Atur akses login untuk: <strong><?php echo htmlspecialchars($data['nama']); ?></strong> — <?php echo htmlspecialchars($data['jabatan']); ?></div>
        </div>
      </div>

      <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
      <?php if ($sukses): ?><div class="alert alert-success"><?php echo $sukses; ?></div><?php endif; ?>

      <?php if ($data['username']): ?>
        <div class="alert alert-info">&#9432; Aparatur ini sudah punya username: <strong><?php echo htmlspecialchars($data['username']); ?></strong>. Isi form ini untuk menggantinya.</div>
      <?php endif; ?>

      <div class="card" style="max-width:520px;">
        <div class="card-head"><div class="card-head-title">Form Set Akses Login</div></div>
        <div class="card-body">
          <form method="POST">
            <div class="form-group">
              <label class="form-label">Username</label>
              <input type="text" name="username" class="form-control"
                     value="<?php echo htmlspecialchars($data['username'] ?? ''); ?>"
                     placeholder="Buat username untuk aparatur ini" required>
            </div>
            <div class="form-group">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" placeholder="Buat password" required>
              <div class="form-hint">Password akan dienkripsi secara otomatis</div>
            </div>
            <div class="form-actions">
              <button type="submit" class="btn btn-primary">Simpan Akses</button>
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