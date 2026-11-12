<?php

$koneksi = mysqli_connect("localhost", "root", "", "kasir_ukkxi");

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

?>
