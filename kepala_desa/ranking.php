<?php
require_once '../includes/auth_kepala_desa.php';
require_once '../includes/koneksi.php';

$active = 'ranking';

// Proses simpan urutan manual
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['urutan'])) {
    foreach ($_POST['urutan'] as $id_aparatur => $urutan) {
        $id_aparatur = (int) $id_aparatur;
        if ($urutan !== '') {
            $urutan = (int) $urutan;
            mysqli_query($koneksi, "UPDATE aparatur SET urutan_manual=$urutan WHERE id_aparatur=$id_aparatur");
        } else {
            mysqli_query($koneksi, "UPDATE aparatur SET urutan_manual=NULL WHERE id_aparatur=$id_aparatur");
        }
    }
    header("Location: ranking.php?pesan=urutan_disimpan"); exit;
}

// Reset urutan manual
if (isset($_GET['reset'])) {
    mysqli_query($koneksi, "UPDATE aparatur SET urutan_manual=NULL");
    header("Location: ranking.php?pesan=urutan_direset"); exit;
}

// Hitung nilai SAW semua aparatur
$hasil = [];
$aparatur_list = mysqli_query($koneksi, "SELECT * FROM aparatur");

while ($a = mysqli_fetch_array($aparatur_list)) {
    $id_aparatur = $a['id_aparatur'];
    $total       = 0;

    $kriteria_list = mysqli_query($koneksi, "SELECT * FROM kriteria");
    while ($k = mysqli_fetch_array($kriteria_list)) {
        $id_kriteria   = $k['id_kriteria'];
        $bobot         = $k['bobot'];
        $atribut       = $k['atribut'];
        $nama_kriteria = $k['nama_kriteria'];

        if (strtolower($nama_kriteria) == 'ketidakhadiran') {
            $row     = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as nilai FROM kehadiran WHERE id_aparatur=$id_aparatur"));
            $nilai   = $row['nilai'] ?? 0;
            $max     = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as max FROM kehadiran GROUP BY id_aparatur ORDER BY COUNT(*) DESC LIMIT 1"));
            $min     = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as min FROM kehadiran GROUP BY id_aparatur ORDER BY COUNT(*) ASC LIMIT 1"));
            $max_val = $max['max'] ?? 0;
            $min_val = $min['min'] ?? 0;
        } else {
            $row     = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as nilai FROM jobdesk WHERE id_aparatur=$id_aparatur AND id_kriteria=$id_kriteria AND status='disetujui'"));
            $nilai   = $row['nilai'] ?? 0;
            $max     = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as max FROM jobdesk WHERE id_kriteria=$id_kriteria AND status='disetujui' GROUP BY id_aparatur ORDER BY COUNT(*) DESC LIMIT 1"));
            $min     = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as min FROM jobdesk WHERE id_kriteria=$id_kriteria AND status='disetujui' GROUP BY id_aparatur ORDER BY COUNT(*) ASC LIMIT 1"));
            $max_val = $max['max'] ?? 0;
            $min_val = $min['min'] ?? 0;
        }

        if ($atribut == 'benefit') {
            $norm = $max_val > 0 ? $nilai / $max_val : 0;
        } else {
            $norm = $nilai == 0 ? 1 : ($min_val > 0 ? $min_val / $nilai : 0);
        }
        $total += round($norm * $bobot, 4);
    }

    $hasil[] = [
        'id_aparatur'   => $a['id_aparatur'],
        'nama'          => $a['nama'],
        'jabatan'       => $a['jabatan'],
        'nilai'         => round($total, 4),
        'urutan_manual' => $a['urutan_manual'],
    ];
}

// Cari nilai yang kembar - konversi ke string dulu biar bisa pakai array_count_values
$nilai_string = array_map(fn($h) => (string)$h['nilai'], $hasil);
$nilai_count  = array_count_values($nilai_string);
$nilai_kembar = array_keys(array_filter($nilai_count, fn($c) => $c > 1));
$ada_kembar   = count($nilai_kembar) > 0;

// Urutan: kalau ada urutan manual pakai itu, kalau tidak pakai nilai SAW
usort($hasil, function($a, $b) {
    if ($a['urutan_manual'] !== null && $b['urutan_manual'] !== null) {
        return $a['urutan_manual'] <=> $b['urutan_manual'];
    }
    if ($a['urutan_manual'] !== null) return -1;
    if ($b['urutan_manual'] !== null) return 1;
    return $b['nilai'] <=> $a['nilai'];
});

// Pisahkan aparatur yang nilainya kembar
$aparatur_kembar = array_filter($hasil, fn($h) => in_array((string)$h['nilai'], $nilai_kembar));
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hasil Ranking SAW</title>
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
      <div class="topbar-title">Hasil Ranking SAW</div>
      <div class="topbar-right">
        <span class="topbar-user"><?php echo htmlspecialchars($_SESSION['kepala_desa_nama']); ?></span>
        <div class="avatar"><?php echo strtoupper(substr($_SESSION['kepala_desa_nama'], 0, 1)); ?></div>
      </div>
    </header>
    <main class="content">
      <div class="breadcrumb">
        <a href="dashboard.php">Beranda</a>
        <span class="breadcrumb-sep">/</span>
        <span class="breadcrumb-active">Hasil Ranking SAW</span>
      </div>
      <div class="page-header">
        <div>
          <div class="page-title">Hasil Ranking Aparatur</div>
          <div class="page-sub">Ranking aparatur terbaik berdasarkan metode SAW</div>
        </div>
        <?php if (!empty(array_filter(array_column($hasil, 'urutan_manual'), fn($v) => $v !== null))): ?>
          <a href="ranking.php?reset=1" class="btn btn-ghost btn-sm" onclick="return confirm('Reset urutan manual ke otomatis?')">&#8635; Reset Urutan</a>
        <?php endif; ?>
      </div>

      <?php if (isset($_GET['pesan'])): ?>
        <?php if ($_GET['pesan'] == 'urutan_disimpan'): ?><div class="alert alert-success">Urutan berhasil disimpan!</div>
        <?php elseif ($_GET['pesan'] == 'urutan_direset'): ?><div class="alert alert-info">Urutan dikembalikan ke otomatis.</div>
        <?php endif; ?>
      <?php endif; ?>

      <?php if ($ada_kembar): ?>
      <div class="alert alert-warning">
        &#9888; Terdapat aparatur dengan nilai SAW yang sama. Gunakan form <strong>"Atur Urutan"</strong> di bawah untuk menentukan urutan secara manual.
      </div>
      <?php endif; ?>

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

      <div class="card" style="margin-bottom:24px;">
        <div class="card-head">
          <div class="card-head-title">Ranking Semua Aparatur</div>
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
                <tr><td colspan="5"><div class="empty-state"><div class="empty-state-icon">&#127942;</div><div class="empty-state-title">Belum ada data</div></div></td></tr>
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
                  <td style="font-weight:500">
                    <?php echo htmlspecialchars($h['nama']); ?>
                    <?php if ($h['urutan_manual'] !== null): ?>
                      <span class="badge badge-gold" style="font-size:10px; margin-left:6px;">Manual</span>
                    <?php endif; ?>
                  </td>
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

      <!-- Form Atur Urutan — HANYA untuk yang nilainya sama -->
      <?php if ($ada_kembar): ?>
      <div class="card">
        <div class="card-head">
          <div class="card-head-title">&#9998; Atur Urutan Aparatur Nilai Sama</div>
          <span class="badge badge-gold">Perlu Keputusan Manual</span>
        </div>
        <div class="card-body">
          <div class="alert alert-info" style="margin-bottom:16px;">
            Hanya aparatur dengan nilai SAW yang sama ditampilkan di sini. Isi nomor urutan untuk menentukan peringkat. Nomor terkecil = peringkat lebih tinggi.
          </div>
          <form method="POST">
            <?php foreach ($nilai_kembar as $nilai_sama): ?>
              <div style="margin-bottom:20px;">
                <div style="font-size:12px; font-weight:700; color:var(--gray-500); text-transform:uppercase; margin-bottom:10px; padding-bottom:6px; border-bottom:1px solid #f0f0f0;">
                  Nilai SAW Sama: <span style="color:var(--green-dark);"><?php echo $nilai_sama; ?></span>
                </div>
                <?php foreach ($aparatur_kembar as $h):
                  if ((string)$h['nilai'] != $nilai_sama) continue; ?>
                <div style="display:flex; align-items:center; gap:16px; padding:10px 0; border-bottom:1px solid #f9f9f9;">
                  <div style="flex:1;">
                    <div style="font-weight:600;"><?php echo htmlspecialchars($h['nama']); ?></div>
                    <div style="font-size:12px; color:var(--gray-500);"><?php echo htmlspecialchars($h['jabatan']); ?></div>
                  </div>
                  <div>
                    <label style="font-size:11px; color:var(--gray-500); display:block; margin-bottom:4px;">NOMOR URUTAN</label>
                    <input type="number"
                           name="urutan[<?php echo $h['id_aparatur']; ?>]"
                           class="form-control"
                           style="width:80px; text-align:center;"
                           min="1"
                           placeholder="—"
                           value="<?php echo $h['urutan_manual'] ?? ''; ?>">
                  </div>
                </div>
                <?php endforeach; ?>
              </div>
            <?php endforeach; ?>
            <div style="margin-top:16px; padding-top:16px; border-top:1px solid #f0f0f0;">
              <button type="submit" class="btn btn-primary">Simpan Urutan</button>
              <a href="ranking.php?reset=1" class="btn btn-ghost" onclick="return confirm('Reset ke urutan otomatis?')" style="margin-left:8px;">Reset ke Otomatis</a>
            </div>
          </form>
        </div>
      </div>
      <?php endif; ?>

    </main>
  </div>
</div>
</body>
</html>