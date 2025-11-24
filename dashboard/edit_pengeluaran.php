<?php
// FILE: edit_pengeluaran.php
session_start();
include '../koneksi.php';

// Fungsi bantuan untuk format Rupiah (diambil dari file sebelumnya)
function formatRupiah($angka)
{
    if (!is_numeric($angka)) {
        $angka = 0;
    }
    return 'Rp ' . number_format($angka, 2, ',', '.');
}

// 1. Ambil ID Pengeluaran dari URL dan Validasi
if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: pengeluaran.php?status=error&pesan=" . urlencode("ID Pengeluaran tidak valid."));
    exit();
}

$id_pengeluaran = (int) $_GET['id'];

// 2. Ambil Data Pengeluaran untuk Diedit
$sql_pengeluaran = "SELECT * FROM pengeluaran WHERE id_pengeluaran = ?";
$stmt_pengeluaran = $conn->prepare($sql_pengeluaran);
$stmt_pengeluaran->bind_param("i", $id_pengeluaran);
$stmt_pengeluaran->execute();
$result_pengeluaran = $stmt_pengeluaran->get_result();

if ($result_pengeluaran->num_rows === 0) {
    die("Data Pengeluaran tidak ditemukan.");
}

$data_pengeluaran = $result_pengeluaran->fetch_assoc();
$stmt_pengeluaran->close();

$event_id_target = $data_pengeluaran['id_event'];
$keterangan_db = htmlspecialchars($data_pengeluaran['keterangan']);
$nominal_db = $data_pengeluaran['nominal'];


// ASUMSI: Karena database tidak menyimpan harga_satuan dan jumlah, 
// kita asumsikan harga satuan = nominal dan jumlah = 1 untuk pre-filling form.
$harga_satuan_prefill = $nominal_db;
$jumlah_prefill = 1;

// 3. Ambil Detail Event (untuk Header)
$sql_event = "SELECT nama_event, total_anggaran FROM events WHERE id_event = ?";
$stmt_event = $conn->prepare($sql_event);
$stmt_event->bind_param("i", $event_id_target);
$stmt_event->execute();
$result_event = $stmt_event->get_result();
$event = $result_event->fetch_assoc();
$stmt_event->close();

$nama_event = htmlspecialchars($event['nama_event'] ?? 'Event Tidak Ditemukan');
$total_anggaran = $event['total_anggaran'] ?? 0;


// 4. Ambil Daftar Kategori (untuk Dropdown)
$sql_kategori = "SELECT id_kategori, nama_kategori FROM kategori ORDER BY nama_kategori ASC";
$result_kategori = mysqli_query($conn, $sql_kategori);
$kategori_list = mysqli_fetch_all($result_kategori, MYSQLI_ASSOC);

$conn->close();
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Pengeluaran Event</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/favic-reka.PNG" />
    <link rel="stylesheet" href="./assets/css/styles.min.css" />
    <style>
        /* Gaya CSS dashboard Anda */
        .app-header {
            margin-top: -69px;
        }

        .app-header[style] {
            top: calc(var(--bs-nav-top, 0px) - 8px) !important;
        }

        .left-sidebar {
            transform: translateY(-64px);
        }
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
                            <h5 class="card-title fw-semibold mb-2">Edit Pengeluaran</h5>
                            <p class="mb-4 text-muted">Mengedit data: **<?php echo $keterangan_db; ?>**</p>

                            <div class="alert alert-info d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <p class="mb-0 fw-bold">Event: <?php echo $nama_event; ?></p>
                                </div>
                                <h4 class="mb-0 text-dark">Anggaran: <?php echo formatRupiah($total_anggaran); ?></h4>
                            </div>

                            <form method="POST" action="proses_edit_pengeluaran.php">
                                <input type="hidden" name="id_pengeluaran" value="<?php echo $id_pengeluaran; ?>">
                                <input type="hidden" name="id_event" value="<?php echo $event_id_target; ?>">

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="nama_item" class="form-label">Nama Item Pengeluaran *</label>
                                        <input type="text" class="form-control" id="nama_item" name="nama_item" required
                                            value="<?php echo $keterangan_db; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="tanggal_pengeluaran" class="form-label">Tanggal Pengeluaran *</label>
                                        <input type="date" class="form-control" id="tanggal_pengeluaran" name="tanggal_pengeluaran" required
                                            value="<?php echo htmlspecialchars($data_pengeluaran['tanggal']); ?>">
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label for="harga_satuan" class="form-label">Harga Satuan *</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" step="0.01" class="form-control" id="harga_satuan" name="harga_satuan" required placeholder="Contoh: 50000"
                                                value="<?php echo $harga_satuan_prefill; ?>">
                                        </div>
                                        <small class="form-text text-muted">Nilai awal diisi dengan nominal total, silakan sesuaikan kuantitas dan harga satuan jika perlu.</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="jumlah" class="form-label">Jumlah (Qty) *</label>
                                        <input type="number" class="form-control" id="jumlah" name="jumlah" required min="1"
                                            value="<?php echo $jumlah_prefill; ?>">
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label for="id_kategori" class="form-label">Kategori Pengeluaran *</label>
                                        <select class="form-select" id="id_kategori" name="id_kategori" required>
                                            <option value="">Pilih Kategori</option>
                                            <?php foreach ($kategori_list as $kategori): ?>
                                                <option value="<?php echo $kategori['id_kategori']; ?>"
                                                    <?php echo ($kategori['id_kategori'] == $data_pengeluaran['id_kategori']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($kategori['nama_kategori']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="status_pembayaran" class="form-label">Status Pembayaran *</label>
                                        <select class="form-select" id="status_pembayaran" name="status_pembayaran" required>
                                            <option value="Lunas" <?php echo ($data_pengeluaran['status_pembayaran'] == 'Lunas') ? 'selected' : ''; ?>>Lunas</option>
                                            <option value="Pending" <?php echo ($data_pengeluaran['status_pembayaran'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="keterangan" class="form-label">Keterangan Tambahan</label>
                                    <textarea class="form-control" id="keterangan" name="keterangan" rows="3"><?php echo htmlspecialchars($data_pengeluaran['keterangan']); ?></textarea>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="detail_pengeluaran.php?event_id=<?php echo $event_id_target; ?>" class="btn btn-outline-secondary">
                                        <i class="ti ti-arrow-back me-1"></i> Batal / Kembali
                                    </a>
                                    <button type="submit" class="btn btn-warning">
                                        <i class="ti ti-device-floppy me-1"></i> Simpan Perubahan
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