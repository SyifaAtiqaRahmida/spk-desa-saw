<?php
require_once '../includes/auth.php';
require_once '../includes/koneksi.php';
$active = 'aparatur';
$id     = (int) $_GET['id'];
$data   = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT a.*, u.username, u.id_user FROM aparatur a LEFT JOIN user u ON a.id_aparatur = u.id_aparatur AND u.role='aparatur' WHERE a.id_aparatur=$id"));
$error  = '';
$sukses = '';

if (!$data) { header("Location: aparatur.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    $cek = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username' AND id_aparatur != $id"));
    if ($cek) {
        $error = 'Username sudah dipakai aparatur lain!';
    } else {
        if ($data['id_user']) {
            // Update akun yang sudah ada
            if (!empty($password)) {
                $pw = password_hash($password, PASSWORD_DEFAULT);
                mysqli_query($koneksi, "UPDATE user SET username='$username', password='$pw' WHERE id_user={$data['id_user']}");
            } else {
                mysqli_query($koneksi, "UPDATE user SET username='$username' WHERE id_user={$data['id_user']}");
            }
        } else {
            // Buat akun baru
            $pw = password_hash($password, PASSWORD_DEFAULT);
            $nama = mysqli_real_escape_string($koneksi, $data['nama']);
            mysqli_query($koneksi, "INSERT INTO user (nama, username, password, role, id_aparatur) VALUES ('$nama', '$username', '$pw', 'aparatur', $id)");
        }
        $sukses = 'Akses login berhasil diperbarui!';
        $data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT a.*, u.username, u.id_user FROM aparatur a LEFT JOIN user u ON a.id_aparatur = u.id_aparatur AND u.role='aparatur' WHERE a.id_aparatur=$id"));
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Set Akses Aparatur</title>
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
        <a href="index.php">Beranda</a><span class="breadcrumb-sep">/</span>
        <a href="aparatur.php">Data Aparatur</a><span class="breadcrumb-sep">/</span>
        <span class="breadcrumb-active">Set Akses</span>
      </div>
      <div class="page-header">
        <div>
          <div class="page-title">Set Akses Login</div>
          <div class="page-sub">Aparatur: <strong><?php echo htmlspecialchars($data['nama']); ?></strong> — <?php echo htmlspecialchars($data['jabatan']); ?></div>
        </div>
      </div>
      <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
      <?php if ($sukses): ?><div class="alert alert-success"><?php echo $sukses; ?></div><?php endif; ?>
      <?php if ($data['username']): ?>
        <div class="alert alert-info">&#9432; Akun aktif dengan username: <strong><?php echo htmlspecialchars($data['username']); ?></strong>. Kosongkan password jika tidak ingin menggantinya.</div>
      <?php endif; ?>
      <div class="card" style="max-width:520px;">
        <div class="card-head"><div class="card-head-title">Form Akses Login</div></div>
        <div class="card-body">
          <form method="POST">
            <div class="form-group">
              <label class="form-label">Username</label>
              <input type="text" name="username" class="form-control" placeholder="Buat username" required
                     value="<?php echo htmlspecialchars($data['username'] ?? ''); ?>">
            </div>
            <div class="form-group">
              <label class="form-label">Password <?php echo $data['username'] ? '<span style="color:var(--gray-300); font-weight:400;">(opsional)</span>' : ''; ?></label>
              <input type="password" name="password" class="form-control" placeholder="<?php echo $data['username'] ? 'Kosongkan jika tidak ingin ganti' : 'Buat password'; ?>"
                     <?php echo !$data['username'] ? 'required' : ''; ?>>
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
