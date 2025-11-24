<?php
// FILE: hapus_pengeluaran.php
session_start();
include '../koneksi.php'; // PASTIKAN PATH KONEKSI SUDAH BENAR!

// 1. Validasi ID Pengeluaran
if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    $pesan = "ID Pengeluaran tidak valid atau tidak ditemukan.";
    header("Location: pengeluaran.php?status=error&pesan=" . urlencode($pesan));
    exit();
}

$id_pengeluaran = (int) $_GET['id'];
$id_event = 0; // Variabel untuk menyimpan ID Event tujuan redirect

// Mulai transaksi untuk memastikan konsistensi (opsional, tapi disarankan)
$conn->begin_transaction();

try {
    // 2. AMBIL ID EVENT TERKAIT SEBELUM DATA DIHAPUS
    $sql_get_event_id = "SELECT id_event FROM pengeluaran WHERE id_pengeluaran = ?";
    $stmt_get_id = $conn->prepare($sql_get_event_id);
    $stmt_get_id->bind_param("i", $id_pengeluaran);
    $stmt_get_id->execute();
    $result_id = $stmt_get_id->get_result();

    if ($result_id->num_rows === 0) {
        throw new Exception("Data pengeluaran tidak ditemukan di database.");
    }
    
    $data = $result_id->fetch_assoc();
    $id_event = $data['id_event']; // Simpan ID Event untuk redirect
    $stmt_get_id->close();


    // 3. Eksekusi Query DELETE
    $sql_delete = "DELETE FROM pengeluaran WHERE id_pengeluaran = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $id_pengeluaran);

    if (!$stmt_delete->execute()) {
        throw new Exception("Gagal menghapus pengeluaran.");
    }
    $stmt_delete->close();
    
    // 4. Commit Transaksi dan Set Pesan Sukses
    $conn->commit();
    $pesan = "Pengeluaran berhasil dihapus.";
    $tipe = "success";

} catch (Exception $e) {
    // Rollback jika ada kesalahan
    $conn->rollback();
    $pesan = "Gagal menghapus pengeluaran. Error: " . $e->getMessage();
    $tipe = "error";
    
    // Jika ID Event tidak ditemukan di awal, redirect ke daftar utama
    if ($id_event === 0) {
        header("Location: pengeluaran.php?status=$tipe&pesan=" . urlencode($pesan));
        exit();
    }
}

$conn->close();

// 5. Redirect ke halaman detail event yang bersangkutan
header("Location: detail_pengeluaran.php?event_id=$id_event&status=$tipe&pesan=" . urlencode($pesan));
exit();
?>