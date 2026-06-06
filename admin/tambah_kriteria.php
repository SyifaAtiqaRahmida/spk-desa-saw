<?php
require_once '../includes/auth.php';
require_once '../includes/koneksi.php';
$active = 'kriteria';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_kriteria = mysqli_real_escape_string($koneksi, $_POST['nama_kriteria']);
    $bobot         = mysqli_real_escape_string($koneksi, $_POST['bobot']);
    $atribut       = mysqli_real_escape_string($koneksi, $_POST['atribut']);
    mysqli_query($koneksi, "INSERT INTO kriteria (nama_kriteria, bobot, atribut) VALUES ('$nama_kriteria', '$bobot', '$atribut')");
    header("Location: kriteria.php?pesan=tambah"); exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tambah Kriteria</title>
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
      <a href="kriteria.php" class="active">&#9632; Data Kriteria</a>
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
      <div class="topbar-title">Tambah Kriteria</div>
      <div class="topbar-right">
        <span class="topbar-user"><?php echo htmlspecialchars($_SESSION['nama']); ?></span>
        <div class="avatar"><?php echo strtoupper(substr($_SESSION['nama'], 0, 1)); ?></div>
      </div>
    </header>
    <main class="content">
      <div class="breadcrumb">
        <a href="index.php">Beranda</a><span class="breadcrumb-sep">/</span>
        <a href="kriteria.php">Data Kriteria</a><span class="breadcrumb-sep">/</span>
        <span class="breadcrumb-active">Tambah</span>
      </div>
      <div class="page-header">
        <div><div class="page-title">Tambah Kriteria</div><div class="page-sub">Tambahkan kriteria baru untuk penilaian SAW</div></div>
      </div>
      <div class="card" style="max-width:520px;">
        <div class="card-head"><div class="card-head-title">Form Kriteria</div></div>
        <div class="card-body">
          <form method="POST">
            <div class="form-group">
              <label class="form-label">Nama Kriteria</label>
              <input type="text" name="nama_kriteria" class="form-control" placeholder="Contoh: Kualitas Kerja, Kerja Sama..." required>
            </div>
            <div class="form-group">
              <label class="form-label">Bobot (0 - 1)</label>
              <input type="number" name="bobot" class="form-control" placeholder="Contoh: 0.25" step="0.01" min="0" max="1" required>
              <div class="form-hint">Total bobot semua kriteria harus = 1</div>
            </div>
            <div class="form-group">
              <label class="form-label">Atribut</label>
              <select name="atribut" class="form-control" required>
                <option value="">-- Pilih Atribut --</option>
                <option value="benefit">Benefit (semakin tinggi semakin bagus)</option>
                <option value="cost">Cost (semakin rendah semakin bagus)</option>
              </select>
            </div>
            <div class="form-actions">
              <button type="submit" class="btn btn-primary">Simpan Kriteria</button>
              <a href="kriteria.php" class="btn btn-ghost">Batal</a>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</div>
</body>
</html>