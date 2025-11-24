<?php
include 'koneksi.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_event = (int) $_GET['id'];
    $conn->begin_transaction();

    try {
        $sql_delete_pengeluaran = "DELETE FROM pengeluaran WHERE id_event = ?";
        $stmt_pengeluaran = $conn->prepare($sql_delete_pengeluaran);
        $stmt_pengeluaran->bind_param("i", $id_event);
        if (!$stmt_pengeluaran->execute()) {
            throw new Exception("Gagal menghapus pengeluaran terkait.");
        }
        $stmt_pengeluaran->close();

        $sql_delete_event = "DELETE FROM events WHERE id_event = ?";
        $stmt_event = $conn->prepare($sql_delete_event);
        $stmt_event->bind_param("i", $id_event);
        if (!$stmt_event->execute()) {
            throw new Exception("Gagal menghapus event utama.");
        }
        $stmt_event->close();

        $conn->commit();
        $pesan = "Event dan semua pengeluaran terkait berhasil dihapus.";
        $tipe = "success";
    } catch (Exception $e) {
        $conn->rollback();
        $pesan = "Gagal menghapus event. Error: " . $e->getMessage();
        $tipe = "error";
    }

    header("Location: pengeluaran.php?status=$tipe&pesan=" . urlencode($pesan));
    exit();
} else {
    header("Location: pengeluaran.php?status=error&pesan=" . urlencode("ID Event tidak valid atau tidak ditemukan."));
    exit();
}
