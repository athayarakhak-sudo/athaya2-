<?php
require_once __DIR__ . '/auth.php';
$activePage = $activePage ?? '';
$pageTitle  = $pageTitle ?? 'Dashboard';
$pageDesc   = $pageDesc ?? '';
function navActive($page, $current) { return $page === $current ? 'active' : ''; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($pageTitle) ?> - <?= APP_NAME ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="app-wrapper">

    <aside class="sidebar no-print">
        <div class="brand">
            <div class="logo-box"><i class="fa-solid fa-boxes-stacked"></i></div>
            <div>
                <h1><?= APP_NAME ?></h1>
                <span><?= APP_SUBTITLE ?></span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-label">Menu Utama</div>
            <a href="index.php" class="<?= navActive('home', $activePage) ?>">
                <i class="fa-solid fa-house"></i> Home / Dashboard
            </a>
            <a href="laporan.php" class="<?= navActive('laporan', $activePage) ?>">
                <i class="fa-solid fa-chart-column"></i> Laporan
            </a>

            <div class="nav-label">Data Barang</div>
            <a href="barang_list.php" class="<?= navActive('barang', $activePage) ?>">
                <i class="fa-solid fa-warehouse"></i> Daftar Barang
            </a>
            <a href="barang_tambah.php" class="<?= navActive('tambah', $activePage) ?>">
                <i class="fa-solid fa-circle-plus"></i> Tambah Barang
            </a>

            <div class="nav-label">Akun</div>
            <a href="logout.php" onclick="return confirm('Yakin ingin keluar?')">
                <i class="fa-solid fa-right-from-bracket"></i> Keluar
            </a>
        </nav>

        <div class="sidebar-footer">
            <div><b><?= htmlspecialchars(APP_AUTHOR) ?></b></div>
            <div><?= htmlspecialchars(APP_KELAS) ?> &bull; Absen <?= htmlspecialchars(APP_ABSEN) ?></div>
            <div>Server: <?= htmlspecialchars(APP_OS) ?></div>
        </div>
    </aside>

    <div class="main-content">
        <div class="topbar no-print">
            <div>
                <h2><?= htmlspecialchars($pageTitle) ?></h2>
                <?php if ($pageDesc): ?><p><?= htmlspecialchars($pageDesc) ?></p><?php endif; ?>
            </div>
            <div class="user-chip">
                <div class="avatar"><?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?></div>
                <span><?= htmlspecialchars($_SESSION['nama'] ?: $_SESSION['username']) ?></span>
                <a href="logout.php" onclick="return confirm('Yakin ingin keluar?')" title="Keluar">
                    <i class="fa-solid fa-power-off"></i>
                </a>
            </div>
        </div>
        <div class="content">
