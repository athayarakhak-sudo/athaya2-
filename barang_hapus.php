<?php
require_once 'config.php';
require_once 'auth.php';

$id = (int)($_GET['id'] ?? 0);

$stmt = mysqli_prepare($conn, "SELECT foto FROM barang WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$row = mysqli_stmt_get_result($stmt)->fetch_assoc();

if ($row) {
    // Hapus file foto dari server jika ada
    if (!empty($row['foto']) && file_exists(UPLOAD_DIR . $row['foto'])) {
        @unlink(UPLOAD_DIR . $row['foto']);
    }
    $del = mysqli_prepare($conn, "DELETE FROM barang WHERE id = ?");
    mysqli_stmt_bind_param($del, 'i', $id);
    mysqli_stmt_execute($del);
}

header('Location: barang_list.php?msg=deleted');
exit;
