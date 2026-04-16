<?php
require_once '../includes/auth.php';
require_once '../includes/koneksi.php';
$active = 'kehadiran';
$id     = (int) $_GET['id'];
$data   = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM kehadiran WHERE id_kehadiran = $id"));
if (!$data) { header("Location: kehadiran.php"); exit; }
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_aparatur = (int) $_POST['id_aparatur'];
    $tanggal     = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $status      = mysqli_real_escape_string($koneksi, $_POST['status']);
    $keterangan  = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
    mysqli_query($koneksi, "UPDATE kehadiran SET id_aparatur=$id_aparatur, tanggal='$tanggal', status='$status', keterangan='$keterangan' WHERE id_kehadiran=$id");
    header("Location: kehadiran.php?pesan=simpan"); exit;
}
$aparatur = mysqli_query($koneksi, "SELECT * FROM aparatur ORDER BY nama");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Ketidakhadiran</title>
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
      <a href="kriteria.php">&#9632; Data Kriteria</a>
      <a href="kehadiran.php" class="active">&#9632; Kehadiran</a>
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
      <div class="topbar-title">Edit Ketidakhadiran</div>
      <div class="topbar-right">
        <span class="topbar-user"><?php echo htmlspecialchars($_SESSION['nama']); ?></span>
        <div class="avatar"><?php echo strtoupper(substr($_SESSION['nama'], 0, 1)); ?></div>
      </div>
    </header>
    <main class="content">
      <div class="breadcrumb">
        <a href="index.php">Beranda</a><span class="breadcrumb-sep">/</span>
        <a href="kehadiran.php">Kehadiran</a><span class="breadcrumb-sep">/</span>
        <span class="breadcrumb-active">Edit</span>
      </div>
      <div class="page-header">
        <div><div class="page-title">Edit Ketidakhadiran</div><div class="page-sub">Perbarui data ketidakhadiran aparatur</div></div>
      </div>
      <div class="card" style="max-width:520px;">
        <div class="card-head"><div class="card-head-title">Form Edit Ketidakhadiran</div></div>
        <div class="card-body">
          <form method="POST">
            <div class="form-group">
              <label class="form-label">Aparatur</label>
              <select name="id_aparatur" class="form-control" required>
                <?php while ($row = mysqli_fetch_assoc($aparatur)): ?>
                  <option value="<?php echo $row['id_aparatur']; ?>" <?php echo $row['id_aparatur'] == $data['id_aparatur'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($row['nama']); ?> — <?php echo htmlspecialchars($row['jabatan']); ?></option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Tanggal</label>
              <input type="date" name="tanggal" class="form-control" value="<?php echo $data['tanggal']; ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">Alasan Tidak Hadir</label>
              <select name="status" class="form-control" required>
                <option value="izin"  <?php echo $data['status'] == 'izin'  ? 'selected' : ''; ?>>Izin</option>
                <option value="sakit" <?php echo $data['status'] == 'sakit' ? 'selected' : ''; ?>>Sakit</option>
                <option value="alpha" <?php echo $data['status'] == 'alpha' ? 'selected' : ''; ?>>Alpha</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Keterangan <span style="color:var(--gray-300); font-weight:400;">(opsional)</span></label>
              <input type="text" name="keterangan" class="form-control" value="<?php echo htmlspecialchars($data['keterangan']); ?>">
            </div>
            <div class="form-actions">
              <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
              <a href="kehadiran.php" class="btn btn-ghost">Batal</a>
            </div>
          </form>
        </div>
      </div>
    </main>
  </div>
</div>
</body>
</html>