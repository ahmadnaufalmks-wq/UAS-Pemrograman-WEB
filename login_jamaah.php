<?php

session_set_cookie_params(0, "/");
session_start();
session_regenerate_id(true);
include "koneksi.php";

if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query(
        $conn,
        "SELECT * FROM jamaah WHERE username='$username'"
    );

    if (mysqli_num_rows($query) > 0) {

        $data = mysqli_fetch_assoc($query);

        if ($password == $data['password']) {

            $_SESSION['login_jamaah'] = true;
            $_SESSION['id_jamaah'] = $data['id'];
            $_SESSION['nama_jamaah'] = $data['nama'];
            $_SESSION['username_jamaah'] = $data['username'];

            setcookie(
                "login_jamaah",
                "true",
                0,
                "/"
            );

            // kembali ke halaman infaq yang dituju sebelumnya (jika ada)
            if (isset($_GET['redirect']) && $_GET['redirect'] == 'infaq') {
                header("Location: infaq.php");
            } else {
                header("Location: landing_page.php");
            }
            exit;

        } else {

            echo "
            <script>
            window.onload=function(){
                tampilError();
            }
            </script>";
        }
    } else {

        echo "
        <script>
        window.onload=function(){
            tampilError();
        }
        </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Jamaah - Masjid Al-Haq BTN CV Dewi</title>
    <link rel="stylesheet" href="desain/desain_login.css">
</head>

<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <div class="mosque-icon">🕌</div>
                <h1>Masjid Al-Haq</h1>
                <p class="location">Login Jamaah</p>
            </div>

            <div id="errorCard" class="error-card">
                <span>❌ Username atau Password salah!</span>
            </div>

            <div class="login-form">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            placeholder="Masukkan username">
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Masukkan password">
                    </div>

                    <button type="submit" name="login" class="btn-login">Masuk</button>
            </div>

            <footer class="login-footer">
                <p class="info-text">Belum punya akun? <a href="daftar_jamaah.php" style="color:var(--hijau-tua); font-weight:700;">Daftar di sini</a></p>
                <p class="info-text"><a href="landing_page.php">← Kembali ke Beranda</a></p>
                <p class="year">© 2026 Masjid Al-Haq BTN CV Dewi</p>
            </footer>
        </div>

        <div class="background-pattern"></div>
    </div>

    <script>
        function tampilError() {

            const card = document.getElementById("errorCard");

            card.style.display = "block";

            setTimeout(function() {

                card.style.display = "none";

            }, 3000);

        }
    </script>

</body>

</html>
