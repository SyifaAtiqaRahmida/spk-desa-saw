<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
unset($_SESSION['kepala_desa'], $_SESSION['kepala_desa_nama']);
header("Location: ../login.php");
exit;
