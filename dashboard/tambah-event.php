<?php

include_once'../koneksi.php';

$sql = "SELECT id_event, nama_event, tanggal_mulai, lokasi, total_anggaran FROM events ORDER BY tanggal_mulai DESC";
$result = mysqli_query($conn, $sql);


function formatRupiah($angka) {
   
    if (is_numeric($angka)) {
        return 'Rp ' . number_format($angka, 2, ',', '.');
    }
    return 'Rp 0.00'; 
}


$events = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Event</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/favic-reka.PNG" />
    <link rel="stylesheet" href="./assets/css/styles.min.css" />
    <style>
        /* Minor tweak: shift top navbar up a bit to reduce empty space */
        .app-header {
            margin-top: -69px;
        }

        /* if header uses top positioning, nudge it up slightly */
        .app-header[style] {
            top: calc(var(--bs-nav-top, 0px) - 8px) !important;
        }

        /* Nudge the sidebar up slightly so it aligns with header */
        .left-sidebar {
            transform: translateY(-64px);
        }
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
                            <a class="sidebar-link justify-content-between" href="tambah-event.php" aria-expanded="false">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="d-flex">
                                        <i class="ti ti-aperture"></i>
                                    </span>
                                    <span class="hide-menu">Event</span>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link justify-content-between" href="pengeluaran.php" aria-expanded="false">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="d-flex">
                                        <i class="ti ti-shopping-cart"></i>
                                    </span>
                                    <span class="hide-menu">Pengeluaran</span>
                                </div>
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
                            <a class="nav-link sidebartoggler " id="headerCollapse" href="javascript:void(0)">
                                <i class="ti ti-menu-2"></i>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link " href="javascript:void(0)" id="drop1" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="ti ti-bell"></i>
                                <div class="notification bg-primary rounded-circle"></div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-animate-up" aria-labelledby="drop1">
                                <div class="message-body">
                                    <a href="javascript:void(0)" class="dropdown-item">
                                        Item 1
                                    </a>
                                    <a href="javascript:void(0)" class="dropdown-item">
                                        Item 2
                                    </a>
                                </div>
                            </div>
                        </li>
                    </ul>
                    <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
                        <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end"
                            style="position: relative;">

                            <li class="nav-item dropdown">
                                <a class="nav-link " href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <p class="mb-2 fs-3 mt-2 ">Halo </p>
                                    <img src="./assets/images/profile/user-1.jpg" alt="" width="35" height="35"
                                        class="rounded-circle">
                                </a>
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up"
                                    aria-labelledby="drop2">
                                    <div class="message-body">
                                        <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                                            <i class="ti ti-user fs-6"></i>
                                            <p class="mb-0 fs-3">My Profile</p>
                                        </a>
                                        <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                                            <i class="ti ti-mail fs-6"></i>
                                            <p class="mb-0 fs-3">My Account</p>
                                        </a>
                                        <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                                            <i class="ti ti-list-check fs-6"></i>
                                            <p class="mb-0 fs-3">My Task</p>
                                        </a>
                                        <a href="./authentication-login.html"
                                            class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>
            <div class="body-wrapper-inner">
                <div class="container-fluid">

                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title fw-semibold mb-4">📅 Event yang Sudah Terdaftar</h5>
                            <p class="text-muted mb-4">Kelola semua event yang sudah disimpan di sini. Total Event: **<?php echo count($events); ?>**</p>

                            <div class="table-responsive">
                                <table class="table table-hover text-nowrap align-middle">
                                    <thead>
                                        <tr class="text-dark">
                                            <th scope="col">Nama Event</th>
                                            <th scope="col">Tanggal Mulai</th>
                                            <th scope="col">Lokasi</th>
                                            <th scope="col" class="text-end">Anggaran</th>
                                            <th scope="col" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($events)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">Belum ada event yang ditambahkan.</td>
                                        </tr>
                                        <?php else: ?>
                                            <?php foreach ($events as $event): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($event['nama_event']); ?></td>
                                                <td><?php echo date('d M Y', strtotime($event['tanggal_mulai'])); ?></td>
                                                <td><?php echo htmlspecialchars($event['lokasi']); ?></td>
                                                <td class="text-end"><?php echo formatRupiah($event['total_anggaran']); ?></td>
                                                <td class="text-center">
                                                    <a href="edit_event.php?id=<?php echo $event['id_event']; ?>" class="btn btn-sm btn-warning me-2" title="Edit Event">
                                                        <i class="ti ti-pencil"></i>
                                                    </a>
                                                    <a href="hapusevent.php?id=<?php echo $event['id_event']; ?>" class="btn btn-sm btn-danger" title="Hapus Event" onclick="return confirm('Apakah Anda yakin ingin menghapus event <?php echo addslashes($event['nama_event']); ?>? Tindakan ini tidak dapat dibatalkan.');">
                                                        <i class="ti ti-trash"></i>
                                                    </a>
                                                    <a href="tambah-pengeluaran.php?event_id=<?php echo $event['id_event']; ?>" class="btn btn-sm btn-info text-white ms-2" title="Tambah Pengeluaran">
                                                        <i class="ti ti-cash"></i> Pengeluaran
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title fw-semibold mb-4">➕ Tambah Event Baru</h5>
                            <p class="text-muted mb-4">Isi semua detail yang diperlukan untuk event baru.</p>

                            <form method="POST" action="proses-tambahevent.php">

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="nama_event" class="form-label">Nama Event *</label>
                                        <input type="text" class="form-control" id="nama_event" name="nama_event"
                                            required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="tanggal_mulai" class="form-label">Tanggal Mulai *</label>
                                        <input type="date" class="form-control" id="tanggal_mulai"
                                            name="tanggal_mulai" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                                        <input type="date" class="form-control" id="tanggal_selesai"
                                            name="tanggal_selesai">
                                        <small class="form-text text-muted">Boleh dikosongkan jika event hanya satu
                                            hari.</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="lokasi" class="form-label">Lokasi *</label>
                                        <input type="text" class="form-control" id="lokasi" name="lokasi" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="deskripsi" class="form-label">Deskripsi *</label>
                                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4"
                                        required></textarea>
                                </div>

                                <div class="mb-4">
                                    <label for="total_anggaran" class="form-label">Total Anggaran</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" step="0.01" class="form-control" id="total_anggaran"
                                            name="total_anggaran" placeholder="Contoh: 1500000.00">
                                    </div>
                                    <small class="form-text text-muted">Boleh dikosongkan. Gunakan format angka desimal
                                        (cth: 1500000).</small>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary me-2">Simpan Event</button>
                                    <button type="reset" class="btn btn-outline-secondary">Reset Formulir</button>
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