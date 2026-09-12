<?php
require_once 'config.php';
$activePage = 'barang';
$pageTitle  = 'Edit Barang';
$pageDesc   = 'Perbarui data barang penyimpanan';

$id = (int)($_GET['id'] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT * FROM barang WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$barang = mysqli_stmt_get_result($stmt)->fetch_assoc();

if (!$barang) {
    header('Location: barang_list.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode       = clean($conn, $_POST['kode_barang']);
    $nama       = clean($conn, $_POST['nama_barang']);
    $kategoriIdRaw = (int)$_POST['kategori_id'];
    $kategoriId = $kategoriIdRaw > 0 ? $kategoriIdRaw : null; // 0 = tidak dipilih -> harus NULL, bukan 0
    $jumlah     = (int)$_POST['jumlah'];
    $satuan     = clean($conn, $_POST['satuan']);
    $lokasi     = clean($conn, $_POST['lokasi_rak']);
    $kondisi    = clean($conn, $_POST['kondisi']);
    $keterangan = clean($conn, $_POST['keterangan']);
    $tanggal    = clean($conn, $_POST['tanggal_masuk']);
    $fotoNama   = $barang['foto'];

    if (!empty($_FILES['foto']['name'])) {

        $uploadErrorMessages = [
            UPLOAD_ERR_INI_SIZE   => 'Ukuran foto melebihi batas upload_max_filesize di server (php.ini).',
            UPLOAD_ERR_FORM_SIZE  => 'Ukuran foto melebihi batas MAX_FILE_SIZE pada form.',
            UPLOAD_ERR_PARTIAL    => 'Foto hanya terunggah sebagian. Coba upload ulang.',
            UPLOAD_ERR_NO_FILE    => 'Tidak ada file yang terunggah.',
            UPLOAD_ERR_NO_TMP_DIR => 'Folder temporary (upload_tmp_dir) tidak ditemukan di server.',
            UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke folder temporary di server.',
            UPLOAD_ERR_EXTENSION  => 'Upload dihentikan oleh salah satu ekstensi PHP di server.',
        ];

        if ($_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            $error = $uploadErrorMessages[$_FILES['foto']['error']] ?? 'Terjadi kesalahan saat mengunggah foto (kode: ' . $_FILES['foto']['error'] . ').';
        } else {
            $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];

            if (!in_array($ext, $allowed)) {
                $error = 'Format foto tidak didukung. Gunakan JPG, JPEG, PNG, atau WEBP.';
            } elseif ($_FILES['foto']['size'] > MAX_UPLOAD_SIZE) {
                $error = 'Ukuran foto maksimal 3 MB.';
            } elseif (!is_writable(UPLOAD_DIR)) {
                $error = 'Folder tujuan (' . UPLOAD_DIR . ') tidak bisa ditulis oleh PHP. Cek permission/SELinux.';
            } else {
                $fotoBaru = 'foto_' . time() . '_' . rand(100, 999) . '.' . $ext;
                if (move_uploaded_file($_FILES['foto']['tmp_name'], UPLOAD_DIR . $fotoBaru)) {
                    // Hapus foto lama jika ada
                    if ($barang['foto'] && file_exists(UPLOAD_DIR . $barang['foto'])) {
                        @unlink(UPLOAD_DIR . $barang['foto']);
                    }
                    $fotoNama = $fotoBaru;
                } else {
                    $lastErr = error_get_last();
                    $error = 'Gagal mengunggah foto baru. Detail: ' . ($lastErr['message'] ?? 'tidak diketahui');
                }
            }
        }
    }

    if ($error === '' && ($kode === '' || $nama === '')) {
        $error = 'Kode barang dan nama barang wajib diisi.';
    }

    if ($error === '') {
        $stmt2 = mysqli_prepare($conn, "UPDATE barang SET
            kode_barang=?, nama_barang=?, kategori_id=?, jumlah=?, satuan=?, lokasi_rak=?,
            kondisi=?, foto=?, keterangan=?, tanggal_masuk=? WHERE id=?");
        mysqli_stmt_bind_param($stmt2, 'ssisssssssi',
            $kode, $nama, $kategoriId, $jumlah, $satuan, $lokasi, $kondisi, $fotoNama, $keterangan, $tanggal, $id);

        if (mysqli_stmt_execute($stmt2)) {
            header('Location: barang_list.php?msg=updated');
            exit;
        } else {
            $dbErr = mysqli_error($conn);
            if (str_contains($dbErr, 'Duplicate')) {
                $error = 'Kode barang sudah digunakan oleh barang lain.';
            } elseif ($dbErr !== '') {
                $error = 'Gagal memperbarui data. Detail: ' . $dbErr;
            } else {
                $error = 'Gagal memperbarui data.';
            }
        }
        mysqli_stmt_close($stmt2);
    }
    // Refresh data barang agar form menampilkan input POST terakhir jika gagal
    $barang = array_merge($barang, [
        'kode_barang'=>$kode,'nama_barang'=>$nama,'kategori_id'=>$kategoriId,'jumlah'=>$jumlah,
        'satuan'=>$satuan,'lokasi_rak'=>$lokasi,'kondisi'=>$kondisi,'keterangan'=>$keterangan,'tanggal_masuk'=>$tanggal
    ]);
}

require_once 'header.php';
$kategoriList = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama_kategori");
?>

<?php if ($error): ?>
<div class="alert-custom alert-danger-custom"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="card-box">
    <div class="card-title"><h4><i class="fa-solid fa-pen"></i> Form Edit Barang - <?= htmlspecialchars($barang['nama_barang']) ?></h4></div>

    <form method="POST" enctype="multipart/form-data">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:18px;">
            <div class="form-group">
                <label>Kode Barang *</label>
                <input type="text" name="kode_barang" class="form-control" required value="<?= htmlspecialchars($barang['kode_barang']) ?>">
            </div>
            <div class="form-group">
                <label>Nama Barang *</label>
                <input type="text" name="nama_barang" class="form-control" required value="<?= htmlspecialchars($barang['nama_barang']) ?>">
            </div>
            <div class="form-group">
                <label>Kategori</label>
                <select name="kategori_id" class="form-control">
                    <option value="0">- Pilih Kategori -</option>
                    <?php mysqli_data_seek($kategoriList,0); while ($k = mysqli_fetch_assoc($kategoriList)): ?>
                        <option value="<?= $k['id'] ?>" <?= $barang['kategori_id'] == $k['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($k['nama_kategori']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Kondisi</label>
                <select name="kondisi" class="form-control">
                    <?php foreach (['Baik','Rusak Ringan','Rusak Berat'] as $opt): ?>
                        <option value="<?= $opt ?>" <?= $barang['kondisi'] === $opt ? 'selected' : '' ?>><?= $opt ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Jumlah</label>
                <input type="number" min="0" name="jumlah" class="form-control" value="<?= (int)$barang['jumlah'] ?>">
            </div>
            <div class="form-group">
                <label>Satuan</label>
                <input type="text" name="satuan" class="form-control" value="<?= htmlspecialchars($barang['satuan']) ?>">
            </div>
            <div class="form-group">
                <label>Lokasi / Rak</label>
                <input type="text" name="lokasi_rak" class="form-control" value="<?= htmlspecialchars($barang['lokasi_rak']) ?>">
            </div>
            <div class="form-group">
                <label>Tanggal Masuk</label>
                <input type="date" name="tanggal_masuk" class="form-control" value="<?= htmlspecialchars($barang['tanggal_masuk']) ?>">
            </div>
        </div>

        <div class="form-group">
            <label>Keterangan</label>
            <textarea name="keterangan" class="form-control"><?= htmlspecialchars($barang['keterangan']) ?></textarea>
        </div>

        <div class="form-group">
            <label>Foto Barang</label><br>
            <?php if ($barang['foto'] && file_exists(UPLOAD_DIR . $barang['foto'])): ?>
                <img src="<?= UPLOAD_URL . htmlspecialchars($barang['foto']) ?>" class="preview-img" style="margin-bottom:12px;display:block;">
            <?php endif; ?>
            <label for="fotoInput" class="upload-box" style="display:block;">
                <i class="fa-solid fa-cloud-arrow-up"></i>
                Klik untuk mengganti foto (kosongkan jika tidak ingin mengubah)
            </label>
            <input type="file" id="fotoInput" name="foto" accept="image/*" style="display:none;" onchange="previewFoto(this)">
            <img id="previewImg" class="preview-img" style="display:none;">
        </div>

        <div style="display:flex;gap:12px;margin-top:10px;">
            <button type="submit" class="btn-primary-custom" style="width:auto;padding:12px 28px;">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
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
