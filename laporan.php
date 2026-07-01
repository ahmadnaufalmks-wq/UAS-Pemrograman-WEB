<?php
session_start();
include "cek_login.php";
include "koneksi.php";

// Jika session atau cookie tidak ada
if (
    !isset($_SESSION['login']) ||
    !isset($_SESSION['username']) ||
    !isset($_COOKIE['login'])
) {
    header("Location: login.php");
    exit;
}

// =============================
// TENTUKAN RENTANG MINGGU (Senin - Minggu)
// =============================
if (isset($_GET['awal']) && isset($_GET['akhir']) && $_GET['awal'] != "" && $_GET['akhir'] != "") {
    $tanggal_awal  = $_GET['awal'];
    $tanggal_akhir = $_GET['akhir'];
} else {
    // default: minggu berjalan (Senin s/d Minggu)
    $tanggal_awal  = date('Y-m-d', strtotime('monday this week'));
    $tanggal_akhir = date('Y-m-d', strtotime('sunday this week'));
}

// Laporan hanya ditampilkan setelah tombol "Tampilkan Laporan" ditekan
// (atau ketika datang dari link "Minggu Ini" / "Minggu Lalu")
$tampilkan_laporan = isset($_GET['tampilkan']) || isset($_GET['awal']);

// =============================
// AMBIL DATA TRANSAKSI DALAM RENTANG MINGGU
// =============================
$data_laporan = mysqli_query(
    $conn,
    "SELECT * FROM keuangan
     WHERE tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'
     ORDER BY tanggal ASC, id ASC"
);

// =============================
// HITUNG TOTAL PEMASUKAN, PENGELUARAN, SALDO PADA RENTANG TSB
// =============================
$total_pemasukan = 0;
$q_pemasukan = mysqli_query(
    $conn,
    "SELECT SUM(jumlah) AS total FROM keuangan
     WHERE tipe='pemasukan' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'"
);
if ($q_pemasukan) {
    $r = mysqli_fetch_assoc($q_pemasukan);
    $total_pemasukan = $r['total'] ? $r['total'] : 0;
}

$total_pengeluaran = 0;
$q_pengeluaran = mysqli_query(
    $conn,
    "SELECT SUM(jumlah) AS total FROM keuangan
     WHERE tipe='pengeluaran' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'"
);
if ($q_pengeluaran) {
    $r = mysqli_fetch_assoc($q_pengeluaran);
    $total_pengeluaran = $r['total'] ? $r['total'] : 0;
}

$saldo_bersih = $total_pemasukan - $total_pengeluaran;

// Format tanggal Indonesia untuk kop laporan
$bulan_id = [
    1 => "Januari", 2 => "Februari", 3 => "Maret", 4 => "April",
    5 => "Mei", 6 => "Juni", 7 => "Juli", 8 => "Agustus",
    9 => "September", 10 => "Oktober", 11 => "November", 12 => "Desember"
];

function tgl_indo($tanggal, $bulan_id) {
    $pecah = explode('-', $tanggal);
    return $pecah[2] . " " . $bulan_id[(int)$pecah[1]] . " " . $pecah[0];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - Masjid Al-Haq BTN CV Dewi</title>
    <link rel="stylesheet" href="desain/desain_keungan.css">
    <link rel="stylesheet" href="desain/desain_laporan.css">
</head>

<body class="keuangan-page">
    <div class="keuangan-shell">
        <aside class="keuangan-sidebar no-print">
            <div class="brand">
                <div class="brand-icon">🕌</div>
                <div>
                    <h1>Masjid Al-Haq</h1>
                    <p>BTN CV Dewi</p>
                </div>
            </div>

            <nav class="sidebar-nav">
                <a href="dashboard.php">Dashboard</a>
                <a href="keungan.php">Keuangan</a>
                <a class="active" href="laporan.php">Laporan</a>
                <a href="#">Pengaturan</a>
                <a href="logout.php">Logout</a>
            </nav>
        </aside>

        <main class="keuangan-main">
            <header class="keuangan-topbar no-print">
                <div>
                    <p class="eyebrow">Laporan Mingguan</p>
                    <h2>Laporan Keuangan Masjid</h2>
                </div>
                <div class="topbar-chip">Administrator</div>
            </header>

            <section class="keuangan-form no-print">
                <div class="form-container">
                    <div class="form-header">
                        <h3>Pilih Periode Minggu</h3>
                        <p>Tentukan rentang tanggal laporan yang ingin dicetak</p>
                    </div>
                    <form action="" method="get" class="data-form">
                        <div class="form-group">
                            <label for="awal">Dari Tanggal</label>
                            <input type="date" id="awal" name="awal" value="<?= $tanggal_awal; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="akhir">Sampai Tanggal</label>
                            <input type="date" id="akhir" name="akhir" value="<?= $tanggal_akhir; ?>" required>
                        </div>
                        <button type="submit" name="tampilkan" value="1" class="submit-btn">Tampilkan Laporan</button>
                    </form>
                    <div class="quick-links">
                        <a href="laporan.php?awal=<?= date('Y-m-d', strtotime('monday this week')); ?>&akhir=<?= date('Y-m-d', strtotime('sunday this week')); ?>">Minggu Ini</a>
                        <a href="laporan.php?awal=<?= date('Y-m-d', strtotime('monday last week')); ?>&akhir=<?= date('Y-m-d', strtotime('sunday last week')); ?>">Minggu Lalu</a>
                        <?php if ($tampilkan_laporan) { ?>
                            <button type="button" class="btn-cetak" onclick="window.print();">🖨️ Cetak Laporan</button>
                        <?php } ?>
                    </div>
                </div>
            </section>

            <?php if (!$tampilkan_laporan) { ?>

                <section class="laporan-kosong no-print">
                    <div class="table-container laporan-empty-state">
                        <div class="empty-icon">📄</div>
                        <h3>Belum Ada Laporan Ditampilkan</h3>
                        <p>Silakan pilih rentang tanggal di atas, lalu tekan tombol <strong>"Tampilkan Laporan"</strong> untuk melihat data keuangan masjid pada periode tersebut.</p>
                    </div>
                </section>

            <?php } else { ?>

            <section class="laporan-cetak">
                <div class="laporan-kop">
                    <h2>Laporan Keuangan Masjid Al-Haq BTN CV Dewi</h2>
                    <p>Periode: <?= tgl_indo($tanggal_awal, $bulan_id); ?> &ndash; <?= tgl_indo($tanggal_akhir, $bulan_id); ?></p>
                </div>

                <section class="keuangan-overview">
                    <div class="overview-card">
                        <p class="card-label">Total Pemasukan</p>
                        <p class="total-value">Rp <?= number_format($total_pemasukan, 0, ",", "."); ?></p>
                        <span class="card-note">Pemasukan pada periode ini.</span>
                    </div>
                    <div class="overview-card">
                        <p class="card-label">Total Pengeluaran</p>
                        <p class="total-value">Rp <?= number_format($total_pengeluaran, 0, ",", "."); ?></p>
                        <span class="card-note">Pengeluaran pada periode ini.</span>
                    </div>
                    <div class="overview-card highlight">
                        <p class="card-label">Saldo Bersih</p>
                        <p class="total-value">Rp <?= number_format($saldo_bersih, 0, ",", "."); ?></p>
                        <span class="card-note">Selisih pemasukan &amp; pengeluaran periode ini.</span>
                    </div>
                </section>

                <div class="table-container">
                    <div class="table-header">
                        <h3>Rincian Transaksi</h3>
                        <p>Seluruh transaksi pemasukan dan pengeluaran pada periode terpilih</p>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Tanggal</th>
                                <th>Tipe</th>
                                <th>Jenis / Deskripsi</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $ada_data = false;

                            while ($row = mysqli_fetch_assoc($data_laporan)) {
                                $ada_data = true;
                            ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= $row['tanggal']; ?></td>
                                    <td>
                                        <span class="badge <?= $row['tipe'] == 'pemasukan' ? 'badge-masuk' : 'badge-keluar'; ?>">
                                            <?= ucfirst($row['tipe']); ?>
                                        </span>
                                    </td>
                                    <td><?= $row['jenis']; ?></td>
                                    <td class="amount">
                                        Rp <?= number_format($row['jumlah'], 0, ",", "."); ?>
                                    </td>
                                </tr>
                            <?php
                            }

                            if (!$ada_data) {
                            ?>
                                <tr>
                                    <td colspan="5" style="text-align:center; padding:24px; color:#888;">
                                        Tidak ada transaksi pada periode ini.
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <?php } ?>
        </main>
    </div>
</body>

</html>
