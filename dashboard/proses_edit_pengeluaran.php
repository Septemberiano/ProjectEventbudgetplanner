<?php
// FILE: proses_edit_pengeluaran.php
session_start();
include '../koneksi.php'; // PASTIKAN PATH KONEKSI SUDAH BENAR!

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Ambil dan Bersihkan Data Input
    $id_pengeluaran     = isset($_POST['id_pengeluaran']) ? (int) $_POST['id_pengeluaran'] : 0;
    $id_event           = isset($_POST['id_event']) ? (int) $_POST['id_event'] : 0;
    $id_kategori        = isset($_POST['id_kategori']) ? (int) $_POST['id_kategori'] : 0;
    $status_pembayaran  = isset($_POST['status_pembayaran']) ? trim($_POST['status_pembayaran']) : 'Pending';

    $nama_item          = isset($_POST['nama_item']) ? trim($_POST['nama_item']) : '';
    $tanggal_pengeluaran= isset($_POST['tanggal_pengeluaran']) ? trim($_POST['tanggal_pengeluaran']) : '';
    $harga_satuan_str   = isset($_POST['harga_satuan']) ? trim($_POST['harga_satuan']) : '0';
    $jumlah             = isset($_POST['jumlah']) ? (int) $_POST['jumlah'] : 1;
    
    $keterangan_db      = isset($_POST['nama_item']) ? trim($_POST['nama_item']) : '';
    
    // Ambil ID user yang login
    $created_by = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 1; 

    // VALIDASI DAN FORMAT TANGGAL
    if (empty($tanggal_pengeluaran)) {
        // Jika kosong, gunakan tanggal hari ini
        $tanggal_db = date('Y-m-d');
    } else {
        // Validasi format tanggal
        $date_parts = explode('-', $tanggal_pengeluaran);
        
        // Cek apakah format valid (YYYY-MM-DD)
        if (count($date_parts) === 3 && checkdate($date_parts[1], $date_parts[2], $date_parts[0])) {
            $tanggal_db = $tanggal_pengeluaran;
        } else {
            // Jika format tidak valid, coba parsing ulang
            $timestamp = strtotime($tanggal_pengeluaran);
            if ($timestamp !== false) {
                $tanggal_db = date('Y-m-d', $timestamp);
            } else {
                // Jika tetap gagal, gunakan tanggal hari ini
                $tanggal_db = date('Y-m-d');
            }
        }
    }

    // HITUNG NOMINAL TOTAL (Harga Satuan * Jumlah)
    $harga_satuan = (float) str_replace(['.', ','], ['', '.'], $harga_satuan_str);
    $nominal_total = $harga_satuan * $jumlah; 

    // 2. Validasi Kritis
    if ($id_pengeluaran <= 0 || $id_event <= 0 || $id_kategori <= 0 || empty($nama_item) || $nominal_total <= 0) {
        $pesan = "Validasi Gagal. Pastikan semua data wajib diisi dengan benar.";
        $tipe = "error";
        header("Location: edit_pengeluaran.php?id=$id_pengeluaran&status=$tipe&pesan=" . urlencode($pesan));
        exit();
    }
    
    // 3. Persiapan dan Eksekusi Query UPDATE
    $sql = "UPDATE pengeluaran SET 
                id_event = ?, 
                id_kategori = ?, 
                keterangan = ?, 
                nominal = ?, 
                tanggal = ?, 
                created_by = ?, 
                status_pembayaran = ? 
            WHERE id_pengeluaran = ?";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die("❌ Prepare Error: " . $conn->error);
    }
    
    // Tipe data: i, i, s, d, s, i, s, i 
    $stmt->bind_param("iisdsisi", 
        $id_event, 
        $id_kategori, 
        $keterangan_db, 
        $nominal_total, 
        $tanggal_db, 
        $created_by, 
        $status_pembayaran, 
        $id_pengeluaran
    );
    
    if ($stmt->execute()) {
        $pesan = "Pengeluaran berhasil diperbarui.";
        $tipe = "success";
    } else {
        // Simpan error untuk debugging
        $error_msg = $stmt->error;
        $stmt->close();
        $conn->close();
        
        // Redirect dengan pesan error
        header("Location: edit_pengeluaran.php?id=$id_pengeluaran&status=error&pesan=" . urlencode("Database Error: " . $error_msg));
        exit();
    }
    
    $stmt->close();
    $conn->close();
    
    // 4. Alihkan kembali ke halaman detail pengeluaran event yang bersangkutan
    header("Location: detail_pengeluaran.php?event_id=$id_event&status=$tipe&pesan=" . urlencode($pesan));
    exit();

} else {
    // Jika diakses tanpa method POST
    header("Location: pengeluaran.php?status=error&pesan=" . urlencode("Akses tidak sah."));
    exit();
}
?>