<?php
// Periksa apakah parameter "command" ada
if(isset($_GET['command'])) {
    // Tangkap parameter dari dashboard.php
    $command = $_GET['command'];

    // Jika perintah adalah "sal", lakukan permintaan saldo
    if ($command === 'sal') {
        // Ambil member id, pin, dan password dari database
        include 'database.php';

        $sql = "SELECT memberid, pin, password FROM member LIMIT 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $memberID = $row["memberid"];
            $pin = $row["pin"];
            $password = $row["password"];

            // Buat URL dengan data yang diperoleh dari database
            $url = "http://ip_server_otomax:6969/balance?memberID=$memberID&pin=$pin&password=$password";

            // Jalankan perintah curl untuk mengambil saldo
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $url
            ]);
            $saldo_response = curl_exec($curl);

            // Tutup koneksi curl
            curl_close($curl);

            // Teruskan pesan respon dari server lain ke dashboard.php
            echo $saldo_response;
            exit;
        } else {
            echo "Tidak ada data member.";
        }

        // Tutup koneksi ke database
        $conn->close();
     } elseif ($command === 'trx') {
        // Kirim permintaan ke respon_trx.php
        header("Location: respon_trx.php?message={$_GET['message']}");
        exit;
    } else {
        // Jika perintah tidak dikenali, abaikan dan biarkan respon_trx.php menangani
    }
} else {
    // Jika parameter "command" tidak ada
    echo "Parameter 'command' tidak ada.";
    exit;
}
?>
