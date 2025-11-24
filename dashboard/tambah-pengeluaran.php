<?php
// Panggil file koneksi database
include '../koneksi.php';

// Pastikan event_id diterima dari URL (misal: tambah-pengeluaran.php?event_id=1)
if (!isset($_GET['event_id']) || empty($_GET['event_id'])) {
    // Jika tidak ada ID, alihkan kembali ke halaman daftar pengeluaran
    header("Location: pengeluaran.php");
    exit();
}

$event_id = mysqli_real_escape_string($conn, $_GET['event_id']);

// Query untuk mengambil detail event yang dipilih
$sql_event = "SELECT id_event, nama_event, total_anggaran FROM events WHERE id_event = '$event_id'";
$result_event = mysqli_query($conn, $sql_event);

if (mysqli_num_rows($result_event) == 0) {
    // Jika Event tidak ditemukan
    die("Event tidak ditemukan.");
}

$event = mysqli_fetch_assoc($result_event);
$nama_event = htmlspecialchars($event['nama_event']);
$total_anggaran = $event['total_anggaran'];

// Fungsi bantuan untuk format Rupiah
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 2, ',', '.');
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Pengeluaran Event</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/favic-reka.PNG" />
    <link rel="stylesheet" href="./assets/css/styles.min.css" />
    <style>
        /* Gaya CSS dashboard Anda */
        .app-header { margin-top: -69px; }
        .app-header[style] { top: calc(var(--bs-nav-top, 0px) - 8px) !important; }
        .left-sidebar { transform: translateY(-64px); }
    </style>
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

        <aside class="left-sidebar">
            <div class="brand-logo d-flex align-items-center justify-content-between">
                <a href="./index.php" class="text-nowrap logo-img">
                    <img src="../assets/images/logoreka.PNG" alt="logoreka" width="200px">
                </a>
            </div>
            <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
                <ul id="sidebarnav">
                    <li class="nav-small-cap">
                        <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
                        <span class="hide-menu">Home</span>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="./index.php" aria-expanded="false">
                            <i class="ti ti-atom"></i>
                            <span class="hide-menu">Dashboard</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link justify-content-between" href="pengeluaran.php" aria-expanded="false">
                            <div class="d-flex align-items-center gap-3">
                                <span class="d-flex">
                                    <i class="ti ti-aperture"></i>
                                </span>
                                <span class="hide-menu">Pengeluaran Event</span>
                            </div>
                        </a>
                    </li>
                    </ul>
            </nav>
            </aside>
        <div class="body-wrapper">
            <header class="app-header">
                </header>
            <div class="body-wrapper-inner">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title fw-semibold mb-2">Tambah Pengeluaran Baru</h5>
                            
                            <div class="alert alert-info d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <p class="mb-0 fw-bold">Event: <?php echo $nama_event; ?></p>
                                    <small>ID Event: <?php echo $event['id_event']; ?></small>
                                </div>
                                <h4 class="mb-0 text-dark">Anggaran: <?php echo formatRupiah($total_anggaran); ?></h4>
                            </div>

                            <form method="POST" action="proses_tambah_pengeluaran.php">
                                
                                <input type="hidden" name="id_event" value="<?php echo $event['id_event']; ?>">

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="nama_item" class="form-label">Nama Item Pengeluaran *</label>
                                        <input type="text" class="form-control" id="nama_item" name="nama_item" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="tanggal_pengeluaran" class="form-label">Tanggal Pengeluaran *</label>
                                        <input type="date" class="form-control" id="tanggal_pengeluaran" name="tanggal_pengeluaran" required>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label for="harga_satuan" class="form-label">Harga Satuan *</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control" id="harga_satuan" name="harga_satuan" required placeholder="Contoh: 50000">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="jumlah" class="form-label">Jumlah (Qty) *</label>
                                        <input type="number" class="form-control" id="jumlah" name="jumlah" required min="1" value="1">
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="keterangan" class="form-label">Keterangan Tambahan</label>
                                    <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="pengeluaran.php" class="btn btn-outline-secondary">
                                        <i class="ti ti-arrow-back me-1"></i> Kembali ke Daftar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-device-floppy me-1"></i> Simpan Pengeluaran
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebarmenu.js"></script>
    <script src="./assets/js/app.min.js"></script>
    <script src="./assets/libs/apexcharts/dist/apexcharts.min.js"></script>
    <script src="./assets/libs/simplebar/dist/simplebar.js"></script>
    <script src="./assets/js/dashboard.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
</body>

</html>