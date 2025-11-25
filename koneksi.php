<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ambil kredensial DB dari Railway
$hostname = getenv('MYSQLHOST') ?: 'localhost';
$username = getenv('MYSQLUSER') ?: 'root';
$password = getenv('MYSQLPASSWORD') ?: '';
$database = getenv('MYSQLDATABASE') ?: 'eventplanner';
$port     = getenv('MYSQLPORT') ?: 3306;

// Buat koneksi
$conn = new mysqli($hostname, $username, $password, $database, $port);

// Cek koneksi
if ($conn->connect_error) {
    error_log("Koneksi Database Gagal: " . $conn->connect_error);
    die("Koneksi Database Gagal.");
}

$conn->set_charset("utf8mb4");

// Fungsi redirect
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
