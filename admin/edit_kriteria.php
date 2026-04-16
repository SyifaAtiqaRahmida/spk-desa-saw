<?php
require_once '../includes/auth.php';
require_once '../includes/koneksi.php';
$active = 'kriteria';
$id     = (int) $_GET['id'];
$data   = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM kriteria WHERE id_kriteria = $id"));
if (!$data) { header("Location: kriteria.php"); exit; }
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_kriteria = mysqli_real_escape_string($koneksi, $_POST['nama_kriteria']);
    $bobot         = mysqli_real_escape_string($koneksi, $_POST['bobot']);
    $atribut       = mysqli_real_escape_string($koneksi, $_POST['atribut']);
    mysqli_query($koneksi, "UPDATE kriteria SET nama_kriteria='$nama_kriteria', bobot='$bobot', atribut='$atribut' WHERE id_kriteria=$id");
    header("Location: kriteria.php?pesan=edit"); exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Kriteria</title>
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
      <div class="topbar-title">Edit Kriteria</div>
      <div class="topbar-right">
        <span class="topbar-user"><?php echo htmlspecialchars($_SESSION['nama']); ?></span>
        <div class="avatar"><?php echo strtoupper(substr($_SESSION['nama'], 0, 1)); ?></div>
      </div>
    </header>
    <main class="content">
      <div class="breadcrumb">
        <a href="index.php">Beranda</a><span class="breadcrumb-sep">/</span>
        <a href="kriteria.php">Data Kriteria</a><span class="breadcrumb-sep">/</span>
        <span class="breadcrumb-active">Edit</span>
      </div>
      <div class="page-header">
        <div><div class="page-title">Edit Kriteria</div>
        <div class="page-sub">Perbarui data: <strong><?php echo htmlspecialchars($data['nama_kriteria']); ?></strong></div></div>
      </div>
      <div class="card" style="max-width:520px;">
        <div class="card-head"><div class="card-head-title">Form Edit Kriteria</div></div>
        <div class="card-body">
          <form method="POST">
            <div class="form-group">
              <label class="form-label">Nama Kriteria</label>
              <input type="text" name="nama_kriteria" class="form-control" value="<?php echo htmlspecialchars($data['nama_kriteria']); ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">Bobot (0 - 1)</label>
              <input type="number" name="bobot" class="form-control" value="<?php echo $data['bobot']; ?>" step="0.01" min="0" max="1" required>
            </div>
            <div class="form-group">
              <label class="form-label">Atribut</label>
              <select name="atribut" class="form-control" required>
                <option value="benefit" <?php echo strtolower($data['atribut']) == 'benefit' ? 'selected' : ''; ?>>Benefit</option>
                <option value="cost"    <?php echo strtolower($data['atribut']) == 'cost'    ? 'selected' : ''; ?>>Cost</option>
              </select>
            </div>
            <div class="form-actions">
              <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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