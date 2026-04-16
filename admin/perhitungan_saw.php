<?php
require_once '../includes/auth.php';
require_once '../includes/koneksi.php';
$active = 'saw';
$hasil  = [];
$aparatur = mysqli_query($koneksi, "SELECT * FROM aparatur");
while ($a = mysqli_fetch_array($aparatur)) {
    $id_aparatur = $a['id_aparatur'];
    $nama        = $a['nama'];
    $total       = 0;
    $kriteria = mysqli_query($koneksi, "SELECT * FROM kriteria");
    while ($k = mysqli_fetch_array($kriteria)) {
        $id_kriteria = $k['id_kriteria'];
        $bobot       = $k['bobot'];
        $atribut     = $k['atribut'];
        $nilai      = mysqli_query($koneksi, "SELECT nilai FROM penilaian WHERE id_aparatur='$id_aparatur' AND id_kriteria='$id_kriteria'");
        $n          = mysqli_fetch_array($nilai);
        $nilai_asli = $n['nilai'] ?? 0;
        $max = mysqli_fetch_array(mysqli_query($koneksi, "SELECT MAX(nilai) as max FROM penilaian WHERE id_kriteria='$id_kriteria'"));
        $min = mysqli_fetch_array(mysqli_query($koneksi, "SELECT MIN(nilai) as min FROM penilaian WHERE id_kriteria='$id_kriteria'"));
        if ($atribut == 'benefit') {
            $normalisasi = $nilai_asli / ($max['max'] ?: 1);
        } else {
            $normalisasi = ($min['min'] ?: 1) / ($nilai_asli ?: 1);
        }
        $total += $normalisasi * $bobot;
    }
    $hasil[] = ['nama' => $nama, 'nilai' => $total];
}
usort($hasil, function ($a, $b) { return $b['nilai'] <=> $a['nilai']; });
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Perhitungan SAW</title>
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
      <div class="topbar-title">Perhitungan SAW</div>
      <div class="topbar-right">
        <span class="topbar-user"><?php echo htmlspecialchars($_SESSION['nama']); ?></span>
        <div class="avatar"><?php echo strtoupper(substr($_SESSION['nama'], 0, 1)); ?></div>
      </div>
    </header>
    <main class="content">
      <div class="breadcrumb">
        <a href="index.php">Beranda</a><span class="breadcrumb-sep">/</span>
        <span class="breadcrumb-active">Perhitungan SAW</span>
      </div>
      <div class="page-header">
        <div><div class="page-title">Hasil Perhitungan SAW</div>
        <div class="page-sub">Ranking aparatur terbaik berdasarkan metode Simple Additive Weighting</div></div>
      </div>
      <?php if (!empty($hasil)): ?>
      <div class="winner-banner">
        <div>
          <div class="winner-label">&#127942; Aparatur Terbaik</div>
          <div class="winner-name"><?php echo htmlspecialchars($hasil[0]['nama']); ?></div>
          <div style="font-size:13px; color:var(--green-light); margin-top:4px;">Peringkat 1 &mdash; Nilai tertinggi</div>
        </div>
        <div class="winner-score"><?php echo round($hasil[0]['nilai'], 3); ?></div>
      </div>
      <?php endif; ?>
      <div class="card">
        <div class="card-head">
          <div class="card-head-title">Ranking Aparatur</div>
          <span class="badge badge-green"><?php echo count($hasil); ?> Aparatur</span>
        </div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th style="width:80px; text-align:center">Ranking</th>
                <th>Nama Aparatur</th>
                <th style="width:200px">Nilai SAW</th>
                <th style="width:120px; text-align:center">Keterangan</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($hasil)): ?>
                <tr><td colspan="4"><div class="empty-state"><div class="empty-state-icon">&#127942;</div><div class="empty-state-title">Belum ada data perhitungan</div><div class="empty-state-sub">Pastikan data aparatur, kriteria, dan penilaian sudah diisi</div></div></td></tr>
              <?php else: ?>
                <?php $rank = 1; foreach ($hasil as $h): ?>
                <tr>
                  <td class="text-center">
                    <?php if ($rank==1): ?><span class="rank rank-1">1</span>
                    <?php elseif ($rank==2): ?><span class="rank rank-2">2</span>
                    <?php elseif ($rank==3): ?><span class="rank rank-3">3</span>
                    <?php else: ?><span class="rank rank-n"><?php echo $rank; ?></span>
                    <?php endif; ?>
                  </td>
                  <td style="font-weight:500"><?php echo htmlspecialchars($h['nama']); ?></td>
                  <td>
                    <div class="score-wrap">
                      <div class="score-track"><div class="score-fill" style="width:<?php echo min(round($h['nilai']*100),100); ?>%"></div></div>
                      <span class="score-num"><?php echo round($h['nilai'],3); ?></span>
                    </div>
                  </td>
                  <td class="text-center">
                    <?php if ($rank==1): ?><span class="badge badge-gold">Terbaik</span>
                    <?php else: ?><span class="badge badge-gray">Peringkat <?php echo $rank; ?></span>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php $rank++; endforeach; ?>
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