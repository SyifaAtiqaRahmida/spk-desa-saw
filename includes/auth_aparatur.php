<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['aparatur'])) {
    header("Location: ../aparatur/login.php");
    exit;
}