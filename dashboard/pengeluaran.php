<?php
include '../koneksi.php';

$events = [];

$sql = "
    SELECT 
        e.id_event, 
        e.nama_event, 
        e.tanggal_mulai, 
        e.lokasi, 
        e.total_anggaran, 
        -- MENGGUNAKAN KOLOM 'nominal' DARI TABEL 'pengeluaran'
        COALESCE(SUM(p.nominal), 0) AS total_pengeluaran 
    FROM 
        events e
    LEFT JOIN 
        pengeluaran p ON e.id_event = p.id_event
    GROUP BY 
        e.id_event, e.nama_event, e.tanggal_mulai, e.lokasi, e.total_anggaran
    ORDER BY 
        e.tanggal_mulai DESC
";

$result = $conn->query($sql);

if ($result === FALSE) {
    die("Error dalam query: " . $conn->error);
}

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['total_anggaran'] = (float) $row['total_anggaran'];
        $row['total_pengeluaran'] = (float) $row['total_pengeluaran'];
        $events[] = $row;
    }
}

function formatRupiah($angka)
{
    if (!is_numeric($angka)) {
        $angka = 0;
    }
    return 'Rp ' . number_format($angka, 2, ',', '.');
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Event</title>
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
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

        <!--  App Topstrip -->

        <!-- Sidebar Start -->
        <aside class="left-sidebar">
            <!-- Sidebar scroll-->
            <div>
                <div class="brand-logo d-flex align-items-center justify-content-between">
                    <a href="./index.php" class="text-nowrap logo-img">
                        <img src="../assets/images/logoreka.PNG" alt="logoreka" width="200px">
                    </a>
                    <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                        <i class="ti ti-x fs-6"></i>
                    </div>
                </div>
                <!-- Sidebar navigation-->
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
                        <!-- ---------------------------------- -->
                        <!-- Dashboard -->
                        <!-- ---------------------------------- -->
                        <li class="sidebar-item">
                            <a class="sidebar-link justify-content-between"
                                href="tambah-event.php" aria-expanded="false">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="d-flex">
                                        <i class="ti ti-aperture"></i>
                                    </span>
                                    <span class="hide-menu">Event</span>
                                </div>

                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link justify-content-between"
                                href="#" aria-expanded="false">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="d-flex">
                                        <i class="ti ti-shopping-cart"></i>
                                    </span>
                                    <span class="hide-menu">Anggaran Biaya</span>
                                </div>
                            </a>
                        </li>
                    </ul>



                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>
        <!--  Sidebar End -->
        <!--  Main wrapper -->
        <div class="body-wrapper">
            <!--  Header Start -->
            <header class="app-header">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <ul class="navbar-nav">
                        <li class="nav-item d-block d-xl-none">
                            <a class="nav-link sidebartoggler " id="headerCollapse" href="javascript:void(0)">
                                <i class="ti ti-menu-2"></i>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link " href="javascript:void(0)" id="drop1" data-bs-toggle="dropdown" aria-expanded="false">
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
                        <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end" style="position: relative;">

                            <li class="nav-item dropdown">
                                <a class="nav-link " href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <p class="mb-2  fs-3 mt-2  ">Halo </p>
                                    <img src="./assets/images/profile/user-1.jpg" alt="" width="35" height="35" class="rounded-circle">
                                </a>
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
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
                                        <a href="../logout.php" class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>
            <!--  Header End -->

            <div class="body-wrapper-inner">
                <div class="container-fluid">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title fw-semibold mb-4">Daftar Pengeluaran Event</h5>
                            <p class="text-muted mb-4">Kelola dan tambahkan pengeluaran untuk setiap event yang sudah terdaftar.</p>

                            <a href="tambah-event.php" class="btn btn-primary mb-3">
                                <i class="ti ti-plus me-1"></i> Tambah Event Baru
                            </a>

                            <div class="table-responsive">
                                <table class="table table-hover text-nowrap align-middle">
                                    <thead>
                                        <tr class="text-dark">
                                            <th scope="col">Nama Event</th>
                                            <th scope="col">Tanggal Mulai</th>
                                            <th scope="col">Lokasi</th>
                                            <th scope="col" class="text-end">Anggaran (Total)</th>
                                            <th scope="col" class="text-end">Pengeluaran</th>
                                            <th scope="col" class="text-end">Sisa Dana</th>
                                            <th scope="col" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($events as $event):
                                            $sisa = $event['total_anggaran'] - $event['total_pengeluaran'];
                                            $sisa_class = ($sisa < 0) ? 'text-danger fw-bold' : 'text-success fw-bold';
                                        ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($event['nama_event']); ?></td>
                                                <td><?php echo date('d M Y', strtotime($event['tanggal_mulai'])); ?></td>
                                                <td><?php echo htmlspecialchars($event['lokasi']); ?></td>
                                                <td class="text-end"><?php echo formatRupiah($event['total_anggaran']); ?></td>
                                                <td class="text-end text-warning fw-semibold"><?php echo formatRupiah($event['total_pengeluaran']); ?></td>
                                                <td class="text-end <?php echo $sisa_class; ?>"><?php echo formatRupiah($sisa); ?></td>
                                                <td class="text-center">
                                                    <a href="tambah-pengeluaran.php?event_id=<?php echo $event['id_event']; ?>" class="btn btn-sm btn-success">
                                                        <i class="ti ti-cash me-1"></i> Tambah Pengeluaran
                                                    </a>
                                                    <a href="detail_pengeluaran.php?event_id=<?php echo $event['id_event']; ?>" class="btn btn-sm btn-info me-2">
                                                        <i class="ti ti-list-details me-1"></i> Lihat Detail
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>

                                        <?php if (empty($events)): ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">Belum ada event yang ditambahkan.</td>
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
    <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/js/sidebarmenu.js"></script>
    <script src="./assets/js/app.min.js"></script>
    <script src="./assets/libs/apexcharts/dist/apexcharts.min.js"></script>
    <script src="./assets/libs/simplebar/dist/simplebar.js"></script>
    <script src="./assets/js/dashboard.js"></script>
    <!-- solar icons -->
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
</body>

</html>