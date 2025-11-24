<?php
session_start();
include_once 'koneksi.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['flash_error'] = 'Akses ditolak.';
    header('Location: dashboard/index.php');
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['flash_error'] = 'ID user tidak valid.';
    header('Location: dashboard/index.php');
    exit();
}

$id = (int) $_GET['id'];

// Cegah admin menghapus akun dirinya sendiri
if ($id === (int) $_SESSION['user_id']) {
    $_SESSION['flash_error'] = 'Anda tidak dapat menghapus akun Anda sendiri.';
    header('Location: dashboard/index.php');
    exit();
}

// Hapus user
$sql = "DELETE FROM users WHERE id_user = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log('[hapus_user] prepare failed: ' . $conn->error);
    $_SESSION['flash_error'] = 'Terjadi kesalahan server.';
    header('Location: dashboard/index.php');
    exit();
}
$stmt->bind_param('i', $id);
if ($stmt->execute()) {
    $_SESSION['flash_success'] = 'User berhasil dihapus.';
    $stmt->close();
    header('Location: dashboard/index.php');
    exit();
} else {
    error_log('[hapus_user] execute failed: ' . $stmt->error);
    $_SESSION['flash_error'] = 'Gagal menghapus user.';
    $stmt->close();
    header('Location: dashboard/index.php');
    exit();
}
