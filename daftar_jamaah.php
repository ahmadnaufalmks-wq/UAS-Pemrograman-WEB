<?php
session_start();
include "koneksi.php";

if (isset($_POST['daftar'])) {

    $nama     = $_POST['nama'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $konfirmasi = $_POST['konfirmasi'];
    $no_hp    = $_POST['no_hp'];

    if ($password !== $konfirmasi) {

        $pesan_error = "Konfirmasi password tidak sama!";

    } else {

        // cek username sudah dipakai atau belum
        $cek = mysqli_query(
            $conn,
            "SELECT * FROM jamaah WHERE username='$username'"
        );

        if (mysqli_num_rows($cek) > 0) {

            $pesan_error = "Username sudah digunakan, silakan pilih username lain!";

        } else {

            $query = mysqli_query(
                $conn,
                "INSERT INTO jamaah(nama, username, password, no_hp)
                 VALUES('$nama', '$username', '$password', '$no_hp')"
            );

            if ($query) {

                echo "
                <script>
                    alert('Pendaftaran berhasil, silakan login.');
                    window.location='login_jamaah.php';
                </script>";
                exit;

            } else {

                $pesan_error = "Pendaftaran gagal, silakan coba lagi.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Jamaah - Masjid Al-Haq BTN CV Dewi</title>
    <link rel="stylesheet" href="desain/desain_login.css">
</head>

<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <div class="mosque-icon">🕌</div>
                <h1>Masjid Al-Haq</h1>
                <p class="location">Pendaftaran Akun Jamaah</p>
            </div>

            <?php if (isset($pesan_error)) { ?>
                <div class="error-card" style="display:block;">
                    <span>❌ <?= $pesan_error; ?></span>
                </div>
            <?php } ?>

            <div class="login-form">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="nama">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" placeholder="Masukkan nama lengkap" required>
                    </div>

                    <div class="form-group">
                        <label for="no_hp">No. HP / WhatsApp</label>
                        <input type="text" id="no_hp" name="no_hp" placeholder="Contoh: 08123456789" required>
                    </div>

                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" placeholder="Buat username" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Buat password" required>
                    </div>

                    <div class="form-group">
                        <label for="konfirmasi">Konfirmasi Password</label>
                        <input type="password" id="konfirmasi" name="konfirmasi" placeholder="Ulangi password" required>
                    </div>

                    <button type="submit" name="daftar" class="btn-login">Daftar</button>
            </div>

            <footer class="login-footer">
                <p class="info-text">Sudah punya akun? <a href="login_jamaah.php" style="color:var(--hijau-tua); font-weight:700;">Login di sini</a></p>
                <p class="year">© 2026 Masjid Al-Haq BTN CV Dewi</p>
            </footer>
        </div>

        <div class="background-pattern"></div>
    </div>
</body>

</html>
