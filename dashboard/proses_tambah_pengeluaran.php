<?php
// FILE: proses_tambah_pengeluaran.php
session_start();
include '../koneksi.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: pengeluaran.php?status=error&pesan=" . urlencode("Akses tidak sah."));
    exit();
}

// --------------------------------------
// 1. Ambil & Bersihkan Data Input
// --------------------------------------
$id_event           = isset($_POST['id_event']) ? (int) $_POST['id_event'] : 0;
$id_kategori        = isset($_POST['id_kategori']) ? (int) $_POST['id_kategori'] : 0;
$status_pembayaran  = isset($_POST['status_pembayaran']) ? trim($_POST['status_pembayaran']) : 'Pending';

$nama_item          = trim($_POST['nama_item'] ?? '');
$tanggal_pengeluaran= trim($_POST['tanggal_pengeluaran'] ?? date('Y-m-d'));

$harga_satuan_str   = trim($_POST['harga_satuan'] ?? '0');
$jumlah             = (int) ($_POST['jumlah'] ?? 1);
$keterangan_tambahan= trim($_POST['keterangan'] ?? '');

// Ambil user login
$created_by         = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 1;

// --------------------------------------
// 2. Format & Hitung Data
// --------------------------------------

// Buat keterangan final
$keterangan_db = $nama_item;
if (!empty($keterangan_tambahan)) {
    $keterangan_db .= " (" . $keterangan_tambahan . ")";
}

// Convert harga satuan
$harga_satuan = floatval(str_replace(['.', ','], ['', '.'], $harga_satuan_str));
$nominal_total = $harga_satuan * $jumlah;

// --------------------------------------
// 3. Validasi Wajib
// --------------------------------------
if (
    $id_event <= 0 ||
    $id_kategori <= 0 ||
    empty($nama_item) ||
    $nominal_total <= 0
) {
    $pesan = "Validasi gagal. Pastikan event, kategori, nama item, dan nominal terisi dengan benar.";
    header("Location: tambah-pengeluaran.php?event_id=$id_event&status=error&pesan=" . urlencode($pesan));
    exit();
}

// --------------------------------------
// 4. Generate ID Pengeluaran (Jika Diperlukan)
// --------------------------------------
// Cek apakah id_pengeluaran AUTO_INCREMENT atau tidak
$check_auto = $conn->query("SHOW COLUMNS FROM pengeluaran LIKE 'id_pengeluaran'");
$column_info = $check_auto->fetch_assoc();
$is_auto_increment = (strpos($column_info['Extra'], 'auto_increment') !== false);

if ($is_auto_increment) {
    // Jika AUTO_INCREMENT, tidak perlu set id_pengeluaran
    $sql = "INSERT INTO pengeluaran 
            (id_event, id_kategori, keterangan, nominal, tanggal, created_by, status_pembayaran)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die("❌ Prepare gagal: " . $conn->error);
    }
    
    $stmt->bind_param(
        "iisdsis",
        $id_event,
        $id_kategori,
        $keterangan_db,
        $nominal_total,
        $tanggal_pengeluaran,
        $created_by,
        $status_pembayaran
    );
} else {
    // Jika TIDAK AUTO_INCREMENT, generate ID manual
    $result = $conn->query("SELECT COALESCE(MAX(id_pengeluaran), 0) + 1 AS next_id FROM pengeluaran");
    $row = $result->fetch_assoc();
    $next_id = $row['next_id'];
    
    $sql = "INSERT INTO pengeluaran 
            (id_pengeluaran, id_event, id_kategori, keterangan, nominal, tanggal, created_by, status_pembayaran)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die("❌ Prepare gagal: " . $conn->error);
    }
    
    $stmt->bind_param(
        "iiisdsis",
        $next_id,
        $id_event,
        $id_kategori,
        $keterangan_db,
        $nominal_total,
        $tanggal_pengeluaran,
        $created_by,
        $status_pembayaran
    );
}

// --------------------------------------
// 5. Eksekusi Query INSERT
// --------------------------------------
if ($stmt->execute()) {
    header("Location: detail_pengeluaran.php?event_id=$id_event&status=success&pesan=" . urlencode("Pengeluaran berhasil ditambahkan."));
    exit();
} else {
    $error_msg = $stmt->error;
    $stmt->close();
    $conn->close();
    
    // Redirect dengan pesan error
    header("Location: tambah-pengeluaran.php?event_id=$id_event&status=error&pesan=" . urlencode("Gagal menambahkan pengeluaran: " . $error_msg));
    exit();
}

$stmt->close();
$conn->close();
?>