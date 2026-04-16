<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user'])) {
    header("Location: admin/index.php");
    exit;
}

require_once 'includes/koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username'"));

    if ($query && password_verify($password, $query['password'])) {
        $_SESSION['user']     = $query['id_user'];
        $_SESSION['username'] = $query['username'];
        $_SESSION['nama']     = $query['nama'];
        header("Location: admin/index.php");
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
  <title>Login — SPK Desa Tatah Mesjid</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body { display:flex; align-items:center; justify-content:center; min-height:100vh; background:var(--gray-100); }
  </style>
</head>
<body>

<div style="width:100%; max-width:400px; padding:16px;">
  <div style="text-align:center; margin-bottom:28px;">
    <div style="display:inline-flex; align-items:center; justify-content:center; width:56px; height:56px; background:var(--green-dark); border-radius:var(--radius-lg); margin-bottom:14px;">
      <span style="font-size:24px;">&#127963;</span>
    </div>
    <div style="font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.1em; color:var(--gray-500); margin-bottom:4px;">Sistem Pendukung Keputusan</div>
    <div style="font-size:20px; font-weight:700; color:var(--gray-900);">SPK Desa Tatah Mesjid</div>
  </div>

  <div class="card">
    <div class="card-head">
      <div class="card-head-title">Masuk ke Sistem</div>
    </div>
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
    </div>
  </div>

  <div style="text-align:center; margin-top:16px; font-size:12px; color:var(--gray-500);">
    Metode SAW &bull; 2026
  </div>
</div>

</body>
</html>