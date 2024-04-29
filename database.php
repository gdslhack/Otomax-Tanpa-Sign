<?php
$servername = "localhost"; // Ganti sesuai dengan server Anda
$username = "Otomax"; // Ganti dengan username database Anda
$password = "Password"; // Ganti dengan password database Anda
$dbname = "Otomax"; // Ganti dengan nama database Anda

// Buat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

?>
