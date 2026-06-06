<?php
require_once '../includes/auth_aparatur.php';
require_once '../includes/koneksi.php';

$active      = 'tugas';
$id_aparatur = $_SESSION['aparatur'];
$id_jobdesk  = (int) $_GET['id'];

$jobdesk = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT j.*, k.nama_kriteria, k.id_kriteria
    FROM jobdesk j
    JOIN kriteria k ON j.id_kriteria = k.id_kriteria
    WHERE j.id_jobdesk=$id_jobdesk AND j.id_aparatur=$id_aparatur
"));
if (!$jobdesk) { header("Location: tugas.php"); exit; }

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $keterangan          = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
    $tanggal_pelaksanaan = mysqli_real_escape_string($koneksi, $_POST['tanggal_pelaksanaan']);
    $foto_name           = '';

    // Proses upload foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
        $ext     = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $error = 'Format file tidak didukung! Gunakan JPG, PNG, atau PDF.';
        } elseif ($_FILES['foto']['size'] > 5 * 1024 * 1024) {
            $error = 'Ukuran file terlalu besar! Maksimal 5MB.';
        } else {
            $foto_name = 'bukti_' . time() . '_' . $id_aparatur . '.' . $ext;
            $upload_path = '../uploads/' . $foto_name;
            if (!move_uploaded_file($_FILES['foto']['tmp_name'], $upload_path)) {
                $error = 'Gagal mengupload foto. Pastikan folder uploads sudah dibuat.';
                $foto_name = '';
            }
        }
    } else {
        $error = 'Foto bukti wajib diupload!';
    }

    if (!$error) {
        $foto_escape = mysqli_real_escape_string($koneksi, $foto_name);
        mysqli_query($koneksi, "INSERT INTO bukti_tugas (id_jobdesk, keterangan, tanggal_pelaksanaan, foto) VALUES ($id_jobdesk, '$keterangan', '$tanggal_pelaksanaan', '$foto_escape')");
        mysqli_query($koneksi, "UPDATE jobdesk SET status='selesai' WHERE id_jobdesk=$id_jobdesk");
        header("Location: detail_tugas.php?id_kriteria={$jobdesk['id_kriteria']}&pesan=konfirmasi"); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Konfirmasi Tugas</title>
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
      <div class="topbar-title">Konfirmasi Tugas</div>
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
        <a href="detail_tugas.php?id_kriteria=<?php echo $jobdesk['id_kriteria']; ?>"><?php echo htmlspecialchars($jobdesk['nama_kriteria']); ?></a>
        <span class="breadcrumb-sep">/</span>
        <span class="breadcrumb-active">Konfirmasi</span>
      </div>

      <div class="page-header">
        <div>
          <div class="page-title">Konfirmasi Tugas Selesai</div>
          <div class="page-sub">Isi keterangan, tanggal pelaksanaan, dan upload foto bukti tugas</div>
        </div>
      </div>

      <!-- Info jobdesk -->
      <div class="card" style="max-width:560px; margin-bottom:20px;">
        <div class="card-body">
          <div style="font-size:12px; color:var(--gray-500); margin-bottom:4px;">TUGAS</div>
          <div style="font-size:15px; font-weight:500;"><?php echo htmlspecialchars($jobdesk['deskripsi']); ?></div>
          <div style="font-size:12px; color:var(--gray-500); margin-top:8px;">Kriteria: <strong><?php echo htmlspecialchars($jobdesk['nama_kriteria']); ?></strong></div>
        </div>
      </div>

      <?php if ($error): ?>
        <div class="alert alert-danger" style="max-width:560px;"><?php echo $error; ?></div>
      <?php endif; ?>

      <!-- Form konfirmasi -->
      <div class="card" style="max-width:560px;">
        <div class="card-head"><div class="card-head-title">Form Konfirmasi</div></div>
        <div class="card-body">
          <form method="POST" enctype="multipart/form-data">

            <div class="form-group">
              <label class="form-label">Tanggal Pelaksanaan</label>
              <input type="date" name="tanggal_pelaksanaan" class="form-control"
                     value="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <div class="form-group">
              <label class="form-label">Keterangan</label>
              <textarea name="keterangan" class="form-control"
                        placeholder="Jelaskan secara singkat hasil pekerjaan kamu..." required></textarea>
              <div class="form-hint">Jelaskan apa yang sudah dikerjakan</div>
            </div>

            <div class="form-group">
              <label class="form-label">Foto Bukti <span style="color:var(--danger); font-size:11px;">*Wajib</span></label>
              <input type="file" name="foto" class="form-control" accept=".jpg,.jpeg,.png,.pdf" required>
              <div class="form-hint">Format: JPG, PNG, atau PDF. Maksimal 5MB.</div>
            </div>

            <div class="form-actions">
              <button type="submit" class="btn btn-primary">Kirim Konfirmasi</button>
              <a href="detail_tugas.php?id_kriteria=<?php echo $jobdesk['id_kriteria']; ?>" class="btn btn-ghost">Batal</a>
            </div>

          </form>
        </div>
      </div>

    </main>
  </div>
</div>
</body>
</html>
