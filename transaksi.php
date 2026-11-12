
<?php
session_start();

if (!isset($_SESSION['kasir'])) {
    header("Location: login.php");
    exit;
}

include "koneksi.php";

$pesan = "";

if (isset($_POST['simpan'])) {
    $pelangganID = (int) $_POST['pelanggan'];
    $produkID = (int) $_POST['produk'];
    $jumlah = (int) $_POST['jumlah'];
    $uangBayar = (float) $_POST['uang_bayar'];

    if ($jumlah <= 0 || $uangBayar < 0) {
        $pesan = "Jumlah dan pembayaran tidak valid.";
    } else {
        mysqli_begin_transaction($koneksi);

        try {
            // Mengambil harga dan stok produk
            $query = mysqli_prepare($koneksi,
                "SELECT Harga, Stok FROM produk
                 WHERE ProdukID = ? FOR UPDATE"
            );

            mysqli_stmt_bind_param($query, "i", $produkID);
            mysqli_stmt_execute($query);

            $hasil = mysqli_stmt_get_result($query);
            $barang = mysqli_fetch_assoc($hasil);

            if (!$barang) {
                throw new Exception("Produk tidak ditemukan.");
            }

            if ($barang['Stok'] < $jumlah) {
                throw new Exception("Stok produk tidak mencukupi.");
            }

            // Memeriksa pelanggan
            $query = mysqli_prepare($koneksi,
                "SELECT PelangganID FROM pelanggan
                 WHERE PelangganID = ?"
            );

            mysqli_stmt_bind_param($query, "i", $pelangganID);
            mysqli_stmt_execute($query);

            if (mysqli_num_rows(mysqli_stmt_get_result($query)) == 0) {
                throw new Exception("Pelanggan tidak ditemukan.");
            }

            // Menghitung pembayaran
            $totalHarga = $barang['Harga'] * $jumlah;

            if ($uangBayar < $totalHarga) {
                throw new Exception("Uang pembayaran masih kurang.");
            }

            $kembalian = $uangBayar - $totalHarga;

            // Menyimpan transaksi
            $query = mysqli_prepare($koneksi,
                "INSERT INTO penjualan
                (TanggalPenjualan, TotalHarga, PelangganID, UangBayar, Kembalian)
                VALUES (CURDATE(), ?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $query, "didd",
                $totalHarga, $pelangganID, $uangBayar, $kembalian
            );

            mysqli_stmt_execute($query);

            $penjualanID = mysqli_insert_id($koneksi);

            // Menyimpan detail transaksi
            $query = mysqli_prepare($koneksi,
                "INSERT INTO detailpenjualan
                (PenjualanID, ProdukID, JumlahProduk, Subtotal)
                VALUES (?, ?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $query, "iiid",
                $penjualanID, $produkID, $jumlah, $totalHarga
            );

            mysqli_stmt_execute($query);

            // Mengurangi stok
            $query = mysqli_prepare($koneksi,
                "UPDATE produk SET Stok = Stok - ?
                 WHERE ProdukID = ?"
            );

            mysqli_stmt_bind_param($query, "ii", $jumlah, $produkID);
            mysqli_stmt_execute($query);

            mysqli_commit($koneksi);

            header("Location: laporan.php");
            exit;

        } catch (Exception $e) {
            mysqli_rollback($koneksi);
            $pesan = $e->getMessage();
        }
    }
}

$pelanggan = mysqli_query($koneksi,
    "SELECT * FROM pelanggan ORDER BY NamaPelanggan ASC"
);

$produk = mysqli_query($koneksi,
    "SELECT * FROM produk WHERE Stok > 0 ORDER BY NamaProduk ASC"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Transaksi Kasir</title>
</head>
<body>

<h2>TRANSAKSI PENJUALAN</h2>

<form method="POST">

    <label>Nama Pembeli</label><br>
    <select name="pelanggan" required>
        <option value="">Pilih Pembeli</option>

        <?php while ($p = mysqli_fetch_assoc($pelanggan)) { ?>
            <option value="<?= $p['PelangganID']; ?>">
                <?= htmlspecialchars($p['NamaPelanggan']); ?>
            </option>
        <?php } ?>
    </select>

    <br><br>

    <label>Nama Produk</label><br>
    <select name="produk" required>
        <option value="">Pilih Produk</option>

        <?php while ($b = mysqli_fetch_assoc($produk)) { ?>
            <option value="<?= $b['ProdukID']; ?>">
                <?= htmlspecialchars($b['NamaProduk']); ?>
                - Rp<?= number_format($b['Harga'], 0, ',', '.'); ?>
                - Stok <?= $b['Stok']; ?>
            </option>
        <?php } ?>
    </select>

    <br><br>

    <label>Jumlah Barang</label><br>
    <input type="number" name="jumlah" min="1" required>

    <br><br>

    <label>Uang Pembayaran</label><br>
    <input type="number" name="uang_bayar" min="0" required>

    <br><br>

    <button type="submit" name="simpan">
        Simpan Transaksi
    </button>

</form>

<p style="color:red;">
    <?= htmlspecialchars($pesan); ?>
</p>

<br>
<a href="dashboard.php">Kembali ke Dashboard</a>

</body>
</html>
