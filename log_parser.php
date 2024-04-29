<?php
// Include file database.php untuk koneksi ke database
include 'database.php';

// Parse pesan log
$log = '2024-04-29 04:35:28 ip-otomax - - [29/Apr/2024:04:35:28 +0700] "GET /OtomaX/report.php?t=

preg_match('/^(\S+)\s(\S+)\s(\S+)\s(\S+)\s\[(.*?)\]\s\"(.*?)\"\s(\S+)\s(\S+)$/', $log, $matches);

if (count($matches) >= 9) {
    $date = $matches[1];
    $ip_address = $matches[3];
    $request = $matches[6];
    $status = $matches[8];

    // Memasukkan informasi ke dalam database
    $sql = "INSERT INTO logs (date, ip_address, request, status) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $date, $ip_address, $request, $status);
    $stmt->execute();
    $stmt->close();
}

// Tutup koneksi ke database
$conn->close();
?>
