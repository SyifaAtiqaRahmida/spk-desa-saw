<?php
require_once '../includes/auth_kepala_desa.php';
require_once '../includes/koneksi.php';

$active = 'verifikasi';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_jobdesk = (int) $_POST['id_jobdesk'];
    $aksi       = $_POST['aksi'];
    $catatan    = mysqli_real_escape_string($koneksi, $_POST['catatan']);

    if ($aksi == 'setujui') {
        mysqli_query($koneksi, "UPDATE jobdesk SET status='disetujui', catatan_kepala='$catatan' WHERE id_jobdesk=$id_jobdesk");
    } elseif ($aksi == 'tolak') {
        mysqli_query($koneksi, "UPDATE jobdesk SET status='perlu_revisi', catatan_kepala='$catatan' WHERE id_jobdesk=$id_jobdesk");
    }
    header("Location: verifikasi.php?pesan=berhasil"); exit;
}

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'menunggu';
if ($filter == 'menunggu')   $where = "WHERE j.status='selesai'";
elseif ($filter == 'disetujui') $where = "WHERE j.status='disetujui'";
elseif ($filter == 'ditolak')   $where = "WHERE j.status='perlu_revisi'";
else $where = "";

$jobdesk_list = mysqli_query($koneksi, "
    SELECT j.*, a.nama as nama_aparatur, a.jabatan, k.nama_kriteria,
           b.keterangan as bukti, b.tanggal_pelaksanaan, b.foto
    FROM jobdesk j
    JOIN aparatur a ON j.id_aparatur = a.id_aparatur
    JOIN kriteria k ON j.id_kriteria = k.id_kriteria
    LEFT JOIN bukti_tugas b ON j.id_jobdesk = b.id_jobdesk
    $where
    ORDER BY j.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verifikasi Jobdesk</title>
  <link rel="stylesheet" href="../style.css">
</head>
<body>
<div class="layout">
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-brand-name">Portal Kepala Desa</div>
      <div class="sidebar-brand-sub">SPK Desa Tatah Mesjid</div>
    </div>
    <nav class="sidebar-nav">
      <div class="nav-section">Menu</div>
      <a href="dashboard.php"  class="<?php echo $active=='dashboard'  ? 'active' : ''; ?>">&#9632; Beranda</a>
      <a href="verifikasi.php" class="<?php echo $active=='verifikasi' ? 'active' : ''; ?>">&#9632; Verifikasi Jobdesk</a>
      <a href="ranking.php"    class="<?php echo $active=='ranking'    ? 'active' : ''; ?>">&#9632; Hasil Ranking SAW</a>
      <div class="nav-section">Akun</div>
      <a href="logout.php" style="color:rgba(255,100,100,0.7);">&#9632; Logout</a>
    </nav>
    <div class="sidebar-footer">Login sebagai: <strong style="color:var(--green-light);"><?php echo htmlspecialchars($_SESSION['kepala_desa_nama']); ?></strong></div>
  </aside>
  <div class="main">
    <header class="topbar">
      <div class="topbar-title">Verifikasi Jobdesk</div>
      <div class="topbar-right">
        <span class="topbar-user"><?php echo htmlspecialchars($_SESSION['kepala_desa_nama']); ?></span>
        <div class="avatar"><?php echo strtoupper(substr($_SESSION['kepala_desa_nama'], 0, 1)); ?></div>
      </div>
    </header>
    <main class="content">
      <div class="breadcrumb">
        <a href="dashboard.php">Beranda</a>
        <span class="breadcrumb-sep">/</span>
        <span class="breadcrumb-active">Verifikasi Jobdesk</span>
      </div>
      <div class="page-header">
        <div>
          <div class="page-title">Verifikasi Jobdesk Aparatur</div>
          <div class="page-sub">Approve atau tolak jobdesk yang sudah dikonfirmasi aparatur</div>
        </div>
      </div>

      <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'berhasil'): ?>
        <div class="alert alert-success">Verifikasi berhasil disimpan.</div>
      <?php endif; ?>

      <!-- Filter -->
      <div class="d-flex gap-1 mb-3 flex-wrap">
        <a href="verifikasi.php?filter=menunggu"  class="btn <?php echo $filter=='menunggu'  ? 'btn-primary' : 'btn-ghost'; ?> btn-sm">Menunggu</a>
        <a href="verifikasi.php?filter=disetujui" class="btn <?php echo $filter=='disetujui' ? 'btn-primary' : 'btn-ghost'; ?> btn-sm">Disetujui</a>
        <a href="verifikasi.php?filter=ditolak"   class="btn <?php echo $filter=='ditolak'   ? 'btn-primary' : 'btn-ghost'; ?> btn-sm">Perlu Revisi</a>
        <a href="verifikasi.php?filter=semua"     class="btn <?php echo $filter=='semua'     ? 'btn-primary' : 'btn-ghost'; ?> btn-sm">Semua</a>
      </div>

      <?php if (mysqli_num_rows($jobdesk_list) == 0): ?>
        <div class="card">
          <div class="card-body">
            <div class="empty-state">
              <div class="empty-state-icon">&#10003;</div>
              <div class="empty-state-title">Tidak ada jobdesk</div>
              <div class="empty-state-sub">Belum ada jobdesk yang perlu diverifikasi</div>
            </div>
          </div>
        </div>
      <?php else: ?>
        <?php while ($row = mysqli_fetch_assoc($jobdesk_list)): ?>
        <div class="card" style="margin-bottom:16px;">
          <div class="card-head">
            <div>
              <div style="font-weight:600; font-size:14px;"><?php echo htmlspecialchars($row['nama_aparatur']); ?></div>
              <div style="font-size:12px; color:var(--gray-500);"><?php echo htmlspecialchars($row['jabatan']); ?> &bull; Kriteria: <strong><?php echo htmlspecialchars($row['nama_kriteria']); ?></strong></div>
            </div>
            <?php
              $badges = [
                'selesai'       => ['badge-gold',  'Menunggu Verifikasi'],
                'disetujui'     => ['badge-green', 'Disetujui'],
                'perlu_revisi'  => ['badge-red',   'Perlu Revisi'],
                'belum_selesai' => ['badge-gray',  'Belum Selesai'],
              ];
              $b = $badges[$row['status']] ?? ['badge-gray', $row['status']];
            ?>
            <span class="badge <?php echo $b[0]; ?>"><?php echo $b[1]; ?></span>
          </div>
          <div class="card-body">

            <!-- Deskripsi tugas -->
            <div style="margin-bottom:12px;">
              <div style="font-size:11px; color:var(--gray-500); margin-bottom:4px; text-transform:uppercase;">Deskripsi Tugas</div>
              <div><?php echo htmlspecialchars($row['deskripsi']); ?></div>
              <div style="font-size:12px; color:var(--gray-500); margin-top:4px;">Diinput: <?php echo date('d/m/Y', strtotime($row['tanggal_input'])); ?></div>
            </div>

            <!-- Bukti pelaksanaan -->
            <?php if ($row['bukti']): ?>
            <div style="margin-bottom:12px; background:var(--green-pale); padding:12px; border-radius:var(--radius-md);">
              <div style="font-size:11px; color:var(--gray-500); margin-bottom:8px; text-transform:uppercase;">Bukti Pelaksanaan</div>
              <div style="margin-bottom:6px;"><?php echo htmlspecialchars($row['bukti']); ?></div>
              <div style="font-size:12px; color:var(--gray-500); margin-bottom:10px;">Tanggal: <?php echo date('d/m/Y', strtotime($row['tanggal_pelaksanaan'])); ?></div>

              <!-- Foto bukti -->
              <?php if ($row['foto']): ?>
                <?php
                  $ext = strtolower(pathinfo($row['foto'], PATHINFO_EXTENSION));
                ?>
                <div style="font-size:11px; color:var(--gray-500); margin-bottom:6px; text-transform:uppercase;">Foto Bukti</div>
                <?php if ($ext == 'pdf'): ?>
                  <a href="../uploads/<?php echo htmlspecialchars($row['foto']); ?>" target="_blank" class="btn btn-outline btn-sm">&#128196; Lihat PDF</a>
                <?php else: ?>
                  <div style="margin-top:6px;">
                    <img src="../uploads/<?php echo htmlspecialchars($row['foto']); ?>"
                         alt="Bukti foto"
                         style="max-width:100%; max-height:300px; border-radius:var(--radius-md); border:1px solid var(--color-border-tertiary); cursor:pointer;"
                         onclick="window.open('../uploads/<?php echo htmlspecialchars($row['foto']); ?>', '_blank')">
                    <div style="font-size:11px; color:var(--gray-500); margin-top:4px;">Klik foto untuk memperbesar</div>
                  </div>
                <?php endif; ?>
              <?php else: ?>
                <div class="alert alert-warning" style="margin-top:8px;">Aparatur belum mengupload foto bukti.</div>
              <?php endif; ?>
            </div>
            <?php else: ?>
              <div class="alert alert-warning" style="margin-bottom:12px;">Aparatur belum mengisi bukti pelaksanaan.</div>
            <?php endif; ?>

            <!-- Catatan sebelumnya -->
            <?php if ($row['catatan_kepala']): ?>
            <div style="margin-bottom:12px; background:var(--warning-pale); padding:12px; border-radius:var(--radius-md);">
              <div style="font-size:11px; color:var(--gray-500); margin-bottom:4px; text-transform:uppercase;">Catatan Sebelumnya</div>
              <div><?php echo htmlspecialchars($row['catatan_kepala']); ?></div>
            </div>
            <?php endif; ?>

            <!-- Form verifikasi -->
            <?php if ($row['status'] == 'selesai'): ?>
            <form method="POST" style="border-top:1px solid #f0f0f0; padding-top:14px; margin-top:4px;">
              <input type="hidden" name="id_jobdesk" value="<?php echo $row['id_jobdesk']; ?>">
              <div class="form-group">
                <label class="form-label">Catatan <span style="color:var(--gray-300); font-weight:400;">(opsional)</span></label>
                <input type="text" name="catatan" class="form-control" placeholder="Tulis catatan untuk aparatur...">
              </div>
              <div class="d-flex gap-1">
                <button type="submit" name="aksi" value="setujui" class="btn btn-primary">&#10003; Setujui</button>
                <button type="submit" name="aksi" value="tolak" class="btn btn-danger">&#10007; Perlu Revisi</button>
              </div>
            </form>
            <?php endif; ?>

          </div>
        </div>
        <?php endwhile; ?>
      <?php endif; ?>

    </main>
  </div>
</div>
</body>
</html>
