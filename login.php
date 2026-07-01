<?php

session_start();
include "koneksi.php";

if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query(
        $conn,
        "SELECT * FROM admin WHERE username='$username'"
    );

    if (mysqli_num_rows($query) > 0) {

        $data = mysqli_fetch_assoc($query);

        if ($password == $data['password']) {

            $_SESSION['login'] = true;
            $_SESSION['username'] = $data['username'];

            setcookie(
                "login",
                "true",
                time() + 300,
                "/"
            );

            header("Location: dashboard.php");
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
    <title>Login Admin - Masjid Al-Haq BTN CV Dewi</title>
    <link rel="stylesheet" href="desain/desain_login.css">
</head>



<body>
    <div class="login-container">
        <div class="login-box">
            <!-- Logo/Header -->
            <div class="login-header">
                <div class="mosque-icon">🕌</div>
                <h1>Masjid Al-Haq</h1>
                <p class="location">BTN CV Dewi</p>
            </div>

            <!-- Card Error -->
            <div id="errorCard" class="error-card">
                <span>❌ Username atau Password salah!</span>
            </div>

            <!-- Form Login -->
            <div class="login-form">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username">Username Admin</label>
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

            <!-- Footer -->
            <footer class="login-footer">
                <p class="info-text">Halaman Login Administrator</p>
                <p class="year">© 2026 Masjid Al-Haq BTN CV Dewi</p>
            </footer>
        </div>

        <!-- Background Pattern -->
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