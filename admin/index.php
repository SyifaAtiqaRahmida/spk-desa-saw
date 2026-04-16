<?php
require_once '../includes/auth.php';
require_once '../includes/koneksi.php';

$active = 'dashboard';

$jml_aparatur = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM aparatur"));
$jml_kriteria = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM kriteria"));
$jml_penilaian = mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM penilaian"));
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard — SPK Desa Tatah Mesjid</title>
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
      <a href="index.php"           class="<?php echo $active=='dashboard' ? 'active' : ''; ?>">&#9632; Dashboard</a>
      <a href="aparatur.php"        class="<?php echo $active=='aparatur'  ? 'active' : ''; ?>">&#9632; Data Aparatur</a>
      <a href="kriteria.php"        class="<?php echo $active=='kriteria'  ? 'active' : ''; ?>">&#9632; Data Kriteria</a>
      <a href="kehadiran.php"       class="<?php echo $active=='kehadiran' ? 'active' : ''; ?>">&#9632; Kehadiran</a>
      <a href="penilaian.php"       class="<?php echo $active=='penilaian' ? 'active' : ''; ?>">&#9632; Penilaian</a>
      <a href="perhitungan_saw.php" class="<?php echo $active=='saw'       ? 'active' : ''; ?>">&#9632; Perhitungan SAW</a>
      <a href="kelola_user.php"     class="<?php echo $active=='user'      ? 'active' : ''; ?>">&#9632; Kelola User</a>
      <div class="nav-section">Akun</div>
      <a href="../logout.php" style="color:rgba(255,100,100,0.7);">&#9632; Logout</a>
    </nav>
    <div class="sidebar-footer">Login sebagai: <strong style="color:var(--green-light);"><?php echo htmlspecialchars($_SESSION['nama']); ?></strong></div>
  </aside>
  <div class="main">
    <header class="topbar">
      <div class="topbar-title">Dashboard</div>
      <div class="topbar-right">
        <span class="topbar-user"><?php echo htmlspecialchars($_SESSION['nama']); ?></span>
        <div class="avatar"><?php echo strtoupper(substr($_SESSION['nama'], 0, 1)); ?></div>
      </div>
    </header>
    <main class="content">
      <div class="breadcrumb">
        <a href="index.php">Beranda</a>
        <span class="breadcrumb-sep">/</span>
        <span class="breadcrumb-active">Dashboard</span>
      </div>
      <div class="page-header">
        <div>
          <div class="page-title">Selamat Datang, <?php echo htmlspecialchars($_SESSION['nama']); ?>!</div>
          <div class="page-sub">Sistem Pendukung Keputusan Penghargaan Aparatur Desa Tatah Mesjid &mdash; Metode SAW</div>
        </div>
      </div>
      <div class="metric-row">
        <div class="metric-card green">
          <div class="metric-label">Total Aparatur</div>
          <div class="metric-value"><?php echo $jml_aparatur; ?></div>
          <div class="metric-desc">Kandidat penerima penghargaan</div>
        </div>
        <div class="metric-card">
          <div class="metric-label">Kriteria Penilaian</div>
          <div class="metric-value"><?php echo $jml_kriteria; ?></div>
          <div class="metric-desc">Bobot terbobot metode SAW</div>
        </div>
        <div class="metric-card">
          <div class="metric-label">Data Penilaian</div>
          <div class="metric-value"><?php echo $jml_penilaian; ?></div>
          <div class="metric-desc">Total nilai yang sudah diinput</div>
        </div>
      </div>
      <div class="page-sub mb-2" style="font-weight:600; color:var(--gray-700);">Akses Cepat</div>
      <div class="menu-grid">
        <a href="aparatur.php" class="menu-card">
          <div class="menu-card-icon">&#128100;</div>
          <div class="menu-card-label">Data Aparatur</div>
          <div class="menu-card-desc">Kelola data aparatur desa yang akan dinilai</div>
        </a>
        <a href="kriteria.php" class="menu-card">
          <div class="menu-card-icon">&#9878;</div>
          <div class="menu-card-label">Data Kriteria</div>
          <div class="menu-card-desc">Atur kriteria dan bobot penilaian SAW</div>
        </a>
        <a href="kehadiran.php" class="menu-card">
          <div class="menu-card-icon">&#128197;</div>
          <div class="menu-card-label">Kehadiran</div>
          <div class="menu-card-desc">Input ketidakhadiran aparatur desa</div>
        </a>
        <a href="penilaian.php" class="menu-card">
          <div class="menu-card-icon">&#128203;</div>
          <div class="menu-card-label">Penilaian</div>
          <div class="menu-card-desc">Input nilai tiap aparatur per kriteria</div>
        </a>
        <a href="perhitungan_saw.php" class="menu-card">
          <div class="menu-card-icon">&#127942;</div>
          <div class="menu-card-label">Perhitungan SAW</div>
          <div class="menu-card-desc">Lihat hasil ranking dan penerima penghargaan</div>
        </a>
        <a href="kelola_user.php" class="menu-card">
          <div class="menu-card-icon">&#128272;</div>
          <div class="menu-card-label">Kelola User</div>
          <div class="menu-card-desc">Kelola akun yang bisa mengakses sistem</div>
        </a>
      </div>
    </main>
  </div>
</div>
</body>
</html>