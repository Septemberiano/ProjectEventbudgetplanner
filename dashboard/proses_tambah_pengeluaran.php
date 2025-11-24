<?php
// FILE: proses_tambah_pengeluaran.php (SKRIP INSERT YANG BENAR)
session_start();
include '../koneksi.php'; // PASTIKAN PATH INI BENAR!
 if (isset($_GET['status']) && $_GET['status'] == 'error'): ?>
    <div class="alert alert-danger"><?php echo urldecode($_GET['pesan']); ?></div>
<?php endif; 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Ambil dan Bersihkan Data Input DARI FORM YANG BARU
    $id_event           = isset($_POST['id_event']) ? (int) $_POST['id_event'] : 0;
    
    // Field baru: id_kategori & status_pembayaran
    $id_kategori        = isset($_POST['id_kategori']) ? (int) $_POST['id_kategori'] : 0;
    $status_pembayaran  = isset($_POST['status_pembayaran']) ? trim($_POST['status_pembayaran']) : 'Pending';

    // Field form Anda:
    $nama_item          = isset($_POST['nama_item']) ? trim($_POST['nama_item']) : '';
    $tanggal_pengeluaran= isset($_POST['tanggal_pengeluaran']) ? trim($_POST['tanggal_pengeluaran']) : date('Y-m-d');
    $harga_satuan_str   = isset($_POST['harga_satuan']) ? trim($_POST['harga_satuan']) : '0';
    $jumlah             = isset($_POST['jumlah']) ? (int) $_POST['jumlah'] : 1;
    $keterangan_tambahan= isset($_POST['keterangan']) ? trim($_POST['keterangan']) : NULL;
    
    // Mapping ke Kolom Database:
    $keterangan_db = empty($keterangan_tambahan) ? $nama_item : $nama_item . ' (' . $keterangan_tambahan . ')';
    $tanggal_db = $tanggal_pengeluaran; 
    
    // Ambil ID user yang login (Asumsi id user disimpan di session)
    $created_by = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 1; 

    // HITUNG NOMINAL TOTAL (Harga Satuan * Jumlah)
    $harga_satuan = (float) str_replace(['.', ','], ['', '.'], $harga_satuan_str); 
    $nominal_total = $harga_satuan * $jumlah; 

    // 2. Validasi Kritis
    if ($id_event <= 0 || $id_kategori <= 0 || empty($nama_item) || $nominal_total <= 0) {
        $pesan = "Validasi Gagal. Pastikan Event, Kategori, Nama Item, dan Nominal terisi dengan benar.";
        $tipe = "error";
        header("Location: tambah-pengeluaran.php?event_id=$id_event&status=$tipe&pesan=" . urlencode($pesan));
        exit();
    }
    
    // 3. Persiapan dan Eksekusi Query INSERT
    // Kolom bukti diabaikan untuk sementara, diasumsikan NULL
    $sql = "INSERT INTO pengeluaran (id_event, id_kategori, keterangan, nominal, tanggal, created_by, status_pembayaran) VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    
    // Tipe data: i (id_event), i (id_kategori), s (keterangan), d (nominal), s (tanggal), i (created_by), s (status_pembayaran)
    $stmt->bind_param("iisdsis", $id_event, $id_kategori, $keterangan_db, $nominal_total, $tanggal_db, $created_by, $status_pembayaran);
    if ($stmt->execute()) {
        $pesan = "Pengeluaran baru berhasil ditambahkan.";
        $tipe = "success";
        header("Location: detail_pengeluaran.php?event_id=$id_event&status=$tipe&pesan=" . urlencode($pesan));
    } else {
        // TAMPILKAN ERROR DATABASE UTAMA
        die("❌ Database Error: " . $stmt->error); 
    }
    
    $stmt->close();
    $conn->close();
    
} else {
    // Jika diakses tanpa method POST
    header("Location: pengeluaran.php?status=error&pesan=" . urlencode("Akses tidak sah."));
    exit();
}
?>