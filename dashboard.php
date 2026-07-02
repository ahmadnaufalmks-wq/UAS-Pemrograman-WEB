<?php
session_set_cookie_params(0, "/");
session_start();
include "cek_login.php";
include "koneksi.php";

// Jika session tidak ada
if (
    !isset($_SESSION['login']) ||
    !isset($_SESSION['username'])
) {
    header("Location: login.php");
    exit;
}

// =============================
// HITUNG TOTAL PEMASUKAN, PENGELUARAN, SALDO
// =============================
$total_pemasukan = 0;
$q_pemasukan = mysqli_query(
    $conn,
    "SELECT SUM(jumlah) AS total FROM keuangan WHERE tipe='pemasukan'"
);
if ($q_pemasukan) {
    $r = mysqli_fetch_assoc($q_pemasukan);
    $total_pemasukan = $r['total'] ? $r['total'] : 0;
}

$total_pengeluaran = 0;
$q_pengeluaran = mysqli_query(
    $conn,
    "SELECT SUM(jumlah) AS total FROM keuangan WHERE tipe='pengeluaran'"
);
if ($q_pengeluaran) {
    $r = mysqli_fetch_assoc($q_pengeluaran);
    $total_pengeluaran = $r['total'] ? $r['total'] : 0;
}

$saldo_bersih = $total_pemasukan - $total_pengeluaran;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Masjid Al-Haq BTN CV Dewi</title>
    <link rel="stylesheet" href="desain/desain_dashboard.css">
</head>

<body class="dashboard-page">
    <div class="dashboard-shell">
        <aside class="dashboard-sidebar">
            <div class="brand">
                <div class="brand-icon">🕌</div>
                <div>
                    <h1>Masjid Al-Haq</h1>
                    <p>BTN CV Dewi</p>
                </div>
            </div>

            <nav class="sidebar-nav">
                <a class="active" href="dashboard.php">Dashboard</a>
                <a href="keungan.php">Keuangan</a>
                <a href="laporan.php">Laporan</a>
                <a href="#">Pengaturan</a>
                <a href="logout.php">Logout</a>
            </nav>
        </aside>

        <main class="dashboard-main">
            <header class="dashboard-topbar">
                <div>
                    <p class="eyebrow">Ringkasan Keuangan</p>
                    <h2>Dashboard Total Keuangan</h2>
                </div>
                <div class="topbar-chip">Administrator</div>
            </header>

            <section class="dashboard-overview">
                <div class="overview-card">
                    <p class="card-label">Total Pemasukan</p>
                    <p class="total-value">Rp <?= number_format($total_pemasukan, 0, ",", "."); ?></p>
                    <span class="card-note">Semua pemasukan kas masjid.</span>
                </div>
                <div class="overview-card">
                    <p class="card-label">Total Pengeluaran</p>
                    <p class="total-value">Rp <?= number_format($total_pengeluaran, 0, ",", "."); ?></p>
                    <span class="card-note">Pengeluaran untuk operasional masjid.</span>
                </div>
                <div class="overview-card highlight">
                    <p class="card-label">Total Saldo</p>
                    <p class="total-value">Rp <?= number_format($saldo_bersih, 0, ",", "."); ?></p>
                    <span class="card-note">Sisa dana kas tersedia sekarang.</span>
                </div>
            </section>

            <section class="dashboard-summary">
                <div class="summary-panel">
                    <div class="summary-header">
                        <h3>Jadwal Sholat</h3>
                        <p>Waktu sholat untuk hari ini di Masjid Al-Haq.</p>
                    </div>
                    <div class="summary-row">
                        <span>Subuh</span>
                        <strong>04:30</strong>
                    </div>
                    <div class="summary-row">
                        <span>Dzuhur</span>
                        <strong>12:00</strong>
                    </div>
                    <div class="summary-row">
                        <span>Ashar</span>
                        <strong>15:15</strong>
                    </div>
                    <div class="summary-row">
                        <span>Maghrib</span>
                        <strong>17:45</strong>
                    </div>
                    <div class="summary-row">
                        <span>Isya</span>
                        <strong>19:00</strong>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>

</html>