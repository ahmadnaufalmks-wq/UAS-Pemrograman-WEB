<?php
include "cek_login.php";
include "koneksi.php";

// =============================
// CREATE DATA KEUANGAN
// =============================
if (isset($_POST['tambah'])) {

    $tipe = $_POST['tipe'];
    $jenis = $_POST['jenis'];
    $jumlah = $_POST['jumlah'];
    $tanggal = $_POST['tanggal'];

    $query = mysqli_query(
        $conn,
        "INSERT INTO keuangan(tipe, jenis, jumlah, tanggal)
        VALUES('$tipe','$jenis','$jumlah','$tanggal')"
    );

    if ($query) {

        echo "
        <script>
            alert('Data berhasil ditambahkan');
            window.location='keungan.php';
        </script>";

        exit;
    } else {

        echo "
        <script>
            alert('Data gagal ditambahkan');
        </script>";
    }
}

// =============================
// UPDATE DATA KEUANGAN
// =============================
if (isset($_POST['update'])) {

    $id      = $_POST['id'];
    $tipe    = $_POST['tipe'];
    $jenis   = $_POST['jenis'];
    $jumlah  = $_POST['jumlah'];
    $tanggal = $_POST['tanggal'];

    $query = mysqli_query(
        $conn,
        "UPDATE keuangan
         SET tipe='$tipe', jenis='$jenis', jumlah='$jumlah', tanggal='$tanggal'
         WHERE id='$id'"
    );

    if ($query) {

        echo "
        <script>
            alert('Data berhasil diupdate');
            window.location='keungan.php';
        </script>";

        exit;
    } else {

        echo "
        <script>
            alert('Data gagal diupdate');
        </script>";
    }
}

// =============================
// DELETE DATA KEUANGAN
// =============================
if (isset($_GET['hapus'])) {

    $id = $_GET['hapus'];

    $query = mysqli_query(
        $conn,
        "DELETE FROM keuangan WHERE id='$id'"
    );

    if ($query) {

        echo "
        <script>
            alert('Data berhasil dihapus');
            window.location='keungan.php';
        </script>";

        exit;
    } else {

        echo "
        <script>
            alert('Data gagal dihapus');
        </script>";
    }
}

// =============================
// AMBIL DATA UNTUK MODE EDIT
// =============================
$edit_data = null;

if (isset($_GET['edit'])) {

    $id_edit = $_GET['edit'];

    $edit_query = mysqli_query(
        $conn,
        "SELECT * FROM keuangan WHERE id='$id_edit'"
    );

    $edit_data = mysqli_fetch_assoc($edit_query);
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

// Mengambil data pemasukan
$pemasukan = mysqli_query(
    $conn,
    "SELECT * FROM keuangan
     WHERE tipe='pemasukan'
     ORDER BY tanggal DESC"
);

// Mengambil data pengeluaran
$pengeluaran = mysqli_query(
    $conn,
    "SELECT * FROM keuangan
     WHERE tipe='pengeluaran'
     ORDER BY tanggal DESC"
);

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keuangan - Masjid Al-Haq BTN CV Dewi</title>
    <link rel="stylesheet" href="desain/desain_keungan.css">
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
                <a href="dashboard.php">Dashboard</a>
                <a class="active" href="keungan.php">Keuangan</a>
                <a href="laporan.php">Laporan</a>
                <a href="#">Pengaturan</a>
                <a href="logout.php">Logout</a>
            </nav>
        </aside>

        <main class="keuangan-main">
            <header class="keuangan-topbar">
                <div>
                    <p class="eyebrow">Manajemen Keuangan</p>
                    <h2>Data Keuangan Masjid</h2>
                </div>
                <div class="topbar-chip">Administrator</div>
            </header>

            <section class="keuangan-overview">
                <div class="overview-card">
                    <p class="card-label">Total Pemasukan</p>
                    <p class="total-value">Rp <?= number_format($total_pemasukan, 0, ",", "."); ?></p>
                    <span class="card-note">Total seluruh uang masuk.</span>
                </div>
                <div class="overview-card">
                    <p class="card-label">Total Pengeluaran</p>
                    <p class="total-value">Rp <?= number_format($total_pengeluaran, 0, ",", "."); ?></p>
                    <span class="card-note">Total seluruh uang keluar.</span>
                </div>
                <div class="overview-card highlight">
                    <p class="card-label">Saldo Bersih</p>
                    <p class="total-value">Rp <?= number_format($saldo_bersih, 0, ",", "."); ?></p>
                    <span class="card-note">Sisa dana kas tersedia.</span>
                </div>
            </section>

            <section class="keuangan-form">
                <div class="form-container">
                    <div class="form-header">
                        <h3><?= $edit_data ? "Edit Data Keuangan" : "Tambah Data Keuangan"; ?></h3>
                        <p><?= $edit_data ? "Perbarui data transaksi yang dipilih" : "Masukkan data pemasukan atau pengeluaran baru"; ?></p>
                    </div>
                    <form action="" method="post" class="data-form">

                        <?php if ($edit_data) { ?>
                            <input type="hidden" name="id" value="<?= $edit_data['id']; ?>">
                        <?php } ?>

                        <div class="form-group">
                            <label for="tipe">Tipe Transaksi</label>
                            <select id="tipe" name="tipe" required>
                                <option value="">Pilih Tipe</option>
                                <option value="pemasukan" <?= ($edit_data && $edit_data['tipe'] == 'pemasukan') ? 'selected' : ''; ?>>Pemasukan</option>
                                <option value="pengeluaran" <?= ($edit_data && $edit_data['tipe'] == 'pengeluaran') ? 'selected' : ''; ?>>Pengeluaran</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="jenis">Jenis / Deskripsi</label>
                            <input type="text" id="jenis" name="jenis" placeholder="Contoh: Donasi Bulanan" value="<?= $edit_data ? $edit_data['jenis'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="jumlah">Jumlah (Rp)</label>
                            <input type="number" id="jumlah" name="jumlah" placeholder="0" min="0" value="<?= $edit_data ? $edit_data['jumlah'] : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="tanggal">Tanggal</label>
                            <input type="date" id="tanggal" name="tanggal" value="<?= $edit_data ? $edit_data['tanggal'] : ''; ?>" required>
                        </div>

                        <?php if ($edit_data) { ?>
                            <button type="submit" name="update" class="submit-btn">Update Data</button>
                            <a href="keungan.php" class="submit-btn" style="text-align:center; text-decoration:none; background:#999;">Batal</a>
                        <?php } else { ?>
                            <button type="submit" name="tambah" class="submit-btn">Tambah Data</button>
                        <?php } ?>
                    </form>
                </div>
            </section>

            <section class="keuangan-tables">
                <div class="table-container">
                    <div class="table-header">
                        <h3>Uang Masuk</h3>
                        <p>Riwayat pemasukan ke kas masjid</p>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Jenis Pemasukan</th>
                                <th>Jumlah</th>
                                <th>Waktu</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;

                            while ($row = mysqli_fetch_assoc($pemasukan)) {
                            ?>

                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= $row['jenis']; ?></td>
                                    <td class="amount">
                                        Rp <?= number_format($row['jumlah'], 0, ",", "."); ?>
                                    </td>
                                    <td><?= $row['tanggal']; ?></td>
                                    <td class="aksi">
                                        <a href="keungan.php?edit=<?= $row['id']; ?>">Edit</a>
                                        |
                                        <a href="keungan.php?hapus=<?= $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus data ini?');">Hapus</a>
                                    </td>
                                </tr>

                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="table-container">
                    <div class="table-header">
                        <h3>Pengeluaran</h3>
                        <p>Riwayat pengeluaran dari kas masjid</p>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Jenis Pengeluaran</th>
                                <th>Jumlah</th>
                                <th>Waktu</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;

                            while ($row = mysqli_fetch_assoc($pengeluaran)) {
                            ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= $row['jenis']; ?></td>
                                    <td class="amount">
                                        Rp <?= number_format($row['jumlah'], 0, ",", "."); ?>
                                    </td>
                                    <td><?= $row['tanggal']; ?></td>
                                    <td class="aksi">
                                        <a href="keungan.php?edit=<?= $row['id']; ?>">Edit</a>
                                        |
                                        <a href="keungan.php?hapus=<?= $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus data ini?');">Hapus</a>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</body>

</html>