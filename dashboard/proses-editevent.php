<?php
session_start();
include_once '../koneksi.php';

// Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil Data
    $id_event       = $_POST['id_event'];
    $nama_event     = trim($_POST['nama_event']);
    $tanggal_mulai  = $_POST['tanggal_mulai'];
    $tanggal_selesai = empty($_POST['tanggal_selesai']) ? NULL : $_POST['tanggal_selesai'];
    $lokasi         = trim($_POST['lokasi']);
    $deskripsi      = trim($_POST['deskripsi']);
    $total_anggaran = empty($_POST['total_anggaran']) ? 0 : (float)$_POST['total_anggaran'];

    // Validasi
    if (empty($nama_event) || empty($tanggal_mulai) || empty($lokasi)) {
        header("Location: edit_event.php?id=$id_event&status=error_empty");
        exit();
    }

    // Update Query
    $sql = "UPDATE events SET 
            nama_event = ?, 
            tanggal_mulai = ?, 
            tanggal_selesai = ?, 
            lokasi = ?, 
            deskripsi = ?, 
            total_anggaran = ? 
            WHERE id_event = ?";

    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        // Tipe data: s=string, d=double, i=int
        $stmt->bind_param("sssssdi", $nama_event, $tanggal_mulai, $tanggal_selesai, $lokasi, $deskripsi, $total_anggaran, $id_event);

        if ($stmt->execute()) {
            // Sukses
            header("Location: tambah-event.php?status=update_sukses");
        } else {
            // Gagal
            header("Location: edit_event.php?id=$id_event&status=error_db");
        }
        $stmt->close();
    } else {
        echo "Error Prepare: " . $conn->error;
    }
    
    $conn->close();
} else {
    header("Location: index.php");
}
?>