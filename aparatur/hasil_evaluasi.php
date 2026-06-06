<?php
require_once '../includes/auth_aparatur.php';
require_once '../includes/koneksi.php';

$active      = 'evaluasi';
$id_aparatur = $_SESSION['aparatur'];

$kriteria_list = mysqli_query($koneksi, "SELECT * FROM kriteria ORDER BY id_kriteria");
$detail_nilai  = [];
$total_saw     = 0;

while ($k = mysqli_fetch_assoc($kriteria_list)) {
    $id_kriteria   = $k['id_kriteria'];
    $bobot         = $k['bobot'];
    $atribut       = $k['atribut'];
    $nama_kriteria = $k['nama_kriteria'];

    if (strtolower($nama_kriteria) == 'ketidakhadiran') {
        // Nilai dari jumlah ketidakhadiran
        $row_nilai  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as nilai FROM kehadiran WHERE id_aparatur=$id_aparatur"));
        $nilai_asli = $row_nilai['nilai'] ?? 0;

        $max = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as max FROM kehadiran GROUP BY id_aparatur ORDER BY COUNT(*) DESC LIMIT 1"));
        $min = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as min FROM kehadiran GROUP BY id_aparatur ORDER BY COUNT(*) ASC LIMIT 1"));
        $max_val = $max['max'] ?? 0;
        $min_val = $min['min'] ?? 0;
    } else {
        // Nilai dari jumlah jobdesk disetujui
        $row_nilai  = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as nilai FROM jobdesk WHERE id_aparatur=$id_aparatur AND id_kriteria=$id_kriteria AND status='disetujui'"));
        $nilai_asli = $row_nilai['nilai'] ?? 0;

        $max = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as max FROM jobdesk WHERE id_kriteria=$id_kriteria AND status='disetujui' GROUP BY id_aparatur ORDER BY COUNT(*) DESC LIMIT 1"));
        $min = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as min FROM jobdesk WHERE id_kriteria=$id_kriteria AND status='disetujui' GROUP BY id_aparatur ORDER BY COUNT(*) ASC LIMIT 1"));
        $max_val = $max['max'] ?? 0;
        $min_val = $min['min'] ?? 0;
    }

    if ($atribut == 'benefit') {
        $normalisasi = $max_val > 0 ? $nilai_asli / $max_val : 0;
    } else {
        if ($nilai_asli == 0) {
            $normalisasi = 1;
        } else {
            $normalisasi = $min_val > 0 ? $min_val / $nilai_asli : 0;
        }
    }

    $skor       = round($normalisasi * $bobot, 4);
    $total_saw += $skor;

    $detail_nilai[] = [
        'nama'        => $nama_kriteria,
        'atribut'     => $atribut,
        'bobot'       => $bobot,
        'nilai'       => $nilai_asli,
        'normalisasi' => round($normalisasi, 4),
        'skor'        => $skor,
    ];
}

$total_saw    = round($total_saw, 4);
$total_persen = round($total_saw * 100, 2);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hasil Evaluasi</title>
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
      <a href="tugas.php">&#9632; Tugas Saya</a>
      <a href="hasil_evaluasi.php" class="active">&#9632; Hasil Evaluasi</a>
      <div class="nav-section">Akun</div>
      <a href="logout.php" style="color:rgba(255,100,100,0.7);">&#9632; Logout</a>
    </nav>
    <div class="sidebar-footer">Login sebagai: <strong style="color:var(--green-light);"><?php echo htmlspecialchars($_SESSION['aparatur_nama']); ?></strong></div>
  </aside>
  <div class="main">
    <header class="topbar">
      <div class="topbar-title">Hasil Evaluasi</div>
      <div class="topbar-right">
        <span class="topbar-user"><?php echo htmlspecialchars($_SESSION['aparatur_nama']); ?></span>
        <div class="avatar"><?php echo strtoupper(substr($_SESSION['aparatur_nama'], 0, 1)); ?></div>
      </div>
    </header>
    <main class="content">
      <div class="breadcrumb">
        <a href="dashboard.php">Beranda</a>
        <span class="breadcrumb-sep">/</span>
        <span class="breadcrumb-active">Hasil Evaluasi</span>
      </div>
      <div class="page-header">
        <div>
          <div class="page-title">Hasil Evaluasi Kinerja</div>
          <div class="page-sub">Nilai dihitung otomatis dari jobdesk yang disetujui Kepala Desa</div>
        </div>
      </div>

      <!-- Info -->
      <div class="alert alert-info" style="margin-bottom:20px;">
        &#9432; Nilai kamu dihitung dari <strong>jumlah jobdesk yang disetujui</strong> Kepala Desa per kriteria. Semakin banyak jobdesk disetujui, semakin tinggi nilaimu!
      </div>

      <!-- Banner nilai -->
      <?php if ($total_saw > 0): ?>
      <div class="winner-banner" style="margin-bottom:24px;">
        <div>
          <div class="winner-label">&#127942; Nilai SAW Kamu</div>
          <div class="winner-name"><?php echo htmlspecialchars($_SESSION['aparatur_nama']); ?></div>
          <div style="font-size:13px; color:var(--green-light); margin-top:4px;"><?php echo htmlspecialchars($_SESSION['aparatur_jabatan']); ?></div>
        </div>
        <div>
          <div class="winner-score"><?php echo $total_saw; ?></div>
          <div style="font-size:13px; color:var(--green-light); text-align:right;"><?php echo $total_persen; ?>%</div>
        </div>
      </div>
      <?php else: ?>
      <div class="alert alert-warning">Jobdesk kamu belum ada yang disetujui Kepala Desa. Segera tambahkan jobdesk dan konfirmasi!</div>
      <?php endif; ?>

      <!-- Tabel detail -->
      <div class="card">
        <div class="card-head"><div class="card-head-title">Detail Perhitungan SAW</div></div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Kriteria</th>
                <th style="width:80px">Atribut</th>
                <th style="width:100px; text-align:center">Nilai</th>
                <th style="width:80px; text-align:center">Bobot</th>
                <th style="width:110px; text-align:center">Normalisasi</th>
                <th style="width:100px; text-align:center">Skor</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($detail_nilai as $d): ?>
              <tr>
                <td style="font-weight:500"><?php echo htmlspecialchars($d['nama']); ?></td>
                <td>
                  <?php if (strtolower($d['atribut']) == 'benefit'): ?>
                    <span class="badge badge-green">Benefit</span>
                  <?php else: ?>
                    <span class="badge badge-red">Cost</span>
                  <?php endif; ?>
                </td>
                <td class="text-center">
                  <?php if (strtolower($d['nama']) == 'ketidakhadiran'): ?>
                    <span class="badge badge-<?php echo $d['nilai'] == 0 ? 'green' : 'red'; ?>"><?php echo $d['nilai']; ?> hari</span>
                  <?php else: ?>
                    <span class="badge badge-green"><?php echo $d['nilai']; ?> jobdesk</span>
                  <?php endif; ?>
                </td>
                <td class="text-center"><?php echo $d['bobot']; ?></td>
                <td class="text-center"><?php echo $d['normalisasi']; ?></td>
                <td class="text-center"><strong><?php echo $d['skor']; ?></strong></td>
              </tr>
              <?php endforeach; ?>
              <tr style="background:var(--green-pale);">
                <td colspan="5" style="font-weight:700; text-align:right; padding-right:16px;">Total Nilai SAW</td>
                <td class="text-center"><strong style="font-size:15px; color:var(--green-dark);"><?php echo $total_saw; ?></strong></td>
              </tr>
              <tr style="background:var(--green-pale);">
                <td colspan="5" style="font-weight:700; text-align:right; padding-right:16px;">Nilai Akhir (%)</td>
                <td class="text-center"><strong style="font-size:15px; color:var(--green-dark);"><?php echo $total_persen; ?>%</strong></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </main>
  </div>
</div>
</body>
</html>