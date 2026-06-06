<?php
require_once '../includes/auth.php';
require_once '../includes/koneksi.php';
$active = 'kehadiran';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_aparatur = (int) $_POST['id_aparatur'];
    $tanggal     = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $status      = mysqli_real_escape_string($koneksi, $_POST['status']);
    $keterangan  = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
    $cek = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM kehadiran WHERE id_aparatur=$id_aparatur AND tanggal='$tanggal'"));
    if ($cek) {
        mysqli_query($koneksi, "UPDATE kehadiran SET status='$status', keterangan='$keterangan' WHERE id_aparatur=$id_aparatur AND tanggal='$tanggal'");
    } else {
        mysqli_query($koneksi, "INSERT INTO kehadiran (id_aparatur, tanggal, status, keterangan) VALUES ($id_aparatur, '$tanggal', '$status', '$keterangan')");
    }
    header("Location: kehadiran.php?pesan=simpan"); exit;
}
$aparatur  = mysqli_query($koneksi, "SELECT * FROM aparatur ORDER BY nama");
$rekap     = mysqli_query($koneksi, "
    SELECT a.nama, a.jabatan,
        COUNT(k.id_kehadiran) as total_tidak_hadir,
        SUM(k.status = 'izin')  as izin,
        SUM(k.status = 'sakit') as sakit,
        SUM(k.status = 'alpha') as alpha
    FROM aparatur a
    LEFT JOIN kehadiran k ON a.id_aparatur = k.id_aparatur
    GROUP BY a.id_aparatur
    ORDER BY total_tidak_hadir DESC
");
$kehadiran = mysqli_query($koneksi, "
    SELECT k.id_kehadiran, a.nama, k.tanggal, k.status, k.keterangan
    FROM kehadiran k
    JOIN aparatur a ON k.id_aparatur = a.id_aparatur
    ORDER BY k.tanggal DESC, a.nama
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ketidakhadiran Aparatur</title>
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
      <a href="kehadiran.php"       class="<?php echo $active=='kehadiran' ? 'active' : ''; ?>">&#9632; Ketidakhadiran</a>
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
      <div class="topbar-title">Ketidakhadiran</div>
      <div class="topbar-right">
        <span class="topbar-user"><?php echo htmlspecialchars($_SESSION['nama']); ?></span>
        <div class="avatar"><?php echo strtoupper(substr($_SESSION['nama'], 0, 1)); ?></div>
      </div>
    </header>
    <main class="content">
      <div class="breadcrumb">
        <a href="index.php">Beranda</a><span class="breadcrumb-sep">/</span>
        <span class="breadcrumb-active">Ketidakhadiran</span>
      </div>
      <div class="page-header">
        <div>
          <div class="page-title">Input Ketidakhadiran Aparatur</div>
          <div class="page-sub">Catat hanya jika aparatur <strong>tidak hadir</strong> — yang tidak diinput dianggap hadir</div>
        </div>
      </div>
      <div class="alert alert-info">&#9432; Kriteria <strong>Ketidakhadiran</strong> menggunakan atribut <strong>Cost</strong> — semakin sedikit ketidakhadiran, semakin tinggi nilainya dalam perhitungan SAW.</div>
      <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'simpan'): ?><div class="alert alert-success">Data ketidakhadiran berhasil disimpan.</div><?php endif; ?>
      <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'hapus'): ?><div class="alert alert-danger">Data ketidakhadiran berhasil dihapus.</div><?php endif; ?>

      <!-- Form Input -->
      <div class="card" style="max-width:560px; margin-bottom:28px;">
        <div class="card-head"><div class="card-head-title">Form Input Ketidakhadiran</div></div>
        <div class="card-body">
          <form method="POST">
            <div class="form-group">
              <label class="form-label">Aparatur</label>
              <select name="id_aparatur" class="form-control" required>
                <option value="">-- Pilih Aparatur --</option>
                <?php while ($row = mysqli_fetch_assoc($aparatur)): ?>
                  <option value="<?php echo $row['id_aparatur']; ?>"><?php echo htmlspecialchars($row['nama']); ?> — <?php echo htmlspecialchars($row['jabatan']); ?></option>
                <?php endwhile; ?>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Tanggal</label>
              <input type="date" name="tanggal" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="form-group">
              <label class="form-label">Alasan Tidak Hadir</label>
              <select name="status" class="form-control" required>
                <option value="">-- Pilih Alasan --</option>
                <option value="izin">Izin</option>
                <option value="sakit">Sakit</option>
                <option value="alpha">Alpha (Tanpa Keterangan)</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Keterangan <span style="color:var(--gray-300); font-weight:400;">(opsional)</span></label>
              <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Izin keperluan keluarga">
            </div>
            <div class="form-actions"><button type="submit" class="btn btn-primary">Simpan</button></div>
          </form>
        </div>
      </div>

      <!-- Rekap -->
      <div class="card" style="margin-bottom:24px;">
        <div class="card-head"><div class="card-head-title">Rekapitulasi Ketidakhadiran</div></div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th style="width:50px">No</th>
                <th>Nama Aparatur</th>
                <th style="width:80px; text-align:center">Izin</th>
                <th style="width:80px; text-align:center">Sakit</th>
                <th style="width:80px; text-align:center">Alpha</th>
                <th style="width:120px; text-align:center">Total</th>
              </tr>
            </thead>
            <tbody>
              <?php $no = 1; while ($row = mysqli_fetch_assoc($rekap)): ?>
              <tr>
                <td class="text-muted text-center"><?php echo $no++; ?></td>
                <td style="font-weight:500"><?php echo htmlspecialchars($row['nama']); ?> <span class="text-muted" style="font-size:12px;">— <?php echo htmlspecialchars($row['jabatan']); ?></span></td>
                <td class="text-center"><span class="badge badge-gold"><?php echo $row['izin'] ?? 0; ?></span></td>
                <td class="text-center"><span class="badge badge-gray"><?php echo $row['sakit'] ?? 0; ?></span></td>
                <td class="text-center"><span class="badge badge-red"><?php echo $row['alpha'] ?? 0; ?></span></td>
                <td class="text-center"><strong><?php echo $row['total_tidak_hadir'] ?? 0; ?></strong> hari</td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Detail -->
      <div class="card">
        <div class="card-head">
          <div class="card-head-title">Detail Ketidakhadiran</div>
          <span class="badge badge-green"><?php echo mysqli_num_rows($kehadiran); ?> Data</span>
        </div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th style="width:50px">No</th>
                <th>Nama Aparatur</th>
                <th style="width:120px">Tanggal</th>
                <th style="width:100px">Status</th>
                <th>Keterangan</th>
                <th style="width:160px; text-align:center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (mysqli_num_rows($kehadiran) == 0): ?>
                <tr><td colspan="6"><div class="empty-state"><div class="empty-state-icon">&#128197;</div><div class="empty-state-title">Belum ada data ketidakhadiran</div><div class="empty-state-sub">Semua aparatur dianggap hadir</div></div></td></tr>
              <?php else: ?>
                <?php $no = 1; while ($row = mysqli_fetch_assoc($kehadiran)): ?>
                <tr>
                  <td class="text-muted text-center"><?php echo $no++; ?></td>
                  <td style="font-weight:500"><?php echo htmlspecialchars($row['nama']); ?></td>
                  <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                  <td><?php $b = ['izin'=>'badge-gold','sakit'=>'badge-gray','alpha'=>'badge-red'][$row['status']] ?? 'badge-gray'; ?><span class="badge <?php echo $b; ?>"><?php echo ucfirst($row['status']); ?></span></td>
                  <td class="text-muted"><?php echo $row['keterangan'] ? htmlspecialchars($row['keterangan']) : '-'; ?></td>
                  <td>
                    <div class="d-flex gap-1 justify-center">
                      <a href="edit_kehadiran.php?id=<?php echo $row['id_kehadiran']; ?>" class="btn btn-outline btn-sm">Edit</a>
                      <a href="hapus_kehadiran.php?id=<?php echo $row['id_kehadiran']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">Hapus</a>
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