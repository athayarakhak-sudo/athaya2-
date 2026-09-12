<?php
require_once 'config.php';
require_once 'auth.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT nama_barang, foto FROM barang WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$row = mysqli_stmt_get_result($stmt)->fetch_assoc();

if (!$row || empty($row['foto']) || !file_exists(UPLOAD_DIR . $row['foto'])) {
    header('Location: barang_list.php');
    exit;
}

$filePath = UPLOAD_DIR . $row['foto'];
$ext = pathinfo($filePath, PATHINFO_EXTENSION);
$safeName = preg_replace('/[^A-Za-z0-9_-]/', '_', $row['nama_barang']);
$downloadName = $safeName . '.' . $ext;

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $downloadName . '"');
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: must-revalidate');
header('Pragma: public');
readfile($filePath);
exit;
