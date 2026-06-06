<?php
require_once '../includes/auth_aparatur.php';
require_once '../includes/koneksi.php';

$active      = 'tugas';
$id_aparatur = $_SESSION['aparatur'];

// Ambil semua kriteria beserta jumlah jobdesk
$kriteria_list = mysqli_query($koneksi, "SELECT * FROM kriteria ORDER BY id_kriteria");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tugas Saya</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="layout">
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-brand-name">Portal Aparatur</div>
      <div class="sidebar-brand-sub">SPK Desa Tatah Mesjid</div>
    </div>
    <nav class="sidebar-nav">
      <div class="nav-section">Menu</div>
      <a href="dashboard.php"      class="<?php echo $active=='dashboard' ? 'active' : ''; ?>">&#9632; Beranda</a>
      <a href="tugas.php"          class="<?php echo $active=='tugas'     ? 'active' : ''; ?>">&#9632; Tugas Saya</a>
      <a href="hasil_evaluasi.php" class="<?php echo $active=='evaluasi'  ? 'active' : ''; ?>">&#9632; Hasil Evaluasi</a>
      <div class="nav-section">Akun</div>
      <a href="logout.php" style="color:rgba(255,100,100,0.7);">&#9632; Logout</a>
    </nav>
    <div class="sidebar-footer">Login sebagai: <strong style="color:var(--green-light);"><?php echo htmlspecialchars($_SESSION['aparatur_nama']); ?></strong></div>
  </aside>
  <div class="main">
    <header class="topbar">
      <div class="topbar-title">Tugas Saya</div>
      <div class="topbar-right">
        <span class="topbar-user"><?php echo htmlspecialchars($_SESSION['aparatur_nama']); ?></span>
        <div class="avatar"><?php echo strtoupper(substr($_SESSION['aparatur_nama'], 0, 1)); ?></div>
      </div>
    </header>
    <main class="content">
      <div class="breadcrumb">
        <a href="dashboard.php">Beranda</a>
        <span class="breadcrumb-sep">/</span>
        <span class="breadcrumb-active">Tugas Saya</span>
      </div>
      <div class="page-header">
        <div>
          <div class="page-title">Tugas Saya</div>
          <div class="page-sub">Pilih kriteria untuk melihat & menambahkan jobdesk kamu</div>
        </div>
      </div>

      <div class="alert alert-info">
        &#9432; Tambahkan jobdesk yang kamu kerjakan pada setiap kriteria. Setelah selesai, konfirmasi dengan upload bukti.
      </div>

      <!-- Daftar Kriteria -->
      <div class="card">
        <div class="card-head"><div class="card-head-title">Daftar Kriteria Penilaian</div></div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th style="width:50px">No</th>
                <th>Nama Kriteria</th>
                <th style="width:120px">Bobot</th>
                <th style="width:100px">Atribut</th>
                <th style="width:100px; text-align:center">Jobdesk</th>
                <th style="width:120px; text-align:center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php $no = 1; while ($k = mysqli_fetch_assoc($kriteria_list)): ?>
                <?php
                  $jml = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM jobdesk WHERE id_aparatur=$id_aparatur AND id_kriteria={$k['id_kriteria']}"))['total'];
                  $selesai = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM jobdesk WHERE id_aparatur=$id_aparatur AND id_kriteria={$k['id_kriteria']} AND status IN ('selesai','disetujui')"))['total'];
                ?>
              <tr>
                <td class="text-muted text-center"><?php echo $no++; ?></td>
                <td style="font-weight:500"><?php echo htmlspecialchars($k['nama_kriteria']); ?></td>
                <td>
                  <div class="weight-bar-wrap">
                    <div class="weight-track"><div class="weight-fill" style="width:<?php echo $k['bobot']*100; ?>%"></div></div>
                    <span style="font-size:13px; font-weight:600;"><?php echo $k['bobot']; ?></span>
                  </div>
                </td>
                <td>
                  <?php if (strtolower($k['atribut']) == 'benefit'): ?>
                    <span class="badge badge-green">Benefit</span>
                  <?php else: ?>
                    <span class="badge badge-red">Cost</span>
                  <?php endif; ?>
                </td>
                <td class="text-center">
                  <span class="badge badge-green"><?php echo $selesai; ?>/<?php echo $jml; ?></span>
                </td>
                <td class="text-center">
                  <a href="detail_tugas.php?id_kriteria=<?php echo $k['id_kriteria']; ?>" class="btn btn-outline btn-sm">Lihat</a>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>
</div>
</body>
</html>