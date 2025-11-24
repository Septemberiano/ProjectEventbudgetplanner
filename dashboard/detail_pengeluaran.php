<?php
include '../koneksi.php';

// Fungsi untuk memformat Rupiah (diambil dari pengeluaran.php)
function formatRupiah($angka)
{
    if (!is_numeric($angka)) {
        $angka = 0;
    }
    return 'Rp ' . number_format($angka, 2, ',', '.');
}

$event_id = 0;
$nama_event = "Detail Pengeluaran";
$pengeluaran_list = [];
$total_pengeluaran_event = 0;
$kategori_list = []; // Untuk mengambil nama kategori

// 1. Ambil ID Event dari URL
if (isset($_GET['event_id']) && is_numeric($_GET['event_id'])) {
    $event_id = (int) $_GET['event_id'];

    // 2. Ambil Nama Event untuk Judul
    $sql_event = "SELECT nama_event FROM events WHERE id_event = ?";
    $stmt_event = $conn->prepare($sql_event);
    $stmt_event->bind_param("i", $event_id);
    $stmt_event->execute();
    $result_event = $stmt_event->get_result();

    if ($result_event->num_rows > 0) {
        $data_event = $result_event->fetch_assoc();
        $nama_event = $data_event['nama_event'];
    }
    $stmt_event->close();

    // 3. Ambil Daftar Kategori (Asumsi nama tabel kategori adalah 'kategori')
    $sql_kategori = "SELECT id_kategori, nama_kategori FROM kategori";
    $result_kategori = $conn->query($sql_kategori);
    while ($row = $result_kategori->fetch_assoc()) {
        $kategori_list[$row['id_kategori']] = $row['nama_kategori'];
    }

    // 4. Ambil Daftar Pengeluaran untuk Event ini
    // Menggunakan JOIN untuk menampilkan nama kategori
    $sql_pengeluaran = "
        SELECT 
            p.id_pengeluaran, 
            p.id_kategori, 
            p.keterangan, 
            p.nominal, 
            p.tanggal, 
            p.status_pembayaran
        FROM 
            pengeluaran p
        WHERE 
            p.id_event = ?
        ORDER BY 
            p.tanggal DESC
    ";

    $stmt_pengeluaran = $conn->prepare($sql_pengeluaran);
    $stmt_pengeluaran->bind_param("i", $event_id);
    $stmt_pengeluaran->execute();
    $result_pengeluaran = $stmt_pengeluaran->get_result();

    if ($result_pengeluaran->num_rows > 0) {
        while ($row = $result_pengeluaran->fetch_assoc()) {
            $total_pengeluaran_event += (float) $row['nominal'];
            $pengeluaran_list[] = $row;
        }
    }
    $stmt_pengeluaran->close();
} else {
    // Jika tidak ada ID Event, alihkan kembali
    header("Location: pengeluaran.php?status=error&pesan=" . urlencode("ID Event tidak ditemukan."));
    exit();
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($nama_event); ?> - Detail Pengeluaran</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/favic-reka.PNG" />
    <link rel="stylesheet" href="./assets/css/styles.min.css" />
    <style>
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
        </aside>
        <div class="body-wrapper">
            <header class="app-header">
            </header>
            <div class="body-wrapper-inner">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title fw-semibold mb-4">Detail Pengeluaran: <?php echo htmlspecialchars($nama_event); ?></h5>
                            <p class="text-muted mb-4">Total Pengeluaran Saat Ini: **<?php echo formatRupiah($total_pengeluaran_event); ?>**</p>

                            <a href="pengeluaran.php" class="btn btn-secondary mb-3 me-2">
                                <i class="ti ti-arrow-left me-1"></i> Kembali ke Daftar Event
                            </a>
                            <a href="tambah-pengeluaran.php?event_id=<?php echo $event_id; ?>" class="btn btn-primary mb-3">
                                <i class="ti ti-plus me-1"></i> Tambah Pengeluaran Baru
                            </a>

                            <?php if (isset($_GET['status']) && isset($_GET['pesan'])): ?>
                                <div class="alert alert-<?php echo ($_GET['status'] == 'success' ? 'success' : 'danger'); ?> alert-dismissible fade show" role="alert">
                                    <?php echo htmlspecialchars(urldecode($_GET['pesan'])); ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <div class="table-responsive">
                                <table class="table table-hover text-nowrap align-middle">
                                    <thead>
                                        <tr class="text-dark">
                                            <th scope="col">Tanggal</th>
                                            <th scope="col">Keterangan</th>
                                            <th scope="col">Kategori</th>
                                            <th scope="col" class="text-end">Nominal</th>
                                            <th scope="col">Status Bayar</th>
                                            <th scope="col" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($pengeluaran_list)): ?>
                                            <?php foreach ($pengeluaran_list as $pengeluaran): ?>
                                                <tr>
                                                    <td><?php echo date('d M Y', strtotime($pengeluaran['tanggal'])); ?></td>
                                                    <td><?php echo htmlspecialchars($pengeluaran['keterangan']); ?></td>
                                                    <td><?php echo htmlspecialchars($kategori_list[$pengeluaran['id_kategori']] ?? 'Tidak Diketahui'); ?></td>
                                                    <td class="text-end text-warning fw-semibold"><?php echo formatRupiah($pengeluaran['nominal']); ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php echo ($pengeluaran['status_pembayaran'] == 'Lunas' ? 'success' : 'warning'); ?>">
                                                            <?php echo htmlspecialchars($pengeluaran['status_pembayaran']); ?>
                                                        </span>
                                                    </td>
                                                        
                                                    <td class="text-center">
                                                        <a href="edit_pengeluaran.php?id=<?php echo $pengeluaran['id_pengeluaran']; ?>"
                                                            class="btn btn-sm btn-warning me-1">
                                                            <i class="ti ti-pencil me-1"></i> Edit
                                                        </a>
                                                        <a href="hapus_pengeluaran.php?id=<?php echo $pengeluaran['id_pengeluaran']; ?>"
                                                            class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Yakin ingin menghapus pengeluaran ini: <?php echo htmlspecialchars($pengeluaran['keterangan']); ?>?');">
                                                            <i class="ti ti-trash me-1"></i> Hapus
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">Belum ada pengeluaran untuk event ini.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>