<?php
session_set_cookie_params(0, "/");
session_start();
include "koneksi.php";

// Wajib login sebagai jamaah untuk mengakses halaman ini
if (
    !isset($_SESSION['login_jamaah']) ||
    !isset($_SESSION['id_jamaah'])
) {
    header("Location: login_jamaah.php?redirect=infaq");
    exit;
}

$id_jamaah   = $_SESSION['id_jamaah'];
$nama_jamaah = $_SESSION['nama_jamaah'];

// =============================
// PROSES SIMPAN INFAQ
// =============================
if (isset($_POST['infaq'])) {

    $jenis_infaq = $_POST['jenis_infaq'];
    $jumlah      = $_POST['jumlah'];
    $pesan       = $_POST['pesan'];
    $tanggal     = date('Y-m-d');

    // 1. Simpan ke tabel infaq (riwayat infaq per jamaah)
    $query1 = mysqli_query(
        $conn,
        "INSERT INTO infaq(id_jamaah, nama_jamaah, jenis_infaq, jumlah, pesan, tanggal)
         VALUES('$id_jamaah', '$nama_jamaah', '$jenis_infaq', '$jumlah', '$pesan', '$tanggal')"
    );

    // 2. Otomatis catat juga sebagai Pemasukan di tabel keuangan
    //    supaya langsung terhitung di ringkasan keuangan masjid
    $jenis_keuangan = $jenis_infaq . " - " . $nama_jamaah;
    $query2 = mysqli_query(
        $conn,
        "INSERT INTO keuangan(tipe, jenis, jumlah, tanggal)
         VALUES('pemasukan', '$jenis_keuangan', '$jumlah', '$tanggal')"
    );

    if ($query1 && $query2) {

        echo "
        <script>
            alert('Terima kasih! Infaq Anda berhasil dicatat.');
            window.location='infaq.php';
        </script>";
        exit;

    } else {

        echo "
        <script>
            alert('Infaq gagal disimpan, silakan coba lagi.');
        </script>";
    }
}

// =============================
// AMBIL RIWAYAT INFAQ JAMAAH INI
// =============================
$riwayat = mysqli_query(
    $conn,
    "SELECT * FROM infaq WHERE id_jamaah='$id_jamaah' ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Infaq - Masjid Al-Haq BTN CV Dewi</title>
    <link rel="stylesheet" href="desain/desain_keungan.css">
    <link rel="stylesheet" href="desain/desain_laporan.css">
    <link rel="stylesheet" href="desain/desain_infaq.css">
</head>

<body class="keuangan-page">
    <div class="keuangan-shell">
        <aside class="keuangan-sidebar">
            <div class="brand">
                <div class="brand-icon">🕌</div>
                <div>
                    <h1>Masjid Al-Haq</h1>
                    <p>BTN CV Dewi</p>
                </div>
            </div>

            <nav class="sidebar-nav">
                <a href="landing_page.php">Beranda</a>
                <a class="active" href="infaq.php">Infaq</a>
                <a href="logout_jamaah.php">Logout</a>
            </nav>
        </aside>

        <main class="keuangan-main">
            <header class="keuangan-topbar">
                <div>
                    <p class="eyebrow">Assalamu'alaikum, <?= $nama_jamaah; ?></p>
                    <h2>Form Infaq Jamaah</h2>
                </div>
                <div class="topbar-chip">Jamaah</div>
            </header>

            <section class="keuangan-form">
                <div class="form-container">
                    <div class="form-header">
                        <h3>Tunaikan Infaq</h3>
                        <p>Isi form berikut untuk mencatatkan infaq/sedekah Anda</p>
                    </div>
                    <form action="" method="post" class="data-form">
                        <div class="form-group">
                            <label for="jenis_infaq">Jenis</label>
                            <select id="jenis_infaq" name="jenis_infaq" required>
                                <option value="">Pilih Jenis</option>
                                <option value="Infaq">Infaq</option>
                                <option value="Sedekah">Sedekah</option>
                                <option value="Zakat">Zakat</option>
                                <option value="Wakaf">Wakaf</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="jumlah">Jumlah (Rp)</label>
                            <input type="number" id="jumlah" name="jumlah" placeholder="0" min="1000" required>
                        </div>
                        <div class="form-group full-width">
                            <label for="pesan">Pesan / Doa (opsional)</label>
                            <input type="text" id="pesan" name="pesan" placeholder="Contoh: Semoga bermanfaat untuk pembangunan masjid">
                        </div>
                        <button type="submit" name="infaq" class="submit-btn">Kirim Infaq</button>
                    </form>
                </div>
            </section>

            <div class="table-container">
                <div class="table-header">
                    <h3>Riwayat Infaq Saya</h3>
                    <p>Catatan infaq yang pernah Anda lakukan</p>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Jumlah</th>
                            <th>Pesan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $ada = false;
                        while ($row = mysqli_fetch_assoc($riwayat)) {
                            $ada = true;
                        ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= $row['tanggal']; ?></td>
                                <td><?= $row['jenis_infaq']; ?></td>
                                <td class="amount">Rp <?= number_format($row['jumlah'], 0, ",", "."); ?></td>
                                <td><?= $row['pesan'] ? $row['pesan'] : '-'; ?></td>
                            </tr>
                        <?php
                        }
                        if (!$ada) {
                        ?>
                            <tr>
                                <td colspan="5" style="text-align:center; padding:24px; color:#888;">
                                    Anda belum pernah berinfaq.
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>

</html>
