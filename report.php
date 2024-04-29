<?php
// Include file database.php untuk koneksi ke database
include 'database.php';

// Ambil semua nilai yang dikirimkan melalui metode GET
$all_values = $_GET;

// Pastikan request berasal dari alamat IP Server otomax kawan2
$client_ip = $_SERVER['REMOTE_ADDR'];
if ($client_ip !== 'ip_server_contoh 192.168.1.1') {
    die('Unauthorized access');
}

// Tangkap pesan yang dikirim dari 192.168.1.1
$message = $all_values['message'] ?? '';

// Jika pesan tidak kosong, teruskan ke chatbox
if (!empty($message)) {
    // Ubah format pesan
    $formatted_message = urldecode(str_replace("message=", "", $message));

    // Simpan pesan ke dalam tabel history_chat
    $sql = "INSERT INTO history_chat (message) VALUES (?)";
    $stmt = $conn->prepare($sql);

    // Bind parameter pesan ke pernyataan SQL
    $stmt->bind_param("s", $formatted_message);

    // Eksekusi pernyataan SQL
    $stmt->execute();

    // Tutup pernyataan
    $stmt->close();

    // Tampilkan pesan yang diteruskan ke chatbox
    echo "Pesan berhasil diteruskan ke chatbox: $formatted_message";
} else {
    echo "Tidak ada pesan yang diterima.";
}

// Tutup koneksi ke database
$conn->close();
?>
