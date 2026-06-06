<?php
require_once '../includes/auth.php';
require_once '../includes/koneksi.php';

$active = 'saw';
$hasil  = [];

// Ambil semua aparatur
$aparatur_list = mysqli_query($koneksi, "SELECT * FROM aparatur");

while ($a = mysqli_fetch_array($aparatur_list)) {
    $id_aparatur = $a['id_aparatur'];
    $nama        = $a['nama'];
    $jabatan     = $a['jabatan'];
    $total       = 0;
    $detail      = [];

    // Ambil semua kriteria
    $kriteria_list = mysqli_query($koneksi, "SELECT * FROM kriteria");

    while ($k = mysqli_fetch_array($kriteria_list)) {
        $id_kriteria    = $k['id_kriteria'];
        $bobot          = $k['bobot'];
        $atribut        = $k['atribut'];
        $nama_kriteria  = $k['nama_kriteria'];

        // Cek apakah kriteria ini adalah ketidakhadiran
        if (strtolower($nama_kriteria) == 'ketidakhadiran') {
            // Nilai dari jumlah hari tidak hadir di tabel kehadiran
            $row_nilai = mysqli_fetch_assoc(mysqli_query($koneksi, "
                SELECT COUNT(*) as nilai FROM kehadiran
                WHERE id_aparatur=$id_aparatur
            "));
            $nilai_asli = $row_nilai['nilai'] ?? 0;
            // Kalau tidak ada ketidakhadiran, nilai = 0 (terbaik untuk Cost)
            // Tambah 1 biar tidak ada pembagian 0
            $nilai_asli = $nilai_asli;
        } else {
            // Nilai dari jumlah jobdesk yang DISETUJUI per kriteria
            $row_nilai = mysqli_fetch_assoc(mysqli_query($koneksi, "
                SELECT COUNT(*) as nilai FROM jobdesk
                WHERE id_aparatur=$id_aparatur
                AND id_kriteria=$id_kriteria
                AND status='disetujui'
            "));
            $nilai_asli = $row_nilai['nilai'] ?? 0;
        }

        // Ambil max dan min untuk normalisasi
        if (strtolower($nama_kriteria) == 'ketidakhadiran') {
            $max = mysqli_fetch_assoc(mysqli_query($koneksi, "
                SELECT COUNT(*) as max FROM kehadiran
                GROUP BY id_aparatur
                ORDER BY COUNT(*) DESC
                LIMIT 1
            "));
            $min = mysqli_fetch_assoc(mysqli_query($koneksi, "
                SELECT COUNT(*) as min FROM kehadiran
                GROUP BY id_aparatur
                ORDER BY COUNT(*) ASC
                LIMIT 1
            "));
            $max_val = $max['max'] ?? 0;
            $min_val = $min['min'] ?? 0;
        } else {
            $max = mysqli_fetch_assoc(mysqli_query($koneksi, "
                SELECT COUNT(*) as max FROM jobdesk
                WHERE id_kriteria=$id_kriteria AND status='disetujui'
                GROUP BY id_aparatur
                ORDER BY COUNT(*) DESC
                LIMIT 1
            "));
            $min = mysqli_fetch_assoc(mysqli_query($koneksi, "
                SELECT COUNT(*) as min FROM jobdesk
                WHERE id_kriteria=$id_kriteria AND status='disetujui'
                GROUP BY id_aparatur
                ORDER BY COUNT(*) ASC
                LIMIT 1
            "));
            $max_val = $max['max'] ?? 0;
            $min_val = $min['min'] ?? 0;
        }

        // Normalisasi SAW
        if ($atribut == 'benefit') {
            $normalisasi = $max_val > 0 ? $nilai_asli / $max_val : 0;
        } else {
            // Cost: kalau nilai 0 (tidak pernah tidak hadir) = terbaik = 1
            if ($nilai_asli == 0) {
                $normalisasi = 1;
            } else {
                $normalisasi = $min_val > 0 ? $min_val / $nilai_asli : 0;
            }
        }

        $skor  = round($normalisasi * $bobot, 4);
        $total += $skor;

        $detail[] = [
            'nama'        => $nama_kriteria,
            'atribut'     => $atribut,
            'bobot'       => $bobot,
            'nilai'       => $nilai_asli,
            'normalisasi' => round($normalisasi, 4),
            'skor'        => $skor,
        ];
    }

    $hasil[] = [
        'nama'    => $nama,
        'jabatan' => $jabatan,
        'nilai'   => round($total, 4),
        'detail'  => $detail,
    ];
}

// Urutkan dari terbesar
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
      <div class="topbar-title">Perhitungan SAW</div>
      <div class="topbar-right">
        <span class="topbar-user"><?php echo htmlspecialchars($_SESSION['nama']); ?></span>
        <div class="avatar"><?php echo strtoupper(substr($_SESSION['nama'], 0, 1)); ?></div>
      </div>
    </header>
    <main class="content">
      <div class="breadcrumb">
        <a href="index.php">Beranda</a>
        <span class="breadcrumb-sep">/</span>
        <span class="breadcrumb-active">Perhitungan SAW</span>
      </div>

      <div class="page-header">
        <div>
          <div class="page-title">Hasil Perhitungan SAW</div>
          <div class="page-sub">Nilai dihitung otomatis dari jobdesk yang disetujui & data ketidakhadiran</div>
        </div>
      </div>

      <!-- Info cara hitung -->
      <div class="alert alert-info" style="margin-bottom:20px;">
        &#9432; <strong>Cara perhitungan:</strong>
        Nilai kriteria (Kualitas Kerja, Kerja Sama, Pelayanan) = jumlah jobdesk yang <strong>disetujui</strong> Kepala Desa.
        Nilai Ketidakhadiran = jumlah hari tidak hadir (semakin sedikit semakin bagus).
      </div>

      <!-- Winner Banner -->
      <?php if (!empty($hasil)): ?>
      <div class="winner-banner" style="margin-bottom:24px;">
        <div>
          <div class="winner-label">&#127942; Aparatur Terbaik</div>
          <div class="winner-name"><?php echo htmlspecialchars($hasil[0]['nama']); ?></div>
          <div style="font-size:13px; color:var(--green-light); margin-top:4px;"><?php echo htmlspecialchars($hasil[0]['jabatan']); ?> &mdash; Nilai tertinggi</div>
        </div>
        <div class="winner-score"><?php echo $hasil[0]['nilai']; ?></div>
      </div>
      <?php endif; ?>

      <!-- Tabel Ranking -->
      <div class="card" style="margin-bottom:24px;">
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
                <th>Jabatan</th>
                <th style="width:200px">Nilai SAW</th>
                <th style="width:120px; text-align:center">Keterangan</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($hasil)): ?>
                <tr><td colspan="5"><div class="empty-state"><div class="empty-state-icon">&#127942;</div><div class="empty-state-title">Belum ada data</div><div class="empty-state-sub">Pastikan aparatur sudah punya jobdesk yang disetujui</div></div></td></tr>
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
                  <td><?php echo htmlspecialchars($h['jabatan']); ?></td>
                  <td>
                    <div class="score-wrap">
                      <div class="score-track"><div class="score-fill" style="width:<?php echo min(round($h['nilai']*100),100); ?>%"></div></div>
                      <span class="score-num"><?php echo $h['nilai']; ?></span>
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

      <!-- Detail per aparatur -->
      <div class="page-sub mb-2" style="font-weight:600; color:var(--gray-700);">Detail Perhitungan per Aparatur</div>
      <?php foreach ($hasil as $h): ?>
      <div class="card" style="margin-bottom:16px;">
        <div class="card-head">
          <div class="card-head-title"><?php echo htmlspecialchars($h['nama']); ?> — <?php echo htmlspecialchars($h['jabatan']); ?></div>
          <span class="badge badge-green">Total: <?php echo $h['nilai']; ?></span>
        </div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Kriteria</th>
                <th style="width:80px">Atribut</th>
                <th style="width:80px; text-align:center">Nilai</th>
                <th style="width:80px; text-align:center">Bobot</th>
                <th style="width:110px; text-align:center">Normalisasi</th>
                <th style="width:100px; text-align:center">Skor</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($h['detail'] as $d): ?>
              <tr>
                <td style="font-weight:500"><?php echo htmlspecialchars($d['nama']); ?></td>
                <td>
                  <?php if (strtolower($d['atribut']) == 'benefit'): ?>
                    <span class="badge badge-green">Benefit</span>
                  <?php else: ?>
                    <span class="badge badge-red">Cost</span>
                  <?php endif; ?>
                </td>
                <td class="text-center"><span class="badge badge-gray"><?php echo $d['nilai']; ?></span></td>
                <td class="text-center"><?php echo $d['bobot']; ?></td>
                <td class="text-center"><?php echo $d['normalisasi']; ?></td>
                <td class="text-center"><strong><?php echo $d['skor']; ?></strong></td>
              </tr>
              <?php endforeach; ?>
              <tr style="background:var(--green-pale);">
                <td colspan="5" style="font-weight:700; text-align:right; padding-right:16px;">Total Nilai SAW</td>
                <td class="text-center"><strong style="color:var(--green-dark);"><?php echo $h['nilai']; ?></strong></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <?php endforeach; ?>

    </main>
  </div>
</div>
</body>
</html>