<?php
require_once '../includes/auth.php';
require_once '../includes/koneksi.php';
$active = 'aparatur';
$query  = mysqli_query($koneksi, "SELECT * FROM aparatur");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Data Aparatur</title>
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
      <div class="topbar-title">Data Aparatur</div>
      <div class="topbar-right">
        <span class="topbar-user"><?php echo htmlspecialchars($_SESSION['nama']); ?></span>
        <div class="avatar"><?php echo strtoupper(substr($_SESSION['nama'], 0, 1)); ?></div>
      </div>
    </header>
    <main class="content">
      <div class="breadcrumb">
        <a href="index.php">Beranda</a>
        <span class="breadcrumb-sep">/</span>
        <span class="breadcrumb-active">Data Aparatur</span>
      </div>
      <div class="page-header">
        <div>
          <div class="page-title">Data Aparatur Desa Tatah Mesjid</div>
          <div class="page-sub">Kelola data aparatur yang akan dinilai dalam sistem SAW</div>
        </div>
        <a href="tambah_aparatur.php" class="btn btn-primary">+ Tambah Aparatur</a>
      </div>
      <?php if (isset($_GET['pesan'])): ?>
        <?php if ($_GET['pesan'] == 'tambah'): ?><div class="alert alert-success">Data aparatur berhasil ditambahkan.</div>
        <?php elseif ($_GET['pesan'] == 'edit'): ?><div class="alert alert-success">Data aparatur berhasil diperbarui.</div>
        <?php elseif ($_GET['pesan'] == 'hapus'): ?><div class="alert alert-danger">Data aparatur berhasil dihapus.</div>
        <?php endif; ?>
      <?php endif; ?>
      <div class="card">
        <div class="card-head">
          <div class="card-head-title">Daftar Aparatur</div>
          <span class="badge badge-green"><?php echo mysqli_num_rows($query); ?> Data</span>
        </div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th style="width:50px">No</th>
                <th>Nama</th>
                <th>Jabatan</th>
                <th style="width:160px; text-align:center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (mysqli_num_rows($query) == 0): ?>
                <tr><td colspan="4"><div class="empty-state"><div class="empty-state-icon">&#128100;</div><div class="empty-state-title">Belum ada data aparatur</div></div></td></tr>
              <?php else: ?>
                <?php $no = 1; while ($row = mysqli_fetch_assoc($query)): ?>
                <tr>
                  <td class="text-muted text-center"><?php echo $no++; ?></td>
                  <td style="font-weight:500"><?php echo htmlspecialchars($row['nama']); ?></td>
                  <td><span class="badge badge-green"><?php echo htmlspecialchars($row['jabatan']); ?></span></td>
                  <td>
                    <div class="d-flex gap-1 justify-center">
                      <a href="edit_aparatur.php?id=<?php echo $row['id_aparatur']; ?>" class="btn btn-outline btn-sm">Edit</a>
                      <a href="hapus_aparatur.php?id=<?php echo $row['id_aparatur']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
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