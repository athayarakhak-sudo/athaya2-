<?php
// Pastikan user sudah login sebelum mengakses halaman
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
