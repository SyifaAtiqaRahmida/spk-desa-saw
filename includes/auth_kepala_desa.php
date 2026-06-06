<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['kepala_desa'])) {
    header("Location: ../login.php");
    exit;
}
