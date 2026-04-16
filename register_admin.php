<?php
// FILE INI HANYA DIPAKAI SEKALI untuk membuat akun admin
// Setelah berhasil, HAPUS file ini dari folder!

require_once 'koneksi.php';

$pesan = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $cek = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username'"));

    if ($cek) {
        $pesan = 'error:Username sudah dipakai!';
    } else {
        mysqli_query($koneksi, "INSERT INTO user (nama, username, password) VALUES ('$nama', '$username', '$password')");
        $pesan = 'sukses:Akun berhasil dibuat! Silakan login.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Buat Akun Admin</title>
  <link rel="stylesheet" href="style.css">
  <style>body { display:flex; align-items:center; justify-content:center; min-height:100vh; background:var(--gray-100); }</style>
</head>
<body>
<div style="width:100%; max-width:400px; padding:16px;">
  <div style="text-align:center; margin-bottom:24px;">
    <div style="font-size:18px; font-weight:700; color:var(--gray-900);">Buat Akun Admin</div>
    <div style="font-size:12px; color:var(--gray-500); margin-top:4px;">Gunakan file ini hanya sekali, lalu hapus!</div>
  </div>

  <?php if ($pesan): ?>
    <?php list($tipe, $msg) = explode(':', $pesan, 2); ?>
    <div class="alert <?php echo $tipe == 'sukses' ? 'alert-success' : 'alert-danger'; ?>"><?php echo $msg; ?></div>
  <?php endif; ?>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="">
        <div class="form-group">
          <label class="form-label">Nama Lengkap</label>
          <input type="text" name="nama" class="form-control" placeholder="Contoh: Kepala Desa" required>
        </div>
        <div class="form-group">
          <label class="form-label">Username</label>
          <input type="text" name="username" class="form-control" placeholder="Contoh: admin" required>
        </div>
        <div class="form-group">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" placeholder="Buat password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Buat Akun</button>
      </form>
    </div>
  </div>
</div>
</body>
</html>