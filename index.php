<?php
require_once 'config.php';
$activePage = 'home';
$pageTitle  = 'Home / Dashboard';
$pageDesc   = 'Ringkasan & laporan data penyimpanan barang';
require_once 'header.php';

// ==== Statistik utama ====
$totalBarang   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM barang"))['c'];
$totalUnit     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(jumlah),0) c FROM barang"))['c'];
$totalKategori = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM kategori"))['c'];
$totalRusak    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM barang WHERE kondisi != 'Baik'"))['c'];

// ==== Laporan per kategori ====
$perKategori = mysqli_query($conn, "
    SELECT k.nama_kategori, COUNT(b.id) jumlah_item, COALESCE(SUM(b.jumlah),0) total_unit
    FROM kategori k
    LEFT JOIN barang b ON b.kategori_id = k.id
    GROUP BY k.id, k.nama_kategori
    ORDER BY k.nama_kategori
");

// ==== Laporan kondisi barang ====
$perKondisi = mysqli_query($conn, "
    SELECT kondisi, COUNT(*) jumlah FROM barang GROUP BY kondisi
");
$kondisiMap = ['Baik'=>0, 'Rusak Ringan'=>0, 'Rusak Berat'=>0];
while ($row = mysqli_fetch_assoc($perKondisi)) { $kondisiMap[$row['kondisi']] = $row['jumlah']; }

// ==== Barang terbaru ====
$terbaru = mysqli_query($conn, "
    SELECT b.*, k.nama_kategori FROM barang b
    LEFT JOIN kategori k ON k.id = b.kategori_id
    ORDER BY b.created_at DESC LIMIT 6
");
?>

<div class="stat-grid">
    <div class="stat-card">
        <div class="icon bg-blue"><i class="fa-solid fa-boxes-stacked"></i></div>
        <div><h3><?= (int)$totalBarang ?></h3><p>Jenis Barang Tercatat</p></div>
    </div>
    <div class="stat-card">
        <div class="icon bg-green"><i class="fa-solid fa-cubes"></i></div>
        <div><h3><?= (int)$totalUnit ?></h3><p>Total Unit di Penyimpanan</p></div>
    </div>
    <div class="stat-card">
        <div class="icon bg-orange"><i class="fa-solid fa-layer-group"></i></div>
        <div><h3><?= (int)$totalKategori ?></h3><p>Kategori Barang</p></div>
    </div>
    <div class="stat-card">
        <div class="icon bg-red"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <div><h3><?= (int)$totalRusak ?></h3><p>Barang Perlu Perhatian</p></div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1.4fr 1fr;gap:22px;">

    <!-- Laporan per kategori -->
    <div class="card-box">
        <div class="card-title">
            <h4><i class="fa-solid fa-chart-column"></i> Laporan Barang per Kategori</h4>
            <a href="laporan.php" class="btn-outline"><i class="fa-solid fa-file-lines"></i> Laporan Lengkap</a>
        </div>
        <table class="table-custom">
            <thead>
                <tr><th>Kategori</th><th>Jenis Barang</th><th>Total Unit</th></tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($perKategori) === 0): ?>
                    <tr><td colspan="3" style="text-align:center;color:#94a3b8;">Belum ada data kategori</td></tr>
                <?php else: while ($k = mysqli_fetch_assoc($perKategori)): ?>
                    <tr>
                        <td><b><?= htmlspecialchars($k['nama_kategori']) ?></b></td>
                        <td><?= (int)$k['jumlah_item'] ?> jenis</td>
                        <td><?= (int)$k['total_unit'] ?> unit</td>
                    </tr>
                <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Laporan kondisi -->
    <div class="card-box">
        <div class="card-title"><h4><i class="fa-solid fa-heart-pulse"></i> Kondisi Barang</h4></div>

        <div style="display:flex;flex-direction:column;gap:14px;">
            <div>
                <div style="display:flex;justify-content:space-between;font-size:13.5px;margin-bottom:6px;">
                    <span><span class="badge-custom badge-baik">Baik</span></span><b><?= $kondisiMap['Baik'] ?></b>
                </div>
            </div>
            <div>
                <div style="display:flex;justify-content:space-between;font-size:13.5px;margin-bottom:6px;">
                    <span><span class="badge-custom badge-ringan">Rusak Ringan</span></span><b><?= $kondisiMap['Rusak Ringan'] ?></b>
                </div>
            </div>
            <div>
                <div style="display:flex;justify-content:space-between;font-size:13.5px;margin-bottom:6px;">
                    <span><span class="badge-custom badge-berat">Rusak Berat</span></span><b><?= $kondisiMap['Rusak Berat'] ?></b>
                </div>
            </div>
        </div>

        <hr style="border:none;border-top:1px solid #eef2f7;margin:18px 0;">
        <p style="font-size:12.5px;color:#64748b;line-height:1.7;margin:0;">
            Data ini dihitung otomatis dari seluruh barang yang tersimpan di database
            <b><?= DB_NAME ?></b> pada server <b><?= APP_OS ?></b>.
        </p>
    </div>
</div>

<!-- Barang terbaru -->
<div class="card-box">
    <div class="card-title">
        <h4><i class="fa-solid fa-clock-rotate-left"></i> Barang Terbaru Ditambahkan</h4>
        <a href="barang_tambah.php" class="btn-add"><i class="fa-solid fa-plus"></i> Tambah Barang</a>
    </div>
    <table class="table-custom">
        <thead>
            <tr><th>Foto</th><th>Kode</th><th>Nama Barang</th><th>Kategori</th><th>Jumlah</th><th>Kondisi</th><th>Aksi</th></tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($terbaru) === 0): ?>
            <tr><td colspan="7"><div class="empty-state"><i class="fa-solid fa-box-open"></i>Belum ada data barang</div></td></tr>
        <?php else: while ($b = mysqli_fetch_assoc($terbaru)):
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
                <td><span class="badge-custom <?= $badgeClass ?>"><?= htmlspecialchars($b['kondisi']) ?></span></td>
                <td>
                    <a href="barang_detail.php?id=<?= $b['id'] ?>" class="btn-action btn-view" title="Detail"><i class="fa-solid fa-eye"></i></a>
                </td>
            </tr>
        <?php endwhile; endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'footer.php'; ?>
