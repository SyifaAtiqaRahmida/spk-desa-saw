<?php
require_once 'includes/koneksi.php';

$username = 'syifa';
$password = 'password';

$query = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM aparatur WHERE username='$username'"));

echo "<h3>Debug Login Aparatur</h3>";

if ($query) {
    echo "<p style='color:green'>✓ Username ditemukan di database</p>";
    echo "<p>Nama: " . $query['nama'] . "</p>";
    echo "<p>Username: " . $query['username'] . "</p>";
    echo "<p>Password hash di DB: " . $query['password'] . "</p>";
    
    if (password_verify($password, $query['password'])) {
        echo "<p style='color:green'>✓ Password COCOK! Login harusnya berhasil.</p>";
    } else {
        echo "<p style='color:red'>✗ Password TIDAK cocok dengan hash di database.</p>";
        echo "<p>Hash yang ditest: " . password_hash($password, PASSWORD_DEFAULT) . "</p>";
    }
} else {
    echo "<p style='color:red'>✗ Username '$username' tidak ditemukan di database!</p>";
}
?>