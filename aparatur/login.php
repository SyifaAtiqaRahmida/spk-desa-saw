<?php
// Session khusus aparatur - terpisah dari session lain
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kalau sudah login sebagai aparatur, langsung ke dashboard
if (isset($_SESSION['aparatur'])) {
    header("Location: dashboard.php");
    exit;
}

// Kalau ada session lain (admin/kepala_desa), JANGAN redirect - biarkan aparatur login sendiri
require_once '../includes/koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    // Cari di tabel user dengan role aparatur
    $query = mysqli_fetch_assoc(mysqli_query($koneksi, "
        SELECT u.*, a.nama as nama_aparatur, a.jabatan
        FROM user u
        JOIN aparatur a ON u.id_aparatur = a.id_aparatur
        WHERE u.username='$username' AND u.role='aparatur'
    "));

    if ($query && password_verify($password, $query['password'])) {
        // Set session aparatur
        $_SESSION['aparatur']         = $query['id_aparatur'];
        $_SESSION['aparatur_nama']    = $query['nama_aparatur'];
        $_SESSION['aparatur_jabatan'] = $query['jabatan'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Aparatur — SPK Desa Tatah Mesjid</title>
  <link rel="stylesheet" href="../style.css">
  <style>body { display:flex; align-items:center; justify-content:center; min-height:100vh; background:var(--gray-100); }</style>
</head>
<body>
<div style="width:100%; max-width:400px; padding:16px;">
  <div style="text-align:center; margin-bottom:28px;">
    <div style="display:inline-flex; align-items:center; justify-content:center; width:56px; height:56px; background:var(--green-dark); border-radius:var(--radius-lg); margin-bottom:14px;">
      <span style="font-size:24px;">&#128100;</span>
    </div>
    <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:var(--gray-500); margin-bottom:4px;">Portal Aparatur Desa</div>
    <div style="font-size:20px; font-weight:700; color:var(--gray-900);">SPK Desa Tatah Mesjid</div>
  </div>
  <div class="card">
    <div class="card-head"><div class="card-head-title">Masuk sebagai Aparatur</div></div>
    <div class="card-body">
      <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
      <?php endif; ?>
      <form method="POST">
        <div class="form-group">
          <label class="form-label">Username</label>
          <input type="text" name="username" class="form-control" placeholder="Masukkan username" required autofocus
                 value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block" style="margin-top:8px;">Masuk</button>
      </form>
      <div style="text-align:center; margin-top:16px; font-size:12px; color:var(--gray-500);">
        <a href="../login.php">Login sebagai Admin / Kepala Desa</a>
      </div>
    </div>
  </div>
  <div style="text-align:center; margin-top:16px; font-size:12px; color:var(--gray-500);">Metode SAW &bull; 2026</div>
</div>
</body>
</html>