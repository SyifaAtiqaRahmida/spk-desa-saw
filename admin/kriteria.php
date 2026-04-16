<?php
require_once '../includes/auth.php';
require_once '../includes/koneksi.php';
$active = 'kriteria';
$query  = mysqli_query($koneksi, "SELECT * FROM kriteria");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Kriteria</title>
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
      <div class="topbar-title">Data Kriteria</div>
      <div class="topbar-right">
        <span class="topbar-user"><?php echo htmlspecialchars($_SESSION['nama']); ?></span>
        <div class="avatar"><?php echo strtoupper(substr($_SESSION['nama'], 0, 1)); ?></div>
      </div>
    </header>
    <main class="content">
      <div class="breadcrumb">
        <a href="index.php">Beranda</a><span class="breadcrumb-sep">/</span>
        <span class="breadcrumb-active">Data Kriteria</span>
      </div>
      <div class="page-header">
        <div><div class="page-title">Data Kriteria Penilaian</div>
        <div class="page-sub">Kelola kriteria dan bobot penilaian untuk metode SAW</div></div>
        <a href="tambah_kriteria.php" class="btn btn-primary">+ Tambah Kriteria</a>
      </div>
      <div class="alert alert-info">
        <div>&#9432; <strong>Benefit</strong> = semakin tinggi nilainya semakin bagus &nbsp;|&nbsp; <strong>Cost</strong> = semakin rendah nilainya semakin bagus</div>
      </div>
      <?php if (isset($_GET['pesan'])): ?>
        <?php if ($_GET['pesan'] == 'tambah'): ?><div class="alert alert-success">Data kriteria berhasil ditambahkan.</div>
        <?php elseif ($_GET['pesan'] == 'edit'): ?><div class="alert alert-success">Data kriteria berhasil diperbarui.</div>
        <?php elseif ($_GET['pesan'] == 'hapus'): ?><div class="alert alert-danger">Data kriteria berhasil dihapus.</div>
        <?php endif; ?>
      <?php endif; ?>
      <div class="card">
        <div class="card-head">
          <div class="card-head-title">Daftar Kriteria</div>
          <span class="badge badge-green"><?php echo mysqli_num_rows($query); ?> Kriteria</span>
        </div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th style="width:50px">No</th>
                <th>Nama Kriteria</th>
                <th style="width:140px">Bobot</th>
                <th style="width:110px">Atribut</th>
                <th style="width:160px; text-align:center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (mysqli_num_rows($query) == 0): ?>
                <tr><td colspan="5"><div class="empty-state"><div class="empty-state-icon">&#9878;</div><div class="empty-state-title">Belum ada data kriteria</div></div></td></tr>
              <?php else: ?>
                <?php $no = 1; while ($row = mysqli_fetch_assoc($query)): ?>
                <tr>
                  <td class="text-muted text-center"><?php echo $no++; ?></td>
                  <td style="font-weight:500"><?php echo htmlspecialchars($row['nama_kriteria']); ?></td>
                  <td>
                    <div class="weight-bar-wrap">
                      <div class="weight-track"><div class="weight-fill" style="width:<?php echo ($row['bobot'] * 100); ?>%"></div></div>
                      <span style="font-size:13px; font-weight:600; min-width:32px;"><?php echo $row['bobot']; ?></span>
                    </div>
                  </td>
                  <td>
                    <?php if (strtolower($row['atribut']) == 'benefit'): ?>
                      <span class="badge badge-green">Benefit</span>
                    <?php else: ?>
                      <span class="badge badge-red">Cost</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <div class="d-flex gap-1 justify-center">
                      <a href="edit_kriteria.php?id=<?php echo $row['id_kriteria']; ?>" class="btn btn-outline btn-sm">Edit</a>
                      <a href="hapus_kriteria.php?id=<?php echo $row['id_kriteria']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus kriteria ini?')">Hapus</a>
                    </div>
                  </td>
                </tr>
                <?php endwhile; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>
</div>
</body>
</html>