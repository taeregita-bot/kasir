
<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['kasir'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    $query = mysqli_prepare($koneksi,
        "INSERT INTO produk (NamaProduk, Harga, Stok) VALUES (?, ?, ?)"
    );

    mysqli_stmt_bind_param($query, "sdi", $nama, $harga, $stok);
    mysqli_stmt_execute($query);

    header("Location: produk.php");
    exit;
}

if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    $query = mysqli_prepare($koneksi,
        "UPDATE produk SET NamaProduk=?, Harga=?, Stok=? WHERE ProdukID=?"
    );

    mysqli_stmt_bind_param($query, "sdii", $nama, $harga, $stok, $id);
    mysqli_stmt_execute($query);

    header("Location: produk.php");
    exit;
}

if (isset($_POST['hapus'])) {
    $id = $_POST['id'];

    $query = mysqli_prepare($koneksi,
        "DELETE FROM produk WHERE ProdukID=?"
    );

    mysqli_stmt_bind_param($query, "i", $id);
    mysqli_stmt_execute($query);

    header("Location: produk.php");
    exit;
}

$edit = null;

if (isset($_GET['edit'])) {
    $id = $_GET['edit'];

    $query = mysqli_prepare($koneksi,
        "SELECT * FROM produk WHERE ProdukID=?"
    );

    mysqli_stmt_bind_param($query, "i", $id);
    mysqli_stmt_execute($query);
    mysqli_stmt_bind_result($query, $id_db, $nama_db, $harga_db, $stok_db);

    if (mysqli_stmt_fetch($query)) {
        $edit = [
            'ProdukID' => $id_db,
            'NamaProduk' => $nama_db,
            'Harga' => $harga_db,
            'Stok' => $stok_db
        ];
    }

    mysqli_stmt_close($query);
}

$data = mysqli_query($koneksi, "SELECT * FROM produk");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Barang</title>
</head>
<body>

<h2>DATA BARANG</h2>

<a href="dashboard.php">Kembali ke Dashboard</a>

<hr>

<?php if ($edit) { ?>

<h3>Edit Barang</h3>

<form method="POST">
    <input type="hidden" name="id" value="<?php echo $edit['ProdukID']; ?>">

    Nama Barang:<br>
    <input type="text" name="nama" value="<?php echo htmlspecialchars($edit['NamaProduk']); ?>" required>
    <br><br>

    Harga:<br>
    <input type="number" name="harga" value="<?php echo $edit['Harga']; ?>" required>
    <br><br>

    Stok:<br>
    <input type="number" name="stok" value="<?php echo $edit['Stok']; ?>" required>
    <br><br>

    <button type="submit" name="edit">Simpan</button>
    <a href="produk.php">Batal</a>
</form>

<?php } else { ?>

<h3>Tambah Barang</h3>

<form method="POST">

    Nama Barang:<br>
    <input type="text" name="nama" required>
    <br><br>

    Harga:<br>
    <input type="number" name="harga" required>
    <br><br>

    Stok:<br>
    <input type="number" name="stok" required>
    <br><br>

    <button type="submit" name="tambah">Tambah</button>

</form>

<?php } ?>

<hr>

<h3>Daftar Barang</h3>

<table border="1" cellpadding="8" cellspacing="0">

    <tr>
        <th>No</th>
        <th>Nama Barang</th>
        <th>Harga</th>
        <th>Stok</th>
        <th>Aksi</th>
    </tr>

    <?php
    $no = 1;

    while ($barang = mysqli_fetch_assoc($data)) {
    ?>

    <tr>
        <td><?php echo $no++; ?></td>
        <td><?php echo htmlspecialchars($barang['NamaProduk']); ?></td>
        <td>Rp <?php echo number_format($barang['Harga'], 0, ',', '.'); ?></td>
        <td><?php echo $barang['Stok']; ?></td>

        <td>
            <a href="produk.php?edit=<?php echo $barang['ProdukID']; ?>">Edit</a>

            <form method="POST" style="display:inline;"
                onsubmit="return confirm('Hapus barang ini?')">

                <input type="hidden" name="id" value="<?php echo $barang['ProdukID']; ?>">
                <button type="submit" name="hapus">Hapus</button>

            </form>
        </td>
    </tr>

    <?php } ?>

</table>

</body>
</html>
