<?php

session_start();
include_once '../koneksi.php';

// Pastikan user sudah login — gunakan kunci session yang konsisten `user_id`
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Validasi parameter ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['flash_error'] = "ID Event tidak ditemukan.";
    header("Location: tambah-event.php?status=error");
    exit();
}

$id_event = (int) $_GET['id'];
$id_user_login = $_SESSION['user_id'];

// Cek kepemilikan event
$check_sql = "SELECT created_by FROM events WHERE id_event = ? LIMIT 1";
$check_stmt = mysqli_prepare($conn, $check_sql);
if ($check_stmt === false) {
    error_log('[hapusevent] prepare failed: ' . mysqli_error($conn));
    $_SESSION['flash_error'] = 'Terjadi kesalahan server saat memproses permintaan.';
    header("Location: tambah-event.php?status=error");
    exit();
}

mysqli_stmt_bind_param($check_stmt, "i", $id_event);
if (!mysqli_stmt_execute($check_stmt)) {
    error_log('[hapusevent] execute check failed: ' . mysqli_stmt_error($check_stmt));
    $_SESSION['flash_error'] = 'Terjadi kesalahan server saat memeriksa event.';
    header("Location: tambah-event.php?status=error");
    exit();
}

mysqli_stmt_store_result($check_stmt);
if (mysqli_stmt_num_rows($check_stmt) === 0) {
    $_SESSION['flash_error'] = 'Event tidak ditemukan.';
    header("Location: tambah-event.php?status=error");
    exit();
}

mysqli_stmt_bind_result($check_stmt, $created_by);
mysqli_stmt_fetch($check_stmt);

if ($created_by != $id_user_login) {
    $_SESSION['flash_error'] = 'Anda tidak berhak menghapus event ini.';
    header("Location: tambah-event.php?status=error");
    exit();
}

// Siapkan penghapusan
$delete_sql = "DELETE FROM events WHERE id_event = ?";
$delete_stmt = mysqli_prepare($conn, $delete_sql);
if ($delete_stmt === false) {
    error_log('[hapusevent] delete prepare failed: ' . mysqli_error($conn));
    $_SESSION['flash_error'] = 'Terjadi kesalahan server saat menyiapkan penghapusan.';
    header("Location: tambah-event.php?status=error");
    exit();
}

mysqli_stmt_bind_param($delete_stmt, "i", $id_event);
if (mysqli_stmt_execute($delete_stmt)) {
    $_SESSION['flash_success'] = 'Event berhasil dihapus.';
    header("Location: tambah-event.php?status=hapus_sukses");
    exit();
} else {
    error_log('[hapusevent] delete execute failed: ' . mysqli_stmt_error($delete_stmt));
    $_SESSION['flash_error'] = 'Gagal menghapus event karena kesalahan server.';
    header("Location: tambah-event.php?status=error");
    exit();
}

mysqli_stmt_close($delete_stmt);
mysqli_stmt_close($check_stmt);
mysqli_close($conn);
