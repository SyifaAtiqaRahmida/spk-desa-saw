<?php
// Ganti ini sesuai halaman aktif
$active = 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SPK Desa Tatah Mesjid</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="layout">

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-brand-name">SPK Desa Tatah Mesjid</div>
      <div class="sidebar-brand-sub">Sistem Pendukung Keputusan</div>
    </div>

    <nav class="sidebar-nav">
      <div class="nav-section">Menu Utama</div>
      <a href="index.php"           class="<?= $active=='dashboard' ? 'active' : '' ?>">&#9632; Dashboard</a>
      <a href="aparatur.php"        class="<?= $active=='aparatur'  ? 'active' : '' ?>">&#9632; Data Aparatur</a>
      <a href="kriteria.php"        class="<?= $active=='kriteria'  ? 'active' : '' ?>">&#9632; Data Kriteria</a>
      <a href="penilaian.php"       class="<?= $active=='penilaian' ? 'active' : '' ?>">&#9632; Penilaian</a>
      <a href="perhitungan_saw.php" class="<?= $active=='saw'       ? 'active' : '' ?>">&#9632; Perhitungan SAW</a>
    </nav>

    <div class="sidebar-footer">
      Metode SAW &bull; 2025
    </div>
  </aside>

  <!-- MAIN -->
  <div class="main">

    <!-- TOPBAR -->
    <header class="topbar">
      <div class="topbar-title">Dashboard</div>
      <div class="topbar-right">
        <span class="topbar-user">Admin</span>
        <div class="avatar">A</div>
      </div>
    </header>

    <!-- CONTENT -->
    <main class="content">

      <!-- Breadcrumb -->
      <div class="breadcrumb">
        <a href="index.php">Beranda</a>
        <span class="breadcrumb-sep">/</span>
        <span class="breadcrumb-active">Dashboard</span>
      </div>

      <!-- Page Header -->
      <div class="page-header">
        <div>
          <div class="page-title">Selamat Datang</div>
          <div class="page-sub">Sistem Pendukung Keputusan Penghargaan Aparatur Desa Tatah Mesjid &mdash; Metode SAW</div>
        </div>
      </div>

      <!-- Metric Cards -->
      <?php
        // Contoh query — sesuaikan dengan tabel database kamu
        // require_once 'koneksi.php';
        // $jml_aparatur = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM aparatur"));
        // $jml_kriteria = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM kriteria"));
        $jml_aparatur = 0;
        $jml_kriteria = 0;
      ?>
      <div class="metric-row">
        <div class="metric-card green">
          <div class="metric-label">Total Aparatur</div>
          <div class="metric-value"><?= $jml_aparatur ?></div>
          <div class="metric-desc">Kandidat penerima penghargaan</div>
        </div>
        <div class="metric-card">
          <div class="metric-label">Kriteria Penilaian</div>
          <div class="metric-value"><?= $jml_kriteria ?></div>
          <div class="metric-desc">Bobot terbobot metode SAW</div>
        </div>
        <div class="metric-card">
          <div class="metric-label">Periode</div>
          <div class="metric-value">2025</div>
          <div class="metric-desc">Tahun penilaian aktif</div>
        </div>
      </div>

      <!-- Menu Cards -->
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

      </div>

    </main><!-- /content -->
  </div><!-- /main -->
</div><!-- /layout -->

</body>
</html>