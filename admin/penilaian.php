<?php
require_once '../includes/auth.php';
require_once '../includes/koneksi.php';
$active = 'penilaian';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_aparatur = (int) $_POST['id_aparatur'];
    $id_kriteria = (int) $_POST['id_kriteria'];
    $nilai       = mysqli_real_escape_string($koneksi, $_POST['nilai']);
    $cek = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM penilaian WHERE id_aparatur=$id_aparatur AND id_kriteria=$id_kriteria"));
    if ($cek) {
        mysqli_query($koneksi, "UPDATE penilaian SET nilai='$nilai' WHERE id_aparatur=$id_aparatur AND id_kriteria=$id_kriteria");
    } else {
        mysqli_query($koneksi, "INSERT INTO penilaian (id_aparatur, id_kriteria, nilai) VALUES ($id_aparatur, $id_kriteria, '$nilai')");
    }
    header("Location: penilaian.php?pesan=simpan"); exit;
}
$aparatur  = mysqli_query($koneksi, "SELECT * FROM aparatur");
$kriteria  = mysqli_query($koneksi, "SELECT * FROM kriteria");
$penilaian = mysqli_query($koneksi, "SELECT p.id_penilaian, a.nama, k.nama_kriteria, p.nilai FROM penilaian p JOIN aparatur a ON p.id_aparatur = a.id_aparatur JOIN kriteria k ON p.id_kriteria = k.id_kriteria ORDER BY a.nama, k.nama_kriteria");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Penilaian Aparatur</title>
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
      <div class="topbar-title">Penilaian</div>
      <div class="topbar-right">
        <span class="topbar-user"><?php echo htmlspecialchars($_SESSION['nama']); ?></span>
        <div class="avatar"><?php echo strtoupper(substr($_SESSION['nama'], 0, 1)); ?></div>
      </div>
    </header>
    <main class="content">
      <div class="breadcrumb">
        <a href="index.php">Beranda</a><span class="breadcrumb-sep">/</span>
        <span class="breadcrumb-active">Penilaian</span>
      </div>
      <div class="page-header">
        <div><div class="page-title">Input Penilaian Aparatur</div>
        <div class="page-sub">Masukkan nilai tiap aparatur berdasarkan kriteria yang sudah ditentukan</div></div>
      </div>
      <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'simpan'): ?><div class="alert alert-success">Nilai berhasil disimpan.</div><?php endif; ?>
      <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'hapus'): ?><div class="alert alert-danger">Data penilaian berhasil dihapus.</div><?php endif; ?>
      <div class="card" style="max-width:560px; margin-bottom:28px;">
        <div class="card-head"><div class="card-head-title">Form Input Nilai</div></div>
        <div class="card-body">
          <form method="POST">
            <div class="form-group">
              <label class="form-label">Pilih Aparatur</label>
              <select name="id_aparatur" class="form-control" required>
                <option value="">-- Pilih Aparatur --</option>
                <?php while ($row = mysqli_fetch_assoc($aparatur)): ?>
                  <option value="<?php echo $row['id_aparatur']; ?>"><?php echo htmlspecialchars($row['nama']); ?></option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Pilih Kriteria</label>
              <select name="id_kriteria" class="form-control" required>
                <option value="">-- Pilih Kriteria --</option>
                <?php while ($row = mysqli_fetch_assoc($kriteria)): ?>
                  <option value="<?php echo $row['id_kriteria']; ?>"><?php echo htmlspecialchars($row['nama_kriteria']); ?> (bobot: <?php echo $row['bobot']; ?> - <?php echo $row['atribut']; ?>)</option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Nilai</label>
              <input type="number" name="nilai" class="form-control" placeholder="Masukkan nilai" step="0.01" min="0" required>
              <div class="form-hint">Jika data sudah ada, nilai akan diperbarui otomatis</div>
            </div>
            <div class="form-actions"><button type="submit" class="btn btn-primary">Simpan Nilai</button></div>
          </form>
        </div>
      </div>
      <div class="card">
        <div class="card-head">
          <div class="card-head-title">Data Penilaian</div>
          <span class="badge badge-green"><?php echo mysqli_num_rows($penilaian); ?> Data</span>
        </div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th style="width:50px">No</th>
                <th>Nama Aparatur</th>
                <th>Kriteria</th>
                <th style="width:100px; text-align:center">Nilai</th>
                <th style="width:100px; text-align:center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (mysqli_num_rows($penilaian) == 0): ?>
                <tr><td colspan="5"><div class="empty-state"><div class="empty-state-icon">&#128203;</div><div class="empty-state-title">Belum ada data penilaian</div></div></td></tr>
              <?php else: ?>
                <?php $no = 1; while ($row = mysqli_fetch_assoc($penilaian)): ?>
                <tr>
                  <td class="text-muted text-center"><?php echo $no++; ?></td>
                  <td style="font-weight:500"><?php echo htmlspecialchars($row['nama']); ?></td>
                  <td><?php echo htmlspecialchars($row['nama_kriteria']); ?></td>
                  <td class="text-center"><span class="badge badge-green"><?php echo $row['nilai']; ?></span></td>
                  <td class="text-center">
                    <a href="hapus_penilaian.php?id=<?php echo $row['id_penilaian']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">Hapus</a>
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