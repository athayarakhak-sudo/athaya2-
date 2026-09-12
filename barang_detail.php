<?php
require_once 'config.php';
$activePage = 'barang';
$pageTitle  = 'Detail Barang';
$pageDesc   = 'Informasi lengkap data barang';

$id = (int)($_GET['id'] ?? 0);
$stmt = mysqli_prepare($conn, "
    SELECT b.*, k.nama_kategori FROM barang b
    LEFT JOIN kategori k ON k.id = b.kategori_id
    WHERE b.id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$b = mysqli_stmt_get_result($stmt)->fetch_assoc();

if (!$b) {
    header('Location: barang_list.php');
    exit;
}

require_once 'header.php';
$badgeClass = $b['kondisi'] === 'Baik' ? 'badge-baik' : ($b['kondisi'] === 'Rusak Ringan' ? 'badge-ringan' : 'badge-berat');
?>

<div class="card-box">
    <div class="card-title">
        <h4><i class="fa-solid fa-circle-info"></i> Detail Barang</h4>
        <div>
            <a href="barang_edit.php?id=<?= $b['id'] ?>" class="btn-outline"><i class="fa-solid fa-pen"></i> Edit</a>
            <?php if ($b['foto']): ?>
                <a href="download_foto.php?id=<?= $b['id'] ?>" class="btn-add"><i class="fa-solid fa-download"></i> Download Foto</a>
            <?php endif; ?>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:320px 1fr;gap:28px;">
        <div>
            <?php if ($b['foto'] && file_exists(UPLOAD_DIR . $b['foto'])): ?>
                <img src="<?= UPLOAD_URL . htmlspecialchars($b['foto']) ?>" class="detail-photo">
            <?php else: ?>
                <div class="thumb-placeholder" style="width:100%;height:220px;border-radius:16px;font-size:44px;">
                    <i class="fa-solid fa-image"></i>
                </div>
            <?php endif; ?>
        </div>

        <div>
            <h2 style="margin:0 0 4px;"><?= htmlspecialchars($b['nama_barang']) ?></h2>
            <p style="color:#64748b;margin:0 0 16px;">Kode: <b><?= htmlspecialchars($b['kode_barang']) ?></b></p>

            <span class="badge-custom <?= $badgeClass ?>"><?= htmlspecialchars($b['kondisi']) ?></span>
            <span class="badge-soft" style="margin-left:6px;"><?= htmlspecialchars($b['nama_kategori'] ?? 'Tanpa Kategori') ?></span>

            <table class="table-custom" style="margin-top:20px;">
                <tr><td style="width:180px;color:#64748b;">Jumlah</td><td><b><?= (int)$b['jumlah'] ?> <?= htmlspecialchars($b['satuan']) ?></b></td></tr>
                <tr><td style="color:#64748b;">Lokasi / Rak</td><td><?= htmlspecialchars($b['lokasi_rak'] ?: '-') ?></td></tr>
                <tr><td style="color:#64748b;">Tanggal Masuk</td><td><?= $b['tanggal_masuk'] ? date('d-m-Y', strtotime($b['tanggal_masuk'])) : '-' ?></td></tr>
                <tr><td style="color:#64748b;">Ditambahkan</td><td><?= formatTanggal($b['created_at']) ?></td></tr>
                <tr><td style="color:#64748b;">Terakhir Diubah</td><td><?= formatTanggal($b['updated_at']) ?></td></tr>
                <tr><td style="color:#64748b;vertical-align:top;">Keterangan</td><td><?= nl2br(htmlspecialchars($b['keterangan'] ?: '-')) ?></td></tr>
            </table>
        </div>
    </div>
</div>

<a href="barang_list.php" class="btn-outline"><i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Barang</a>

<?php require_once 'footer.php'; ?>
