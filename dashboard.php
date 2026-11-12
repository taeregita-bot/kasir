
<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['kasir'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Kasir</title>
</head>
<body>

<h2>KASIR SEKOLAH</h2>

<p>Selamat datang, <?php echo htmlspecialchars($_SESSION['kasir']); ?></p>

<hr>

<h3>Menu Utama</h3>

<a href="barang.php">Data Barang</a>
<br><br>

<a href="transaksi.php">Transaksi Penjualan</a>
<br><br>

<a href="laporan.php">Laporan Penjualan</a>
<br><br>

<a href="logout.php">Logout</a>

<hr>

<h3>Informasi Aplikasi</h3>

<p>Selamat datang di aplikasi Kasir Sekolah.</p>
<p>Silakan pilih menu untuk mengelola barang, melakukan transaksi, dan melihat laporan penjualan.</p>

</body>
</html>
