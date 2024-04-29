<?php
// Include file database.php untuk koneksi ke database
include 'database.php';

// Tangkap pesan yang dikirim oleh pengguna dari dashboard.php
$message = $_GET['message'] ?? '';

// Jika pesan tidak kosong
if (!empty($message)) {
    // Proses pesan jika dimulai dengan perintah transaksi 'trx'
    if (strpos($message, 'trx') === 0) {
        // Ekstrak informasi produk dan tujuan dari pesan
        $parts = explode(' ', $message); // Pisahkan pesan berdasarkan spasi
        // Periksa apakah pesan memiliki informasi produk dan tujuan yang cukup
        if (count($parts) < 2) {
            echo "Invalid command"; // Pesan jika parameter tidak ada
            exit;
        }

        // Ekstrak informasi produk dan tujuan dari pesan
        $product_and_dest = explode('.', $parts[1]); // Pisahkan informasi produk dan tujuan berdasarkan titik
        // Periksa apakah pesan memiliki informasi produk dan tujuan yang cukup
        if (count($product_and_dest) < 2) {
            echo "Invalid command"; // Pesan jika parameter tidak ada
            exit;
        }

        // Ambil member id, pin, dan password dari database
        $sql = "SELECT memberid, pin, password FROM member LIMIT 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $memberID = $row["memberid"];
            $pin = $row["pin"];
            $password = $row["password"];

            // Ekstrak informasi produk dan tujuan dari pesan
            $product = $product_and_dest[0]; // Produk adalah bagian pertama setelah titik
            $dest = $product_and_dest[1]; // Tujuan adalah bagian kedua setelah titik

            // Buat reffID acak dengan awalan 'reffid=apisik'
            $reffID = 'reffid=apisik' . rand(100000, 999999);

            // Buat URL dengan data yang diperoleh dari database
            $url = "http://Ip_server_otomax:6969/trx?product=$product&qty=1&dest=$dest&refID=$reffID&memberID=$memberID&pin=$pin&password=$password";

            // Jalankan perintah curl untuk melakukan transaksi
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $url
            ]);
            $transaction_response = curl_exec($curl);

            // Tutup koneksi curl
            curl_close($curl);

            // Tambahkan transaksi ke database
            $sql = "INSERT INTO transaksi (refID, product, dest, memberID, response) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $reffID, $product, $dest, $memberID, $transaction_response);
            $stmt->execute();
            $stmt->close();

            // Teruskan pesan respon transaksi dari server lain ke dashboard.php
            echo $transaction_response;
            exit;
        } else {
            echo "Tidak ada data member.";
        }
    } else {
        // Jika perintah tidak sesuai dengan format yang diharapkan
        echo "Invalid command";
        exit;
    }
}

// Tutup koneksi ke database
$conn->close();
?>
