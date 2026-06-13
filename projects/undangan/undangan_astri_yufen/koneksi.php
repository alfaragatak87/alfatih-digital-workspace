<?php
$host = "localhost";
$user = "mckmmukg_alfa";
$pass = "Alfaragatak87";
$db   = "mckmmukg_undangan";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>