<?php
// Include file database.php untuk koneksi ke database
include 'database.php';

// Parse pesan log
$log = '2024-04-29 04:35:28 103.153.188.91 - - [29/Apr/2024:04:35:28 +0700] "GET /OtomaX/report.php?t=43527&refid=reffid%3Dapisik543576&status=20&price=0&message=R%2311533%20-%20Dedy%20SIK%20-%20%20CPLN.45102735896%20SUKSES.%20SN%2FRef%3A%20idpel%3D546402626546%2Fnometer%3D45102735896%2Fnama%3DM%20FUDOLI%20AJ%2Ftarifdaya%3DR1M%2F900.%20Saldo%20-20.735%20-%20hrg0%3D%20Sisa%20Saldo-20.735%20%4029%2F04%2004%3A06%3A03%20%20*TRX%20AMAN* 1.1" 200 246 "-" "-"';

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
