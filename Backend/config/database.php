<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'vincent_sqdb'; // Nama database yang baru

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>