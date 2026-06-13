<?php
include '../koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $kehadiran = mysqli_real_escape_string($conn, $_POST['kehadiran']);
    $pesan = mysqli_real_escape_string($conn, $_POST['pesan']);

    $sql = "INSERT INTO ucapan (nama, kehadiran, pesan) VALUES ('$nama', '$kehadiran', '$pesan')";
    
    if (mysqli_query($conn, $sql)) {
        echo "sukses";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>