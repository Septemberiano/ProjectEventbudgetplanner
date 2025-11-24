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
    $tanggal_pengeluaran= isset($_POST['tanggal_pengeluaran']) ? trim($_POST['tanggal_pengeluaran']) : date('Y-m-d');
    $harga_satuan_str   = isset($_POST['harga_satuan']) ? trim($_POST['harga_satuan']) : '0';
    $jumlah             = isset($_POST['jumlah']) ? (int) $_POST['jumlah'] : 1;
    // Pada formulir edit, saya menggunakan kolom keterangan_db yang diisi dari nama_item 
    // karena database Anda hanya memiliki satu kolom 'keterangan'.
    $keterangan_db      = isset($_POST['nama_item']) ? trim($_POST['nama_item']) : '';
    $tanggal_db         = $tanggal_pengeluaran; 
    
    // Ambil ID user yang login
    // Asumsi: Anda menggunakan $_SESSION['user_id']
    $created_by = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 1; 

    // HITUNG NOMINAL TOTAL (Harga Satuan * Jumlah)
    // Asumsi input type="number" tidak memiliki pemisah ribuan
    $harga_satuan = (float) $harga_satuan_str; 
    $nominal_total = $harga_satuan * $jumlah; 

    // 2. Validasi Kritis
    if ($id_pengeluaran <= 0 || $id_event <= 0 || $id_kategori <= 0 || empty($nama_item) || $nominal_total <= 0) {
        $pesan = "Validasi Gagal. Pastikan semua data wajib diisi dengan benar.";
        $tipe = "error";
        // Redirect kembali ke halaman edit
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
    
    // Tipe data: i, i, s, d, s, i, s, i 
    // Urutan: id_event, id_kategori, keterangan, nominal, tanggal, created_by, status_pembayaran, id_pengeluaran
    $stmt->bind_param("iissdisi", 
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
        // Tampilkan error database jika gagal
        die("❌ Database Error: " . $stmt->error); 
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