
<?php
session_start();

if (!isset($_SESSION['kasir'])) {
    header("Location: login.php");
    exit;
}

include "koneksi.php";

$sql = "SELECT 
            penjualan.PenjualanID,
            penjualan.TanggalPenjualan,
            pelanggan.NamaPelanggan,
            produk.NamaProduk,
            detailpenjualan.JumlahProduk,
            detailpenjualan.Subtotal,
            penjualan.TotalHarga,
            penjualan.UangBayar,
            penjualan.Kembalian
        FROM penjualan
        INNER JOIN pelanggan 
            ON penjualan.PelangganID = pelanggan.PelangganID
        INNER JOIN detailpenjualan 
            ON penjualan.PenjualanID = detailpenjualan.PenjualanID
        INNER JOIN produk 
            ON detailpenjualan.ProdukID = produk.ProdukID
        ORDER BY penjualan.PenjualanID DESC";

$query = mysqli_query($koneksi, $sql);

if (!$query) {
    die("Query laporan gagal: " . mysqli_error($koneksi));
}

$totalTransaksi = mysqli_num_rows($query);
$totalPenjualan = 0;
$data = [];

while ($row = mysqli_fetch_assoc($query)) {
    $data[] = $row;
    $totalPenjualan += $row['TotalHarga'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan</title>
</head>
<body>

<h2>LAPORAN PENJUALAN KASIR SEKOLAH</h2>

<p>Jumlah Transaksi: <?= $totalTransaksi; ?></p>

<p>
    Total Penjualan:
    Rp<?= number_format($totalPenjualan, 0, ',', '.'); ?>
</p>

<table border="1" cellpadding="8" cellspacing="0">
<tr>
    <th>No</th>
    <th>ID Penjualan</th>
    <th>Tanggal</th>
    <th>Nama Pembeli</th>
    <th>Nama Produk</th>
    <th>Jumlah</th>
    <th>Subtotal</th>
    <th>Total Harga</th>
    <th>Uang Bayar</th>
    <th>Kembalian</th>
</tr>

<?php
$no = 1;
foreach ($data as $row) {
?>
<tr>
    <td><?= $no++; ?></td>
    <td><?= $row['PenjualanID']; ?></td>
    <td><?= $row['TanggalPenjualan']; ?></td>
    <td><?= htmlspecialchars($row['NamaPelanggan']); ?></td>
    <td><?= htmlspecialchars($row['NamaProduk']); ?></td>
    <td><?= $row['JumlahProduk']; ?></td>
    <td>Rp<?= number_format($row['Subtotal'], 0, ',', '.'); ?></td>
    <td>Rp<?= number_format($row['TotalHarga'], 0, ',', '.'); ?></td>
    <td>Rp<?= number_format($row['UangBayar'], 0, ',', '.'); ?></td>
    <td>Rp<?= number_format($row['Kembalian'], 0, ',', '.'); ?></td>
</tr>
<?php } ?>

</table>

<br>

<button onclick="window.print()">Cetak Laporan</button>

<br><br>
<a href="transaksi.php">Tambah Transaksi</a>
<br><br>
<a href="dashboard.php">Kembali ke Dashboard</a>

</body>
</html>
