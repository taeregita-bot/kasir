<?php
include "koneksi.php";

$pesan = "";

if (isset($_POST['daftar'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $cek = mysqli_prepare($koneksi,
        "SELECT id FROM user WHERE username = ?"
    );

    mysqli_stmt_bind_param($cek, "s", $username);
    mysqli_stmt_execute($cek);
    mysqli_stmt_store_result($cek);

    if (mysqli_stmt_num_rows($cek) > 0) {

        $pesan = "Username sudah digunakan!";

    } else {

        $password_baru = password_hash($password, PASSWORD_DEFAULT);

        $query = mysqli_prepare($koneksi,
            "INSERT INTO user (username, password) VALUES (?, ?)"
        );

        mysqli_stmt_bind_param($query, "ss", $username, $password_baru);

        if (mysqli_stmt_execute($query)) {
            $pesan = "Pendaftaran berhasil! Silakan login.";
        } else {
            $pesan = "Pendaftaran gagal!";
        }

        mysqli_stmt_close($query);
    }

    mysqli_stmt_close($cek);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register Kasir</title>
</head>
<body>

<h2>REGISTER KASIR</h2>

<form method="POST">

    Username:<br>
    <input type="text" name="username" required>

    <br><br>

    Password:<br>
    <input type="password" name="password" required>

    <br><br>

    <button type="submit" name="daftar">Daftar</button>

</form>

<p><?php echo $pesan; ?></p>

<a href="login.php">Kembali ke Login</a>

</body>
</html>
