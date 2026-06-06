<?php
require_once '../includes/auth_kepala_desa.php';
require_once '../includes/koneksi.php';

$active    = 'dashboard';
$menunggu  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM jobdesk WHERE status='selesai'"))['total'];
$disetujui = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM jobdesk WHERE status='disetujui'"))['total'];
$ditolak   = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM jobdesk WHERE status='perlu_revisi'"))['total'];
$aparatur  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM aparatur"))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Kepala Desa</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="layout">
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-brand-name">Portal Kepala Desa</div>
      <div class="sidebar-brand-sub">SPK Desa Tatah Mesjid</div>
    </div>
    <nav class="sidebar-nav">
      <div class="nav-section">Menu</div>
      <a href="dashboard.php"  class="<?php echo $active=='dashboard'  ? 'active' : ''; ?>">&#9632; Beranda</a>
      <a href="verifikasi.php" class="<?php echo $active=='verifikasi' ? 'active' : ''; ?>">&#9632; Verifikasi Jobdesk</a>
      <a href="ranking.php"    class="<?php echo $active=='ranking'    ? 'active' : ''; ?>">&#9632; Hasil Ranking SAW</a>
      <div class="nav-section">Akun</div>
      <a href="logout.php" style="color:rgba(255,100,100,0.7);">&#9632; Logout</a>
    </nav>
    <div class="sidebar-footer">Login sebagai: <strong style="color:var(--green-light);"><?php echo htmlspecialchars($_SESSION['kepala_desa_nama']); ?></strong></div>
  </aside>
  <div class="main">
    <header class="topbar">
      <div class="topbar-title">Beranda</div>
      <div class="topbar-right">
        <span class="topbar-user"><?php echo htmlspecialchars($_SESSION['kepala_desa_nama']); ?></span>
        <div class="avatar"><?php echo strtoupper(substr($_SESSION['kepala_desa_nama'], 0, 1)); ?></div>
      </div>
    </header>
    <main class="content">
      <div class="page-header">
        <div>
          <div class="page-title">Selamat Datang, <?php echo htmlspecialchars($_SESSION['kepala_desa_nama']); ?>!</div>
          <div class="page-sub">Panel Kepala Desa &mdash; SPK Penghargaan Aparatur Desa Tatah Mesjid</div>
        </div>
      </div>
      <div class="metric-row">
        <div class="metric-card green">
          <div class="metric-label">Menunggu Verifikasi</div>
          <div class="metric-value"><?php echo $menunggu; ?></div>
          <div class="metric-desc">Jobdesk perlu di-approve</div>
        </div>
        <div class="metric-card">
          <div class="metric-label">Disetujui</div>
          <div class="metric-value"><?php echo $disetujui; ?></div>
          <div class="metric-desc">Jobdesk sudah disetujui</div>
        </div>
        <div class="metric-card">
          <div class="metric-label">Perlu Revisi</div>
          <div class="metric-value"><?php echo $ditolak; ?></div>
          <div class="metric-desc">Jobdesk perlu diperbaiki</div>
        </div>
        <div class="metric-card">
          <div class="metric-label">Total Aparatur</div>
          <div class="metric-value"><?php echo $aparatur; ?></div>
          <div class="metric-desc">Aparatur aktif desa</div>
        </div>
      </div>
      <div class="page-sub mb-2" style="font-weight:600; color:var(--gray-700);">Akses Cepat</div>
      <div class="menu-grid">
        <a href="verifikasi.php" class="menu-card">
          <div class="menu-card-icon">&#10003;</div>
          <div class="menu-card-label">Verifikasi Jobdesk</div>
          <div class="menu-card-desc">Approve atau tolak jobdesk aparatur</div>
        </a>
        <a href="ranking.php" class="menu-card">
          <div class="menu-card-icon">&#127942;</div>
          <div class="menu-card-label">Hasil Ranking SAW</div>
          <div class="menu-card-desc">Lihat ranking aparatur terbaik</div>
        </a>
      </div>
    </main>
  </div>
</div>
</body>
</html>
