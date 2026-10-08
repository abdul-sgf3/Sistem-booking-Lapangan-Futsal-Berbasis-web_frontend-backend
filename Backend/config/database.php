<?php
$host = "localhost";
$user = "root"; 
$pass = "";     
$db = 'vincent_sq_db'; // <-- Ganti dari 'vincent_sqdb.sql' ke 'vincent_sq_db'

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>