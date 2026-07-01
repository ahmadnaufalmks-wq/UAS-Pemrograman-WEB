<?php
session_start();
include "koneksi.php";

// =============================
// HITUNG RINGKASAN KEUANGAN REAL-TIME
// =============================
$total_pemasukan = 0;
$q_pemasukan = mysqli_query($conn, "SELECT SUM(jumlah) AS total FROM keuangan WHERE tipe='pemasukan'");
if ($q_pemasukan) {
    $r = mysqli_fetch_assoc($q_pemasukan);
    $total_pemasukan = $r['total'] ? $r['total'] : 0;
}

$total_pengeluaran = 0;
$q_pengeluaran = mysqli_query($conn, "SELECT SUM(jumlah) AS total FROM keuangan WHERE tipe='pengeluaran'");
if ($q_pengeluaran) {
    $r = mysqli_fetch_assoc($q_pengeluaran);
    $total_pengeluaran = $r['total'] ? $r['total'] : 0;
}

$total_kas = $total_pemasukan - $total_pengeluaran;

// Transaksi terbaru untuk tabel ringkas (10 terakhir)
$transaksi_terbaru = mysqli_query(
    $conn,
    "SELECT * FROM keuangan ORDER BY tanggal DESC, id DESC LIMIT 10"
);

// Status login jamaah
$sudah_login_jamaah = isset($_SESSION['login_jamaah']) && $_SESSION['login_jamaah'] === true;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masjid Al-Haq BTN CV Dewi</title>
    <link rel="stylesheet" href="desain/desain_landing_page.css">
</head>

<body class="landing-page">
    <header class="site-header">
        <div class="logo">🕌 Masjid Al-Haq</div>
        <nav class="main-nav">
            <a href="landing_page.php" class="active">Beranda</a>
            <a href="#keuangan">Keuangan</a>
            <a href="#about">Tentang</a>

            <?php if ($sudah_login_jamaah) { ?>
                <span class="nav-user">Halo, <?= $_SESSION['nama_jamaah']; ?></span>
                <a href="infaq.php" class="btn btn-primary btn-nav">Infaq Sekarang</a>
                <a href="logout_jamaah.php" class="btn btn-secondary btn-nav">Logout</a>
            <?php } else { ?>
                <a href="login_jamaah.php" class="btn btn-primary btn-nav">Masuk Jamaah</a>
                <a href="login.php" class="btn-login-admin">Login Admin</a>
            <?php } ?>
        </nav>
    </header>

    <main>
        <section class="hero">
            <div class="hero-content">
                <p class="eyebrow">Masjid & Komunitas</p>
                <h1>Selamat Datang di Masjid Al-Haq BTN CV Dewi</h1>
                <p>Temukan kegiatan ibadah, informasi donasi, dan dukungan komunitas yang hangat untuk jamaah dan keluarga.</p>
                <div class="hero-actions">
                    <a href="#keuangan" class="btn btn-primary">Lihat Keuangan</a>
                    <?php if ($sudah_login_jamaah) { ?>
                        <a href="infaq.php" class="btn btn-secondary">Infaq Sekarang</a>
                    <?php } else { ?>
                        <a href="login_jamaah.php" class="btn btn-secondary">Infaq Sekarang</a>
                    <?php } ?>
                </div>
            </div>
            <div class="hero-media">
                <img src="masjid_alhaq2.png" alt="Masjid Al-Haq BTN CV Dewi">
            </div>
        </section>

        <section id="keuangan" class="keuangan-summary">
            <div class="section-intro">
                <p class="section-eyebrow">Ringkasan Keuangan</p>
                <h2>Keuangan Masjid Sekarang</h2>
                <p>Data ringkas pemasukan dan pengeluaran saat ini, lengkap dengan total kas yang tersedia untuk operasional dan kegiatan.</p>
            </div>
            <div class="summary-grid">
                <article class="summary-card">
                    <p class="summary-label">Total Pemasukan</p>
                    <p class="summary-value">Rp <?= number_format($total_pemasukan, 0, ",", "."); ?></p>
                </article>
                <article class="summary-card">
                    <p class="summary-label">Total Pengeluaran</p>
                    <p class="summary-value">Rp <?= number_format($total_pengeluaran, 0, ",", "."); ?></p>
                </article>
                <article class="summary-card highlight">
                    <p class="summary-label">Total Kas</p>
                    <p class="summary-value">Rp <?= number_format($total_kas, 0, ",", "."); ?></p>
                </article>
            </div>
            <div class="table-summary">
                <h3>Transaksi Terbaru</h3>
                <table class="summary-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Tipe</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $ada_transaksi = false;
                        while ($row = mysqli_fetch_assoc($transaksi_terbaru)) {
                            $ada_transaksi = true;
                        ?>
                            <tr>
                                <td><?= $row['tanggal']; ?></td>
                                <td><?= $row['jenis']; ?></td>
                                <td><?= ucfirst($row['tipe']); ?></td>
                                <td>Rp <?= number_format($row['jumlah'], 0, ",", "."); ?></td>
                            </tr>
                        <?php
                        }
                        if (!$ada_transaksi) {
                        ?>
                            <tr>
                                <td colspan="4" style="text-align:center; padding:16px; color:#888;">Belum ada transaksi.</td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section id="about" class="about-section">
            <div class="section-intro">
                <p class="section-eyebrow">Tentang Masjid</p>
                <h2>Masjid yang Menjadi Pusat Ibadah dan Kebersamaan</h2>
                <p>Masjid Al-Haq BTN CV Dewi hadir sebagai ruang ibadah, pendidikan, dan sosial untuk warga sekitar. Kami menyelenggarakan sholat berjamaah, kajian, dan kegiatan sosial yang mempererat silaturahmi.</p>
            </div>
            <div class="feature-grid">
                <article class="feature-card">
                    <h3>Sholat Berjamaah</h3>
                    <p>Jadwal sholat yang teratur dengan kenyamanan bagi jamaah seluruh usia.</p>
                </article>
                <article class="feature-card">
                    <h3>Kajian Mingguan</h3>
                    <p>Belajar bersama dalam kajian keagamaan dengan tema yang mudah dipahami.</p>
                </article>
                <article class="feature-card">
                    <h3>Dana Sosial</h3>
                    <p>Mendukung program wakaf, sedekah, dan bantuan untuk warga yang membutuhkan.</p>
                </article>
            </div>
        </section>
    </main>
</body>

</html>
