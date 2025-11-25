<?php

// Pastikan session_start() adalah hal pertama yang dijalankan jika belum dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Ambil Kredensial dari Railway Environment Variables (ENV)
// Jika ENV tidak ditemukan (misalnya saat di local), gunakan nilai default (local)
$hostname = getenv('MYSQL_HOST') ?: 'localhost'; 
$username = getenv('MYSQLUSER') ?: 'root'; 
$password = getenv('MYSQLPASSWORD') ?: '';
$database = getenv('MYSQL_DATABASE') ?: 'eventplanner'; 
$port     = getenv('MYSQLPORT') ?: 3306; // Ambil port, default 3306

// 2. Buat Koneksi
// Tambahkan $port sebagai parameter kelima
$conn = new mysqli($hostname, $username, $password, $database, $port);

// 3. Cek Koneksi
if ($conn->connect_error) {
    // Di lingkungan produksi (Railway), lebih aman menampilkan pesan umum
    // dan mencatat error detail ke log server.
    error_log("Koneksi Database Gagal: " . $conn->connect_error);
    die("Koneksi Database Gagal. Silakan coba sebentar lagi.");
}

$conn->set_charset("utf8mb4");

// 4. Fungsi-fungsi Bawaan (Tetap sama)
function redirectIfLoggedIn($location = 'dashboard/index.php') {
    if (isset($_SESSION['user_id'])) {
        header("Location: $location");
        exit;
    }
}

function redirectIfNotLoggedIn($location = 'login.php') {
    if (!isset($_SESSION['user_id'])) {
        header("Location: $location");
        exit;
    }
}
?>