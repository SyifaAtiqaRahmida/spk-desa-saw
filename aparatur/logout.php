<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
unset($_SESSION['aparatur'], $_SESSION['aparatur_nama'], $_SESSION['aparatur_jabatan']);
header("Location: login.php");
exit;