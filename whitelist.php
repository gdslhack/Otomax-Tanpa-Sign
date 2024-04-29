<?php
// Daftar alamat IP yang diizinkan untuk mengakses server lokal Anda
$allowed_ips = array(
    'ip_otomax_anda', // Ganti dengan alamat IP server otomax
    // Tambahkan alamat IP lain jika perlu
);

// Periksa alamat IP pengirim
$remote_ip = $_SERVER['REMOTE_ADDR'];

if (in_array($remote_ip, $allowed_ips)) {
    // Alamat IP diizinkan, lanjutkan dengan proses yang diinginkan

    // Contoh: Proses pesan yang diterima dari server lokal
    $message = $_POST['message'] ?? '';
    if (!empty($message)) {
        // Lakukan sesuatu dengan pesan yang diterima
        echo "Pesan diterima: $message";
    } else {
        echo "Pesan kosong.";
    }
} else {
    // Alamat IP tidak diizinkan, kirimkan respon error
    http_response_code(403); // Forbidden
    echo "Akses ditolak.";
}
?>
