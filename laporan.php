<?php
require_once 'config.php';
$activePage = 'laporan';
$pageTitle  = 'Laporan Barang';
$pageDesc   = 'Laporan lengkap seluruh data penyimpanan barang';
require_once 'header.php';

$data = mysqli_query($conn, "
    SELECT b.*, k.nama_kategori FROM barang b
    LEFT JOIN kategori k ON k.id = b.kategori_id
    ORDER BY k.nama_kategori, b.nama_barang
");

$totalBarang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM barang"))['c'];
$totalUnit   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(jumlah),0) c FROM barang"))['c'];
?>

<div class="card-box">
    <div class="card-title no-print">
        <h4><i class="fa-solid fa-file-lines"></i> Laporan Data Barang</h4>
        <button onclick="window.print()" class="btn-add"><i class="fa-solid fa-print"></i> Cetak / Simpan PDF</button>
    </div>

    <div style="text-align:center;margin-bottom:22px;">
        <h2 style="margin:0;">LAPORAN DATA PENYIMPANAN BARANG</h2>
        <p style="color:#64748b;margin:4px 0 0;"><?= APP_NAME ?> - <?= APP_SUBTITLE ?></p>
        <p style="color:#64748b;margin:2px 0 0;font-size:13px;">Dicetak pada: <?= date('d-m-Y H:i') ?> WIB</p>
    </div>

    <div style="display:flex;gap:16px;justify-content:center;margin-bottom:24px;">
        <div style="background:#eef2ff;border-radius:12px;padding:14px 26px;text-align:center;">
            <div style="font-size:22px;font-weight:700;color:#1e1b4b;"><?= (int)$totalBarang ?></div>
            <div style="font-size:12px;color:#4338ca;">Jenis Barang</div>
        </div>
        <div style="background:#dcfce7;border-radius:12px;padding:14px 26px;text-align:center;">
            <div style="font-size:22px;font-weight:700;color:#14532d;"><?= (int)$totalUnit ?></div>
            <div style="font-size:12px;color:#166534;">Total Unit</div>
        </div>
    </div>

    <table class="table-custom">
        <thead>
            <tr>
                <th>No</th><th>Kode</th><th>Nama Barang</th><th>Kategori</th>
                <th>Jumlah</th><th>Lokasi</th><th>Kondisi</th><th>Tgl Masuk</th>
            </tr>
        </thead>
        <tbody>
        <?php $no = 1; if (mysqli_num_rows($data) === 0): ?>
            <tr><td colspan="8" style="text-align:center;color:#94a3b8;">Belum ada data</td></tr>
        <?php else: while ($b = mysqli_fetch_assoc($data)):
            $badgeClass = $b['kondisi'] === 'Baik' ? 'badge-baik' : ($b['kondisi'] === 'Rusak Ringan' ? 'badge-ringan' : 'badge-berat');
        ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><b><?= htmlspecialchars($b['kode_barang']) ?></b></td>
                <td><?= htmlspecialchars($b['nama_barang']) ?></td>
                <td><?= htmlspecialchars($b['nama_kategori'] ?? '-') ?></td>
                <td><?= (int)$b['jumlah'] ?> <?= htmlspecialchars($b['satuan']) ?></td>
                <td><?= htmlspecialchars($b['lokasi_rak'] ?: '-') ?></td>
                <td><span class="badge-custom <?= $badgeClass ?>"><?= htmlspecialchars($b['kondisi']) ?></span></td>
                <td><?= $b['tanggal_masuk'] ? date('d-m-Y', strtotime($b['tanggal_masuk'])) : '-' ?></td>
            </tr>
        <?php endwhile; endif; ?>
        </tbody>
    </table>

    <div style="margin-top:40px;display:flex;justify-content:flex-end;">
        <div style="text-align:center;">
            <p style="margin:0 0 60px;">Palembang, <?= date('d F Y') ?><br>Petugas Gudang,</p>
            <p style="margin:0;font-weight:700;text-decoration:underline;"><?= htmlspecialchars(APP_AUTHOR) ?></p>
            <p style="margin:0;font-size:13px;color:#64748b;"><?= htmlspecialchars(APP_KELAS) ?> - No. Absen <?= htmlspecialchars(APP_ABSEN) ?></p>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
