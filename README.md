# GUDANGKU — Sistem Informasi Penyimpanan Barang

Website dinamis berbasis **PHP native + MySQL** untuk mengelola data barang di
gudang/penyimpanan, lengkap dengan login, CRUD, upload & download foto, serta
halaman laporan.

**Identitas Pembuat**

| Keterangan | Detail |
|---|---|
| Nama       | Athaya Rakha Khairullah |
| Kelas      | XI TKJ 4 |
| No. Absen  | 03 |
| Server     | Linux / CentOS |

---

## ✨ Fitur

- 🔐 Login & Logout (session-based, password di-hash dengan `password_hash`)
- 📦 CRUD data barang (Create, Read, Update, Delete)
- 🖼️ Upload foto barang & Download foto barang
- 🔎 Pencarian & filter berdasarkan kategori
- 📊 Halaman **Home/Dashboard** berisi ringkasan & laporan otomatis
- 🧾 Halaman **Laporan** lengkap yang bisa langsung dicetak / disimpan PDF
- 🎨 Tampilan modern, rapi, dan responsif (sidebar admin panel)
- 🗄️ Struktur database MySQL relasional (`users`, `kategori`, `barang`)

---

## 🗂️ Struktur File

Semua file sengaja dibuat **flat / satu folder saja** (tidak ada subfolder),
supaya mudah diunggah ke shared hosting maupun GitHub.

```
gudangku/
├── config.php            # konfigurasi koneksi database + upload
├── database.sql          # struktur & data awal database
├── style.css             # semua styling tampilan
├── auth.php              # proteksi login (dipakai halaman lain)
├── header.php            # sidebar + topbar (dipakai halaman lain)
├── footer.php            # footer + identitas (dipakai halaman lain)
├── login.php
├── logout.php
├── index.php              # Home / Dashboard (laporan ringkas)
├── laporan.php             # Laporan lengkap (bisa dicetak)
├── barang_list.php          # daftar barang (Read)
├── barang_tambah.php        # tambah barang (Create)
├── barang_edit.php          # edit barang (Update)
├── barang_hapus.php         # hapus barang (Delete)
├── barang_detail.php        # detail barang
├── download_foto.php        # unduh foto barang
├── .gitignore
├── README.md
└── foto_*.jpg / foto_*.png  # foto barang hasil upload (otomatis dibuat di sini)
```

> Karena tidak ada folder `uploads/`, seluruh foto barang yang diunggah akan
> tersimpan langsung di folder utama ini dengan awalan nama file `foto_...`.

---

## ⚙️ Instalasi di Server Linux / CentOS

### 1. Install paket yang dibutuhkan

```bash
# Update sistem
sudo yum update -y

# Install Apache (httpd), PHP, dan modul yang diperlukan
sudo yum install -y httpd php php-mysqlnd php-gd php-mbstring php-json mariadb-server

# Aktifkan & jalankan service
sudo systemctl enable --now httpd
sudo systemctl enable --now mariadb
```

> Untuk CentOS 8/9 gunakan `dnf` sebagai pengganti `yum` bila diperlukan.

### 2. Amankan & buat database MySQL/MariaDB

```bash
sudo mysql_secure_installation
sudo mysql -u root -p
```

Di dalam prompt MySQL, jalankan file `database.sql` (atau import lewat phpMyAdmin):

```bash
mysql -u root -p < database.sql
```

### 3. Salin project ke direktori web

```bash
sudo cp -r gudangku /var/www/html/
sudo chown -R apache:apache /var/www/html/gudangku
sudo chmod -R 775 /var/www/html/gudangku
```

> Folder ini perlu izin tulis (775) untuk keseluruhan direktori karena foto
> yang diunggah disimpan langsung di folder yang sama (tidak ada subfolder
> `uploads/`).

### 4. Atur konfigurasi database

Buka `config.php` dan sesuaikan bila diperlukan:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_gudang_barang');
```

### 5. Konfigurasi SELinux (khusus CentOS/RHEL)

Jika SELinux aktif (default di CentOS), izinkan Apache menulis foto ke folder
project dan mengakses jaringan database:

```bash
sudo setsebool -P httpd_can_network_connect_db 1
sudo chcon -R -t httpd_sys_rw_content_t /var/www/html/gudangku
```

### 6. Buka firewall (jika perlu diakses dari jaringan lain)

```bash
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --reload
```

### 7. Akses Website

Buka browser lalu kunjungi:

```
http://ALAMAT-IP-SERVER/gudangku/login.php
```

**Akun default:**

- Username : `admin`
- Password : `admin`

Akun ini otomatis dibuat oleh `config.php` saat pertama kali website diakses.

---

## 🐙 Cara Upload ke GitHub

```bash
cd gudangku
git init
git add .
git commit -m "Initial commit - Sistem Informasi Penyimpanan Barang (GUDANGKU)"
git branch -M main
git remote add origin https://github.com/USERNAME-ANDA/NAMA-REPO.git
git push -u origin main
```

> File foto hasil upload (berawalan `foto_...`) sudah diabaikan lewat
> `.gitignore` supaya tidak ikut ter-commit ke repository.

---

## 🔒 Catatan Keamanan

- Password disimpan dengan hashing `password_hash()`, bukan plain text.
- Query database menggunakan **prepared statement** untuk mencegah SQL Injection.
- Ekstensi file foto yang diizinkan dibatasi (`jpg`, `jpeg`, `png`, `webp`) dan
  nama file di-generate ulang oleh sistem, bukan memakai nama asli unggahan.
- Setiap halaman inti dilindungi sesi login (`auth.php`).

---

## 📄 Lisensi

Project ini dibuat untuk keperluan tugas sekolah oleh **Athaya Rakha Khairullah**
(Kelas XI TKJ 4, No. Absen 03) dan bebas digunakan/dikembangkan untuk keperluan
pembelajaran.
