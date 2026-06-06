<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cek session aparatur khusus
if (!isset($_SESSION['aparatur'])) {
    header("Location: login.php");
    exit;
}

require_once '../includes/koneksi.php';

$active      = 'dashboard';
$id_aparatur = $_SESSION['aparatur'];

$tugas_aktif   = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM jobdesk WHERE id_aparatur=$id_aparatur AND status='belum_selesai'"))['total'];
$total_jobdesk = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM jobdesk WHERE id_aparatur=$id_aparatur"))['total'];

// Hitung nilai SAW
$total_saw     = 0;
$kriteria_list = mysqli_query($koneksi, "SELECT * FROM kriteria");
while ($k = mysqli_fetch_assoc($kriteria_list)) {
    $n          = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nilai FROM penilaian WHERE id_aparatur=$id_aparatur AND id_kriteria={$k['id_kriteria']}"));
    $nilai_asli = $n['nilai'] ?? 0;
    $max        = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT MAX(nilai) as max FROM penilaian WHERE id_kriteria={$k['id_kriteria']}"));
    $min        = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT MIN(nilai) as min FROM penilaian WHERE id_kriteria={$k['id_kriteria']}"));
    if ($k['atribut'] == 'benefit') {
        $norm = $nilai_asli / ($max['max'] ?: 1);
    } else {
        $norm = ($min['min'] ?: 1) / ($nilai_asli ?: 1);
    }
    $total_saw += $norm * $k['bobot'];
}
$nilai_terakhir = $total_saw > 0 ? round($total_saw, 3) : '-';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Aparatur</title>
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
      <div class="topbar-title">Beranda</div>
      <div class="topbar-right">
        <span class="topbar-user"><?php echo htmlspecialchars($_SESSION['aparatur_nama']); ?></span>
        <div class="avatar"><?php echo strtoupper(substr($_SESSION['aparatur_nama'], 0, 1)); ?></div>
      </div>
    </header>
    <main class="content">
      <div class="page-header">
        <div>
          <div class="page-title">Selamat Datang, <?php echo htmlspecialchars($_SESSION['aparatur_nama']); ?>!</div>
          <div class="page-sub"><?php echo htmlspecialchars($_SESSION['aparatur_jabatan']); ?> &mdash; Desa Tatah Mesjid</div>
        </div>
      </div>
      <div class="metric-row">
        <div class="metric-card green">
          <div class="metric-label">Nilai SAW Terakhir</div>
          <div class="metric-value"><?php echo $nilai_terakhir; ?></div>
          <div class="metric-desc">Berdasarkan data penilaian</div>
        </div>
        <div class="metric-card">
          <div class="metric-label">Tugas Belum Selesai</div>
          <div class="metric-value"><?php echo $tugas_aktif; ?></div>
          <div class="metric-desc">Segera selesaikan & konfirmasi</div>
        </div>
        <div class="metric-card">
          <div class="metric-label">Total Jobdesk</div>
          <div class="metric-value"><?php echo $total_jobdesk; ?></div>
          <div class="metric-desc">Semua tugas yang diinput</div>
        </div>
      </div>
      <div class="page-sub mb-2" style="font-weight:600; color:var(--gray-700);">Akses Cepat</div>
      <div class="menu-grid">
        <a href="tugas.php" class="menu-card">
          <div class="menu-card-icon">&#128203;</div>
          <div class="menu-card-label">Tugas Saya</div>
          <div class="menu-card-desc">Lihat & tambah jobdesk per kriteria penilaian</div>
        </a>
        <a href="hasil_evaluasi.php" class="menu-card">
          <div class="menu-card-icon">&#127942;</div>
          <div class="menu-card-label">Hasil Evaluasi</div>
          <div class="menu-card-desc">Lihat hasil penilaian SAW kamu</div>
        </a>
      </div>
    </main>
  </div>
</div>
</body>
</html>