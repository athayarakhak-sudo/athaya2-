<?php
require_once 'config.php';
$activePage = 'tambah';
$pageTitle  = 'Tambah Barang';
$pageDesc   = 'Tambahkan data barang baru ke penyimpanan';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode       = clean($conn, $_POST['kode_barang']);
    $nama       = clean($conn, $_POST['nama_barang']);
    $kategoriId = (int)$_POST['kategori_id'];
    $jumlah     = (int)$_POST['jumlah'];
    $satuan     = clean($conn, $_POST['satuan']);
    $lokasi     = clean($conn, $_POST['lokasi_rak']);
    $kondisi    = clean($conn, $_POST['kondisi']);
    $keterangan = clean($conn, $_POST['keterangan']);
    $tanggal    = clean($conn, $_POST['tanggal_masuk']);
    $fotoNama   = null;

    // ==== Proses upload foto (jika ada) ====
    if (!empty($_FILES['foto']['name'])) {
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $allowed)) {
            $error = 'Format foto tidak didukung. Gunakan JPG, JPEG, PNG, atau WEBP.';
        } elseif ($_FILES['foto']['size'] > MAX_UPLOAD_SIZE) {
            $error = 'Ukuran foto maksimal 3 MB.';
        } else {
            $fotoNama = 'foto_' . time() . '_' . rand(100, 999) . '.' . $ext;
            if (!move_uploaded_file($_FILES['foto']['tmp_name'], UPLOAD_DIR . $fotoNama)) {
                $error = 'Gagal mengunggah foto ke server.';
                $fotoNama = null;
            }
        }
    }

    if ($error === '' && ($kode === '' || $nama === '')) {
        $error = 'Kode barang dan nama barang wajib diisi.';
    }

    if ($error === '') {
        $stmt = mysqli_prepare($conn, "INSERT INTO barang
            (kode_barang, nama_barang, kategori_id, jumlah, satuan, lokasi_rak, kondisi, foto, keterangan, tanggal_masuk)
            VALUES (?,?,?,?,?,?,?,?,?,?)");
        mysqli_stmt_bind_param($stmt, 'ssisssssss',
            $kode, $nama, $kategoriId, $jumlah, $satuan, $lokasi, $kondisi, $fotoNama, $keterangan, $tanggal);

        if (mysqli_stmt_execute($stmt)) {
            header('Location: barang_list.php?msg=added');
            exit;
        } else {
            $error = mysqli_error($conn) === '' ? 'Gagal menyimpan data.' :
                (str_contains(mysqli_error($conn), 'Duplicate') ? 'Kode barang sudah digunakan, gunakan kode lain.' : 'Gagal menyimpan data.');
        }
        mysqli_stmt_close($stmt);
    }
}

require_once 'header.php';
$kategoriList = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori");
?>

<?php if ($error): ?>
<div class="alert-custom alert-danger-custom"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card-box">
    <div class="card-title"><h4><i class="fa-solid fa-circle-plus"></i> Form Tambah Barang</h4></div>

    <form method="POST" enctype="multipart/form-data">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
            <div class="form-group">
                <label>Kode Barang *</label>
                <input type="text" name="kode_barang" class="form-control" placeholder="Contoh: BRG-0006" required
                       value="<?= htmlspecialchars($_POST['kode_barang'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Nama Barang *</label>
                <input type="text" name="nama_barang" class="form-control" placeholder="Nama barang" required
                       value="<?= htmlspecialchars($_POST['nama_barang'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Kategori</label>
                <select name="kategori_id" class="form-control">
                    <option value="0">- Pilih Kategori -</option>
                    <?php while ($k = mysqli_fetch_assoc($kategoriList)): ?>
                        <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama_kategori']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Kondisi</label>
                <select name="kondisi" class="form-control">
                    <option value="Baik">Baik</option>
                    <option value="Rusak Ringan">Rusak Ringan</option>
                    <option value="Rusak Berat">Rusak Berat</option>
                </select>
            </div>
            <div class="form-group">
                <label>Jumlah</label>
                <input type="number" min="0" name="jumlah" class="form-control" value="<?= htmlspecialchars($_POST['jumlah'] ?? '0') ?>">
            </div>
            <div class="form-group">
                <label>Satuan</label>
                <input type="text" name="satuan" class="form-control" placeholder="Pcs / Unit / Roll ..." value="<?= htmlspecialchars($_POST['satuan'] ?? 'Pcs') ?>">
            </div>
            <div class="form-group">
                <label>Lokasi / Rak</label>
                <input type="text" name="lokasi_rak" class="form-control" placeholder="Contoh: Rak A1" value="<?= htmlspecialchars($_POST['lokasi_rak'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Tanggal Masuk</label>
                <input type="date" name="tanggal_masuk" class="form-control" value="<?= htmlspecialchars($_POST['tanggal_masuk'] ?? date('Y-m-d')) ?>">
            </div>
        </div>

        <div class="form-group">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control" placeholder="Catatan tambahan..."><?= htmlspecialchars($_POST['keterangan'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label>Foto Barang</label>
            <label for="fotoInput" class="upload-box" style="display:block;">
                <i class="fa-solid fa-cloud-arrow-up"></i>
                Klik untuk memilih foto (JPG, PNG, WEBP - maks 3 MB)
            </label>
            <input type="file" id="fotoInput" name="foto" accept="image/*" style="display:none;" onchange="previewFoto(this)">
            <img id="previewImg" class="preview-img" style="display:none;">
        </div>

        <div style="display:flex;gap:12px;margin-top:10px;">
            <button type="submit" class="btn-primary-custom" style="width:auto;padding:12px 28px;">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Barang
            </button>
            <a href="barang_list.php" class="btn-outline"><i class="fa-solid fa-arrow-left"></i> Batal</a>
        </div>
    </form>
</div>

<script>
function previewFoto(input) {
    const img = document.getElementById('previewImg');
    if (input.files && input.files[0]) {
        img.src = URL.createObjectURL(input.files[0]);
        img.style.display = 'block';
    }
}
</script>

<?php require_once 'footer.php'; ?>
