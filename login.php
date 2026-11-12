<?php
session_start();
include "koneksi.php";

$pesan = "";

if (isset($_POST['login'])) {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_prepare($koneksi,
        "SELECT password FROM user WHERE username = ?"
    );

    if (!$query) {
        die("Query gagal: " . mysqli_error($koneksi));
    }

    mysqli_stmt_bind_param($query, "s", $username);
    mysqli_stmt_execute($query);

    mysqli_stmt_bind_result($query, $password_db);

    if (mysqli_stmt_fetch($query)) {

        if (password_verify($password, $password_db)) {

            $_SESSION['kasir'] = $username;

            mysqli_stmt_close($query);

            header("Location: dashboard.php");
            exit;

        } else {
            $pesan = "Password salah!";
        }

    } else {
        $pesan = "Username tidak ditemukan!";
    }

    mysqli_stmt_close($query);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Kasir</title>
</head>
<body>

<h2>LOGIN KASIR SEKOLAH</h2>

<form method="POST">

    Username:<br>
    <input type="text" name="username" required>

    <br><br>

    Password:<br>
    <input type="password" name="password" required>

    <br><br>

    <button type="submit" name="login">Login</button>

</form>

<p><?php echo $pesan; ?></p>

<a href="register.php">Daftar Akun</a>

</body>
</html>
