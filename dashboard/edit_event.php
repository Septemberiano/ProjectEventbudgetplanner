<?php
session_start();
// Pastikan path koneksi benar.
// Jika file ini ada di folder dashboard, maka naik satu level (../) ke koneksi.php
include_once '../koneksi.php';

// 1. Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// 2. Cek ID Event di URL
if (!isset($_GET['id'])) {
    // Jika tidak ada ID, kembalikan ke dashboard
    header("Location: index.php");
    exit();
}

$id_event = $_GET['id'];

// 3. Ambil Data dari Database
$sql = "SELECT * FROM events WHERE id_event = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_event);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

// Jika event tidak ditemukan
if (!$event) {
    die("Data Event tidak ditemukan di database.");
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Event - Rekaduit</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/favic-reka.PNG" />
    <link rel="stylesheet" href="./assets/css/styles.min.css" />
    
    <style>
        /* CSS Fix agar tampilan tidak tertutup header */
        .app-header { margin-top: -69px; }
        .app-header[style] { top: calc(var(--bs-nav-top, 0px) - 8px) !important; }
        .left-sidebar { transform: translateY(-64px); }
    </style>
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

        <aside class="left-sidebar">
            <div>
                <div class="brand-logo d-flex align-items-center justify-content-between">
                    <a href="./index.php" class="text-nowrap logo-img">
                        <img src="../assets/images/logoreka.PNG" alt="logoreka" width="200px">
                    </a>
                    <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                        <i class="ti ti-x fs-6"></i>
                    </div>
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
                            <a class="sidebar-link" href="tambah-event.php" aria-expanded="false">
                                <i class="ti ti-aperture"></i>
                                <span class="hide-menu">Event</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="pengeluaran.php" aria-expanded="false">
                                <i class="ti ti-shopping-cart"></i>
                                <span class="hide-menu">Pengeluaran</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>
        <div class="body-wrapper">
            <header class="app-header">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <ul class="navbar-nav">
                        <li class="nav-item d-block d-xl-none">
                            <a class="nav-link sidebartoggler" id="headerCollapse" href="javascript:void(0)">
                                <i class="ti ti-menu-2"></i>
                            </a>
                        </li>
                    </ul>
                    <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
                        <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                            <li class="nav-item">
                                <a class="btn btn-outline-primary" href="../logout.php">Logout</a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>
            <div class="body-wrapper-inner">
                <div class="container-fluid">
                    
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title fw-semibold mb-4">✏️ Edit Event</h5>
                            <p class="text-muted mb-4">Ubah detail acara di bawah ini.</p>

                            <form method="POST" action="proses-editevent.php">
                                <input type="hidden" name="id_event" value="<?php echo $event['id_event']; ?>">

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nama Event *</label>
                                        <input type="text" class="form-control" name="nama_event" 
                                               value="<?php echo htmlspecialchars($event['nama_event']); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Tanggal Mulai *</label>
                                        <input type="date" class="form-control" name="tanggal_mulai" 
                                               value="<?php echo $event['tanggal_mulai']; ?>" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Tanggal Selesai</label>
                                        <input type="date" class="form-control" name="tanggal_selesai" 
                                               value="<?php echo $event['tanggal_selesai']; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Lokasi *</label>
                                        <input type="text" class="form-control" name="lokasi" 
                                               value="<?php echo htmlspecialchars($event['lokasi']); ?>" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Deskripsi *</label>
                                    <textarea class="form-control" name="deskripsi" rows="4" required><?php echo htmlspecialchars($event['deskripsi']); ?></textarea>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Total Anggaran</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" step="0.01" class="form-control" name="total_anggaran" 
                                               value="<?php echo $event['total_anggaran']; ?>">
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <a href="tambah-event.php" class="btn btn-outline-secondary me-2">Batal</a>
                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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
    <script src="./assets/libs/simplebar/dist/simplebar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
</body>
</html>