<?php
require_once 'config.php';
$activePage = 'barang';
$pageTitle  = 'Daftar Barang';
$pageDesc   = 'Data barang penyimpanan (Create, Read, Update, Delete)';
require_once 'header.php';

$keyword = clean($conn, $_GET['q'] ?? '');
$kategoriFilter = (int)($_GET['kategori'] ?? 0);

$sql = "SELECT b.*, k.nama_kategori FROM barang b LEFT JOIN kategori k ON k.id = b.kategori_id WHERE 1=1";
if ($keyword !== '') {
    $sql .= " AND (b.nama_barang LIKE '%$keyword%' OR b.kode_barang LIKE '%$keyword%')";
}
if ($kategoriFilter > 0) {
    $sql .= " AND b.kategori_id = $kategoriFilter";
}
$sql .= " ORDER BY b.created_at DESC";
$data = mysqli_query($conn, $sql);

$kategoriList = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori");

if (isset($_GET['msg'])) {
    $msgMap = [
        'added'   => ['success', 'Barang baru berhasil ditambahkan.'],
        'updated' => ['success', 'Data barang berhasil diperbarui.'],
        'deleted' => ['success', 'Barang berhasil dihapus.'],
    ];
    $msg = $msgMap[$_GET['msg']] ?? null;
}
?>

<?php if (!empty($msg)): ?>
<div class="alert-custom alert-<?= $msg[0] ?>-custom">
    <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($msg[1]) ?>
</div>
<?php endif; ?>

<div class="card-box">
    <div class="card-title">
        <h4><i class="fa-solid fa-warehouse"></i> Semua Barang</h4>
        <a href="barang_tambah.php" class="btn-add"><i class="fa-solid fa-plus"></i> Tambah Barang</a>
    </div>

    <form method="GET" style="display:flex;gap:12px;margin-bottom:18px;flex-wrap:wrap;">
        <input type="text" name="q" class="form-control" style="max-width:280px;"
               placeholder="Cari kode / nama barang..." value="<?= htmlspecialchars($keyword) ?>">
        <select name="kategori" class="form-control" style="max-width:220px;">
            <option value="0">Semua Kategori</option>
            <?php mysqli_data_seek($kategoriList, 0); while ($k = mysqli_fetch_assoc($kategoriList)): ?>
                <option value="<?= $k['id'] ?>" <?= $kategoriFilter == $k['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($k['nama_kategori']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit" class="btn-outline"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
        <a href="barang_list.php" class="btn-outline"><i class="fa-solid fa-rotate"></i> Reset</a>
    </form>

    <table class="table-custom">
        <thead>
            <tr>
                <th>Foto</th><th>Kode</th><th>Nama Barang</th><th>Kategori</th>
                <th>Jumlah</th><th>Lokasi</th><th>Kondisi</th><th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($data) === 0): ?>
            <tr><td colspan="8"><div class="empty-state"><i class="fa-solid fa-box-open"></i>Data tidak ditemukan</div></td></tr>
        <?php else: while ($b = mysqli_fetch_assoc($data)):
            $badgeClass = $b['kondisi'] === 'Baik' ? 'badge-baik' : ($b['kondisi'] === 'Rusak Ringan' ? 'badge-ringan' : 'badge-berat');
        ?>
            <tr>
                <td>
                    <?php if ($b['foto'] && file_exists(UPLOAD_DIR . $b['foto'])): ?>
                        <img src="<?= UPLOAD_URL . htmlspecialchars($b['foto']) ?>" class="thumb">
                    <?php else: ?>
                        <div class="thumb-placeholder"><i class="fa-solid fa-image"></i></div>
                    <?php endif; ?>
                </td>
                <td><b><?= htmlspecialchars($b['kode_barang']) ?></b></td>
                <td><?= htmlspecialchars($b['nama_barang']) ?></td>
                <td><?= htmlspecialchars($b['nama_kategori'] ?? '-') ?></td>
                <td><?= (int)$b['jumlah'] ?> <?= htmlspecialchars($b['satuan']) ?></td>
                <td><?= htmlspecialchars($b['lokasi_rak'] ?: '-') ?></td>
                <td><span class="badge-custom <?= $badgeClass ?>"><?= htmlspecialchars($b['kondisi']) ?></span></td>
                <td style="white-space:nowrap;">
                    <a href="barang_detail.php?id=<?= $b['id'] ?>" class="btn-action btn-view" title="Detail"><i class="fa-solid fa-eye"></i></a>
                    <a href="barang_edit.php?id=<?= $b['id'] ?>" class="btn-action btn-edit" title="Edit"><i class="fa-solid fa-pen"></i></a>
                    <?php if ($b['foto']): ?>
                    <a href="download_foto.php?id=<?= $b['id'] ?>" class="btn-action btn-download" title="Download Foto"><i class="fa-solid fa-download"></i></a>
                    <?php endif; ?>
                    <a href="barang_hapus.php?id=<?= $b['id'] ?>" class="btn-action btn-delete" title="Hapus"
                       onclick="return confirm('Yakin ingin menghapus barang \'<?= htmlspecialchars(addslashes($b['nama_barang'])) ?>\'?')">
                       <i class="fa-solid fa-trash"></i>
                    </a>
                </td>
            </tr>
        <?php endwhile; endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'footer.php'; ?>
