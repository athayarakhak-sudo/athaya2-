<?php
require_once 'config.php';

// Jika sudah login, langsung arahkan ke halaman utama
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean($conn, $_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, username, password, nama_lengkap FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id']  = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['nama']     = $row['nama_lengkap'];
                header('Location: index.php');
                exit;
            } else {
                $error = 'Password yang Anda masukkan salah.';
            }
        } else {
            $error = 'Username tidak ditemukan.';
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login - <?= APP_NAME ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="login-wrapper">
  <div class="login-card">

    <div class="login-side">
        <div class="box-icon"><i class="fa-solid fa-boxes-stacked"></i></div>
        <h2><?= APP_NAME ?></h2>
        <p><?= APP_SUBTITLE ?>. Kelola data barang, foto, dan laporan penyimpanan secara rapi, cepat, dan aman langsung dari browser.</p>

        <div class="identity">
            <div><b>Nama</b> : <?= htmlspecialchars(APP_AUTHOR) ?></div>
            <div><b>Kelas</b> : <?= htmlspecialchars(APP_KELAS) ?></div>
            <div><b>No. Absen</b> : <?= htmlspecialchars(APP_ABSEN) ?></div>
            <div><b>Server</b> : <?= htmlspecialchars(APP_OS) ?></div>
        </div>
    </div>

    <div class="login-form-side">
        <h3>Selamat Datang 👋</h3>
        <p class="sub">Silakan masuk untuk mengakses sistem penyimpanan barang.</p>

        <?php if ($error): ?>
            <div class="alert-custom alert-danger-custom">
                <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
            <label style="font-size:13px;font-weight:600;color:#334155;">Username</label>
            <input type="text" name="username" class="form-control-custom" placeholder="Masukkan username" required autofocus>

            <label style="font-size:13px;font-weight:600;color:#334155;">Password</label>
            <input type="password" name="password" class="form-control-custom" placeholder="Masukkan password" required>

            <button type="submit" class="btn-primary-custom">
                <i class="fa-solid fa-right-to-bracket"></i> Masuk
            </button>
        </form>

        <div class="hint-box">
            <i class="fa-solid fa-circle-info"></i>
            Akun default: <b>admin</b> / <b>admin</b> (username dan password sama).
        </div>
    </div>

  </div>
</div>

</body>
</html>
