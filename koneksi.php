<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$hostname = 'localhost';
$username = 'root';        
$password = '';
$database = 'eventplanner'; 


$conn = new mysqli($hostname, $username, $password, $database);


if ($conn->connect_error) {
   
    die("Koneksi gagal: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

function redirectIfLoggedIn($location = 'dashboard.php') {
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