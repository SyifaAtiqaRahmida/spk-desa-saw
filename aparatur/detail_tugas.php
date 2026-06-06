<?php
require_once '../includes/auth_aparatur.php';
require_once '../includes/koneksi.php';

$active      = 'tugas';
$id_aparatur = $_SESSION['aparatur'];
$id_kriteria = (int) $_GET['id_kriteria'];

$kriteria = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM kriteria WHERE id_kriteria=$id_kriteria"));
if (!$kriteria) { header("Location: tugas.php"); exit; }

// Tambah jobdesk
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deskripsi'])) {
    $deskripsi    = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $tanggal      = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    mysqli_query($koneksi, "INSERT INTO jobdesk (id_aparatur, id_kriteria, deskripsi, tanggal_input) VALUES ($id_aparatur, $id_kriteria, '$deskripsi', '$tanggal')");
    header("Location: detail_tugas.php?id_kriteria=$id_kriteria&pesan=tambah"); exit;
}

$jobdesk_list = mysqli_query($koneksi, "SELECT * FROM jobdesk WHERE id_aparatur=$id_aparatur AND id_kriteria=$id_kriteria ORDER BY tanggal_input DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Tugas — <?php echo htmlspecialchars($kriteria['nama_kriteria']); ?></title>
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
      <a href="dashboard.php">&#9632; Beranda</a>
      <a href="tugas.php" class="active">&#9632; Tugas Saya</a>
      <a href="hasil_evaluasi.php">&#9632; Hasil Evaluasi</a>
      <div class="nav-section">Akun</div>
      <a href="logout.php" style="color:rgba(255,100,100,0.7);">&#9632; Logout</a>
    </nav>
    <div class="sidebar-footer">Login sebagai: <strong style="color:var(--green-light);"><?php echo htmlspecialchars($_SESSION['aparatur_nama']); ?></strong></div>
  </aside>
  <div class="main">
    <header class="topbar">
      <div class="topbar-title"><?php echo htmlspecialchars($kriteria['nama_kriteria']); ?></div>
      <div class="topbar-right">
        <span class="topbar-user"><?php echo htmlspecialchars($_SESSION['aparatur_nama']); ?></span>
        <div class="avatar"><?php echo strtoupper(substr($_SESSION['aparatur_nama'], 0, 1)); ?></div>
      </div>
    </header>
    <main class="content">
      <div class="breadcrumb">
        <a href="dashboard.php">Beranda</a>
        <span class="breadcrumb-sep">/</span>
        <a href="tugas.php">Tugas Saya</a>
        <span class="breadcrumb-sep">/</span>
        <span class="breadcrumb-active"><?php echo htmlspecialchars($kriteria['nama_kriteria']); ?></span>
      </div>

      <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'tambah'): ?>
        <div class="alert alert-success">Jobdesk berhasil ditambahkan!</div>
      <?php endif; ?>
      <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'konfirmasi'): ?>
        <div class="alert alert-success">Bukti tugas berhasil dikirim!</div>
      <?php endif; ?>

      <!-- Info kriteria -->
      <div class="card" style="margin-bottom:20px;">
        <div class="card-body">
          <div class="d-flex gap-2 align-center flex-wrap">
            <div>
              <div style="font-size:13px; color:var(--gray-500);">Kriteria</div>
              <div style="font-size:16px; font-weight:700;"><?php echo htmlspecialchars($kriteria['nama_kriteria']); ?></div>
            </div>
            <div style="margin-left:24px;">
              <div style="font-size:13px; color:var(--gray-500);">Bobot</div>
              <div style="font-size:16px; font-weight:700;"><?php echo $kriteria['bobot']; ?></div>
            </div>
            <div style="margin-left:24px;">
              <div style="font-size:13px; color:var(--gray-500);">Atribut</div>
              <div>
                <?php if (strtolower($kriteria['atribut']) == 'benefit'): ?>
                  <span class="badge badge-green">Benefit</span>
                <?php else: ?>
                  <span class="badge badge-red">Cost</span>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Form tambah jobdesk -->
      <div class="card" style="max-width:560px; margin-bottom:24px;">
        <div class="card-head"><div class="card-head-title">+ Tambah Jobdesk</div></div>
        <div class="card-body">
          <form method="POST">
            <div class="form-group">
              <label class="form-label">Deskripsi Tugas</label>
              <textarea name="deskripsi" class="form-control" placeholder="Jelaskan tugas yang kamu kerjakan..." required></textarea>
            </div>
            <div class="form-group">
              <label class="form-label">Tanggal</label>
              <input type="date" name="tanggal" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="form-actions">
              <button type="submit" class="btn btn-primary">Tambah Jobdesk</button>
              <a href="tugas.php" class="btn btn-ghost">Kembali</a>
            </div>
          </form>
        </div>
      </div>

      <!-- Daftar jobdesk -->
      <div class="card">
        <div class="card-head">
          <div class="card-head-title">Daftar Jobdesk</div>
          <span class="badge badge-green"><?php echo mysqli_num_rows($jobdesk_list); ?> Tugas</span>
        </div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th style="width:50px">No</th>
                <th>Deskripsi Tugas</th>
                <th style="width:110px">Tanggal</th>
                <th style="width:120px; text-align:center">Status</th>
                <th style="width:120px; text-align:center">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if (mysqli_num_rows($jobdesk_list) == 0): ?>
                <tr><td colspan="5"><div class="empty-state"><div class="empty-state-icon">&#128203;</div><div class="empty-state-title">Belum ada jobdesk</div><div class="empty-state-sub">Gunakan form di atas untuk menambahkan</div></div></td></tr>
              <?php else: ?>
                <?php $no = 1; while ($row = mysqli_fetch_assoc($jobdesk_list)): ?>
                <tr>
                  <td class="text-muted text-center"><?php echo $no++; ?></td>
                  <td><?php echo htmlspecialchars($row['deskripsi']); ?></td>
                  <td><?php echo date('d/m/Y', strtotime($row['tanggal_input'])); ?></td>
                  <td class="text-center">
                    <?php
                      $badges = [
                        'belum_selesai' => ['badge-gray', 'Belum Selesai'],
                        'selesai'       => ['badge-gold', 'Menunggu'],
                        'disetujui'     => ['badge-green', 'Disetujui'],
                        'perlu_revisi'  => ['badge-red', 'Perlu Revisi'],
                      ];
                      $b = $badges[$row['status']] ?? ['badge-gray', $row['status']];
                    ?>
                    <span class="badge <?php echo $b[0]; ?>"><?php echo $b[1]; ?></span>
                  </td>
                  <td class="text-center">
                    <?php if ($row['status'] == 'belum_selesai' || $row['status'] == 'perlu_revisi'): ?>
                      <a href="konfirmasi_tugas.php?id=<?php echo $row['id_jobdesk']; ?>" class="btn btn-primary btn-sm">Konfirmasi</a>
                    <?php else: ?>
                      <span class="text-muted" style="font-size:12px;">Terkirim</span>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php if ($row['catatan_kepala']): ?>
                <tr>
                  <td colspan="5" style="background:#fffbeb; padding:8px 14px; font-size:12px;">
                    &#128172; <strong>Catatan:</strong> <?php echo htmlspecialchars($row['catatan_kepala']); ?>
                  </td>
                </tr>
                <?php endif; ?>
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