<?php if ($edit) { ?>

<h3>Edit Produk</h3>

<form method="POST">

    <input type="hidden" name="id"
        value="<?php echo $edit['ProdukID']; ?>">

    Nama Barang:<br>
    <input type="text" name="nama"
        value="<?php echo htmlspecialchars($edit['NamaProduk']); ?>"
        required>
    <br><br>

    Harga Barang:<br>
    <input type="number" name="harga"
        value="<?php echo $edit['Harga']; ?>"
        required>
    <br><br>

    Stok Barang:<br>
    <input type="number" name="stok"
        value="<?php echo $edit['Stok']; ?>"
        required>
    <br><br>

    <button type="submit" name="edit">Simpan Perubahan</button>

    <a href="produuk.php">Batal</a>

</form>

<?php } else { ?>

<h3>Tambah Barang</h3>

<form method="POST">

    Nama Barang:<br>
    <input type="text" name="nama" required>
    <br><br>

    Harga Barang:<br>
    <input type="number" name="harga" required>
    <br><br>

    Stok Barang:<br>
    <input type="number" name="stok" required>
    <br><br>

    <button type="submit" name="tambah">Tambah Barang</button>

</form>

<?php } 
?>