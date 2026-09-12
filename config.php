<?php
/**
 * =========================================================
 *  GUDANGKU - Sistem Informasi Penyimpanan Barang
 *  File   : config.php
 *  Fungsi : Konfigurasi koneksi database & session
 * ---------------------------------------------------------
 *  Identitas Pembuat :
 *  Nama      : Athaya Rakha Khairullah
 *  Kelas     : XI TKJ 4
 *  No. Absen : 03
 *  Server    : Linux / CentOS
 * =========================================================
 */

// ==== Mulai session di paling awal ====
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ==== Konfigurasi Database ====
// Silakan sesuaikan jika konfigurasi MySQL di server CentOS Anda berbeda
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_gudang_barang');

// ==== Konfigurasi Aplikasi ====
define('APP_NAME', 'GUDANGKU');
define('APP_SUBTITLE', 'Sistem Informasi Penyimpanan Barang');
define('APP_AUTHOR', 'Athaya Rakha Khairullah');
define('APP_KELAS', 'XI TKJ 4');
define('APP_ABSEN', '03');
define('APP_OS', 'Linux / CentOS');
define('UPLOAD_DIR', __DIR__ . '/');
define('UPLOAD_URL', '');
define('MAX_UPLOAD_SIZE', 3 * 1024 * 1024); // 3 MB

// ==== Koneksi ke MySQL ====
$conn = @mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die('
    <div style="font-family:Segoe UI,Arial,sans-serif;max-width:650px;margin:60px auto;
        background:#fff3f3;border:1px solid #f5c2c2;padding:25px 30px;border-radius:10px;color:#7a1f1f">
        <h2 style="margin-top:0">Koneksi Database Gagal</h2>
        <p>Pastikan MySQL/MariaDB sudah berjalan dan database <b>' . DB_NAME . '</b> sudah dibuat
        dengan mengimpor file <code>database.sql</code>.</p>
        <p><b>Pesan error:</b> ' . htmlspecialchars(mysqli_connect_error()) . '</p>
    </div>');
}
mysqli_set_charset($conn, 'utf8mb4');

/**
 * Membuat akun admin default secara otomatis (sekali saja)
 * Username : admin
 * Password : admin   (username & password dibuat SAMA sesuai permintaan)
 * Password disimpan dengan hashing password_hash() agar tetap aman.
 */
$cek = mysqli_query($conn, "SELECT id FROM users LIMIT 1");
if ($cek && mysqli_num_rows($cek) === 0) {
    $defaultUser = 'admin';
    $defaultPass = password_hash('admin', PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, nama_lengkap) VALUES (?,?,?)");
    $nama = 'Administrator';
    mysqli_stmt_bind_param($stmt, 'sss', $defaultUser, $defaultPass, $nama);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

/**
 * Fungsi bantu sederhana
 */
function clean($conn, $data) {
    return mysqli_real_escape_string($conn, trim($data));
}

function formatTanggal($tanggal) {
    if (!$tanggal) return '-';
    $bulan = ['01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'Mei','06'=>'Jun',
              '07'=>'Jul','08'=>'Agu','09'=>'Sep','10'=>'Okt','11'=>'Nov','12'=>'Des'];
    $d = date('d', strtotime($tanggal));
    $m = $bulan[date('m', strtotime($tanggal))];
    $y = date('Y', strtotime($tanggal));
    $t = date('H:i', strtotime($tanggal));
    return "$d $m $y, $t";
}
