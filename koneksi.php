<?php
// Pastikan file ini disimpan sebagai UTF-8 TANPA BOM
// Gunakan getenv() untuk mengambil variabel lingkungan dari Railway
$servername = getenv('MYSQL_HOST');
$username   = getenv('MYSQL_USER');
$password   = getenv('MYSQL_PASSWORD');
$database   = getenv('MYSQL_DATABASE');
$port       = getenv('MYSQL_PORT'); // Port unik dari Railway

// 1. Lakukan Koneksi
// Penting: Kita perlu memasukkan $port sebagai parameter tambahan
$conn = new mysqli($servername, $username, $password, $database, $port);

// 2. Penanganan Error Koneksi
if ($conn->connect_error) {
    // Catat error di log Railway, jangan tampilkan ke user
    error_log("Koneksi database gagal: " . $conn->connect_error);
    
    // Tampilkan pesan umum ke user
    die("Koneksi database gagal. Silakan hubungi administrator.");
}

$conn->set_charset("utf8mb4");


?>