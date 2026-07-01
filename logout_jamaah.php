<?php

session_start();

unset($_SESSION['login_jamaah']);
unset($_SESSION['id_jamaah']);
unset($_SESSION['nama_jamaah']);
unset($_SESSION['username_jamaah']);

setcookie(
    "login_jamaah",
    "",
    time() - 3600,
    "/"
);

header("Location: landing_page.php");
exit;
?>
