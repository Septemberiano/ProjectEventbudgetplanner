<?php
// Cek status sesi, jika belum mulai, jalankan session_start()
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- KONFIGURASI DATABASE OTOMATIS ---

// Cek apakah script berjalan di Localhost atau Server Online
if ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1') {
    // --- SETTINGAN LOKAL (XAMPP) ---
    $hostname = "localhost";
    $username = "root";
    $password = "";         // Password default XAMPP biasanya kosong
    $database = "eventplanner"; // Sesuaikan dengan nama database lokal Anda
    $port     = 3306;       // Port default MySQL XAMPP
} else {
    // --- SETTINGAN RAILWAY (PRODUCTION) ---
    // Mengambil kredensial dari Environment Variables Railway
    $hostname = getenv('MYSQLHOST');
    $username = getenv('MYSQLUSER');
    $password = getenv('MYSQLPASSWORD');
    $database = getenv('MYSQLDATABASE');
    $port     = getenv('MYSQLPORT');
}

// --- MEMBUAT KONEKSI ---
$conn = new mysqli($hostname, $username, $password, $database, $port);

// --- CEK KONEKSI ---
if ($conn->connect_error) {
    // Mencatat error ke log server (tidak ditampilkan ke user demi keamanan)
    error_log("Koneksi Database Gagal: " . $conn->connect_error);
    die("Koneksi Database Gagal. Silakan cek konfigurasi.");
}

// Set charset agar karakter khusus/emoji aman
$conn->set_charset("utf8mb4");

// --- FUNGSI HELPER (BANTUAN) ---

// Fungsi 1: Redirect jika user SUDAH login (misal: saat akses halaman login)
function redirectIfLoggedIn($location = 'dashboard/index.php') {
    if (isset($_SESSION['user_id'])) {
        header("Location: $location");
        exit;
    }
}

// Fungsi 2: Redirect jika user BELUM login (misal: saat akses dashboard)
function redirectIfNotLoggedIn($location = 'login.php') {
    // Sesuaikan path location jika file pemanggil ada di dalam folder dashboard
    // Logika sederhana: jika user_id tidak ada di sesi, tendang ke login
    if (!isset($_SESSION['user_id'])) {
        header("Location: $location");
        exit;
    }
}
?>