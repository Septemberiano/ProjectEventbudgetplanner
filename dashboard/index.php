<?php
// File: index.php
session_start();
// Pastikan file koneksi terhubung dan menginisialisasi variabel $conn (objek mysqli)
include_once '../koneksi.php';

// 1. AUTENTIKASI (PENTING: Cek user sudah login)
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$greeting_name = "Pengguna"; // Default
$user_id = $_SESSION['user_id'];
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : '';

// 2. FETCH NAMA PENGGUNA UNTUK GREETING & ROLE (Menggunakan Prepared Statement untuk keamanan)
// Mengambil nama dan role user jika belum ada di session.
if ($conn) {
    // Perbaikan: Gunakan prepared statement untuk fetching data dari session ID (Best Practice)
    $sql_user = "SELECT nama, role FROM users WHERE id_user = ? LIMIT 1";
    if ($stmt_user = $conn->prepare($sql_user)) {
        $stmt_user->bind_param("i", $user_id);
        if ($stmt_user->execute()) {
            $result_user = $stmt_user->get_result();
            if ($result_user->num_rows > 0) {
                $user_data = $result_user->fetch_assoc();
                // Simpan di session agar tidak perlu query berulang
                $_SESSION['nama_user'] = $user_data['nama'];
                $_SESSION['role'] = $user_data['role'];
                $user_role = $user_data['role'];
            }
        }
        $stmt_user->close();
    }
}

// Set nama untuk greeting, pastikan di-escape untuk mencegah XSS
if (isset($_SESSION['nama_user']) && !empty($_SESSION['nama_user'])) {
    $greeting_name = htmlspecialchars($_SESSION['nama_user']);
}

// Fungsi untuk memformat Rupiah
function formatRupiah($angka)
{
    if (!is_numeric($angka)) {
        $angka = 0;
    }
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Siapkan data untuk grafik Sales Overview (per event) - Query 1
// Query ini aman dari SQL Injection karena tidak menggunakan input user.
$sv_labels = [];
$sv_income = [];
$sv_expense = [];
$sql_sv = "SELECT e.id_event, e.nama_event, COALESCE(e.total_anggaran,0) AS pemasukan, COALESCE(SUM(p.nominal),0) AS pengeluaran
             FROM events e
             LEFT JOIN pengeluaran p ON p.id_event = e.id_event
             GROUP BY e.id_event, e.nama_event, e.total_anggaran
             ORDER BY e.id_event DESC LIMIT 12";
$tot_income_sv = 0;
$tot_expense_sv = 0;
if ($conn) {
    if ($resv = $conn->query($sql_sv)) {
        while ($rv = $resv->fetch_assoc()) {
            // Penggunaan JSON_HEX_TAG di bawah sudah aman dari XSS di JS
            $sv_labels[] = $rv['nama_event'];
            $inc = (float) $rv['pemasukan'];
            $exp = (float) $rv['pengeluaran'];
            $sv_income[] = $inc;
            $sv_expense[] = $exp;
            $tot_income_sv += $inc;
            $tot_expense_sv += $exp;
        }
        $resv->free();
    }
}
$net = $tot_income_sv - $tot_expense_sv;

// Siapkan data untuk grafik Event Finance (per event) - Query 2
// Query ini aman dari SQL Injection karena tidak menggunakan input user.
$chart_labels = [];
$income_data = [];
$expense_data = [];
$tot_pemasukan = 0;
$tot_pengeluaran = 0;
$sql_fin = "SELECT e.id_event, e.nama_event, COALESCE(e.total_anggaran,0) AS pemasukan, COALESCE(SUM(p.nominal),0) AS pengeluaran
             FROM events e
             LEFT JOIN pengeluaran p ON p.id_event = e.id_event
             GROUP BY e.id_event, e.nama_event, e.total_anggaran
             ORDER BY e.id_event DESC LIMIT 8";
if ($conn) {
    if ($resf = $conn->query($sql_fin)) {
        while ($rowf = $resf->fetch_assoc()) {
            // Penggunaan JSON_HEX_TAG di bawah sudah aman dari XSS di JS
            $chart_labels[] = $rowf['nama_event'];
            $income = (float) $rowf['pemasukan'];
            $expense = (float) $rowf['pengeluaran'];
            $income_data[] = $income;
            $expense_data[] = $expense;
            $tot_pemasukan += $income;
            $tot_pengeluaran += $expense;
        }
        $resf->free();
    }
}
?>



<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
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
                                    <p class="mb-2 fs-3 mt-2 ">Halo <?= $greeting_name ?></p>
                                    <img src="./assets/images/profile/user-1.jpg" alt="" width="35" height="35"
                                        class="rounded-circle">
                                </a>
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up"
                                    aria-labelledby="drop2">
                                    <div class="message-body">
                                        <!-- <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
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
                                        </a> -->
                                        <a href="../logout.php"
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
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card w-100">
                                <div class="card-body">
                                    <div class="d-md-flex align-items-center">
                                        <div>
                                            <h4 class="card-title">Sales Overview</h4>
                                            <p class="card-subtitle">
                                                Ample admin Vs Pixel admin
                                            </p>
                                        </div>
                                        <div class="ms-auto">
                                            <ul class="list-unstyled mb-0">
                                                <li class="list-inline-item text-primary">
                                                    <span
                                                        class="round-8 text-bg-primary rounded-circle me-1 d-inline-block"></span>
                                                    Ample
                                                </li>
                                                <li class="list-inline-item text-info">
                                                    <span
                                                        class="round-8 text-bg-info rounded-circle me-1 d-inline-block"></span>
                                                    Pixel Admin
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <?php
                                    // Data sudah disiapkan di blok PHP awal: $sv_labels, $sv_income, $sv_expense
                                    // dan total: $tot_income_sv, $tot_expense_sv, $net.
                                    ?>

                                    <div class="row mt-4">
                                        <div class="col-12 mb-3">
                                            <div class="d-flex gap-4 align-items-center">
                                                <div>
                                                    <h6 class="mb-0">Total Pemasukan</h6>
                                                    <strong class="fs-3 text-success"><?= formatRupiah($tot_income_sv) ?></strong>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">Total Pengeluaran</h6>
                                                    <strong class="fs-3 text-danger"><?= formatRupiah($tot_expense_sv) ?></strong>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">Net</h6>
                                                    <strong class="fs-3"
                                                        style="color:<?= $net >= 0 ? '#198754' : '#dc3545' ?>"><?= formatRupiah($net) ?></strong>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div id="sales-overview-chart" style="height:360px;"></div>
                                        </div>
                                    </div>

                                    <script>
                                        (function () {
                                            // PENGAMANAN XSS: Variabel PHP di-encode dengan JSON_HEX_TAG
                                            var labels = <?= json_encode($sv_labels, JSON_HEX_TAG) ?>;
                                            var income = <?= json_encode($sv_income) ?>;
                                            var expense = <?= json_encode($sv_expense) ?>;

                                            function render() {
                                                var options = {
                                                    series: [{
                                                        name: 'Pemasukan',
                                                        data: income
                                                    },
                                                    {
                                                        name: 'Pengeluaran',
                                                        data: expense
                                                    }
                                                    ],
                                                    chart: {
                                                        type: 'area',
                                                        height: 360,
                                                        toolbar: {
                                                            show: false
                                                        }
                                                    },
                                                    markers: {
                                                        size: 4
                                                    },
                                                    stroke: {
                                                        curve: 'smooth',
                                                        width: 2
                                                    },
                                                    xaxis: {
                                                        categories: labels,
                                                        labels: {
                                                            rotate: -30
                                                        }
                                                    },
                                                    yaxis: {
                                                        labels: {
                                                            formatter: function (v) {
                                                                return 'Rp ' + Number(v).toLocaleString();
                                                            }
                                                        }
                                                    },
                                                    tooltip: {
                                                        y: {
                                                            formatter: function (v) {
                                                                return 'Rp ' + Number(v).toLocaleString();
                                                            }
                                                        }
                                                    },
                                                    colors: ['#0d6efd', '#dc3545'],
                                                    dataLabels: {
                                                        enabled: false
                                                    }
                                                };
                                                var chart = new ApexCharts(document.querySelector('#sales-overview-chart'), options);
                                                chart.render();
                                            }

                                            if (typeof ApexCharts === 'undefined') {
                                                var t = setInterval(function () {
                                                    if (typeof ApexCharts !== 'undefined') {
                                                        clearInterval(t);
                                                        render();
                                                    }
                                                }, 200);
                                            } else {
                                                render();
                                            }
                                        })();
                                    </script>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card overflow-hidden">
                                <div class="card-body pb-0">
                                    <div class="d-flex align-items-start">
                                        <div>
                                            <h4 class="card-title">Event Finance</h4>
                                            <p class="card-subtitle">Ringkasan pemasukan & pengeluaran per event</p>
                                        </div>
                                        <div class="ms-auto">
                                            <div class="dropdown">
                                                <a href="javascript:void(0)" class="text-muted" id="finance-dropdown"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="ti ti-dots fs-7"></i>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-end"
                                                    aria-labelledby="finance-dropdown">
                                                    <li><a class="dropdown-item" href="javascript:void(0)">Refresh</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <?php
                                    // Data sudah disiapkan di blok PHP awal: $chart_labels, $income_data, $expense_data
                                    // dan total: $tot_pemasukan, $tot_pengeluaran
                                    ?>

                                    <div class="mt-3 mb-3">
                                        <div class="d-flex gap-3 align-items-center">
                                            <div>
                                                <h6 class="mb-1">Total Pemasukan</h6>
                                                <strong class="fs-4 text-success"><?= formatRupiah($tot_pemasukan) ?></strong>
                                            </div>
                                            <div>
                                                <h6 class="mb-1">Total Pengeluaran</h6>
                                                <strong class="fs-4 text-danger"><?= formatRupiah($tot_pengeluaran) ?></strong>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="event-finance-chart" style="height:260px;"></div>

                                    <script>
                                        window.addEventListener('load', function () {
                                            // PENGAMANAN XSS: Variabel PHP di-encode dengan JSON_HEX_TAG
                                            var labels = <?= json_encode($chart_labels, JSON_HEX_TAG) ?>;
                                            var income = <?= json_encode($income_data) ?>;
                                            var expense = <?= json_encode($expense_data) ?>;

                                            if (typeof ApexCharts === 'undefined') {
                                                console.warn('ApexCharts belum dimuat — chart akan dicoba setelah library tersedia.');
                                                var waitInterval = setInterval(function () {
                                                    if (typeof ApexCharts !== 'undefined') {
                                                        clearInterval(waitInterval);
                                                        renderEventFinanceChart(labels, income, expense);
                                                    }
                                                }, 200);
                                            } else {
                                                renderEventFinanceChart(labels, income, expense);
                                            }

                                            function renderEventFinanceChart(labels, income, expense) {
                                                var options = {
                                                    series: [{
                                                        name: 'Pemasukan',
                                                        type: 'column',
                                                        data: income
                                                    },
                                                    {
                                                        name: 'Pengeluaran',
                                                        type: 'column',
                                                        data: expense
                                                    }
                                                    ],
                                                    chart: {
                                                        height: 260,
                                                        type: 'line',
                                                        stacked: false,
                                                        toolbar: {
                                                            show: false
                                                        }
                                                    },
                                                    stroke: {
                                                        width: [0, 0]
                                                    },
                                                    plotOptions: {
                                                        bar: {
                                                            columnWidth: '50%'
                                                        }
                                                    },
                                                    dataLabels: {
                                                        enabled: false
                                                    },
                                                    xaxis: {
                                                        categories: labels,
                                                        labels: {
                                                            rotate: -20
                                                        }
                                                    },
                                                    yaxis: {
                                                        labels: {
                                                            formatter: function (val) {
                                                                return 'Rp ' + Number(val).toLocaleString();
                                                            }
                                                        }
                                                    },
                                                    tooltip: {
                                                        y: {
                                                            formatter: function (val) {
                                                                return 'Rp ' + Number(val).toLocaleString();
                                                            }
                                                        }
                                                    },
                                                    colors: ['#0d6efd', '#dc3545']
                                                };

                                                var chart = new ApexCharts(document.querySelector('#event-finance-chart'), options);
                                                chart.render();
                                            }
                                        });
                                    </script>

                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-md-flex align-items-center">
                                        <div>
                                            <h4 class="card-title">Users</h4>
                                            <p class="card-subtitle">Daftar pengguna aplikasi</p>
                                        </div>
                                        <div class="ms-auto mt-3 mt-md-0">
                                            <select class="form-select theme-select border-0"
                                                aria-label="Default select example">
                                                <option value="all">Semua</option>
                                                <option value="admin">Admin</option>
                                                <option value="user">User</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="table-responsive mt-4">
                                        <?php if ($user_role === 'admin'): ?>
                                            <?php
                                            $users = [];
                                            // Query user, tidak ada input user, jadi aman
                                            $sql = "SELECT id_user, nama, username, email, role, created_at FROM users ORDER BY id_user DESC";
                                            if ($conn) {
                                                if ($res = $conn->query($sql)) {
                                                    while ($r = $res->fetch_assoc()) {
                                                        $users[] = $r;
                                                    }
                                                    $res->free();
                                                }
                                            }
                                            ?>
                                            <table class="table mb-0 text-nowrap varient-table align-middle fs-3 table-hover">
                                                <thead>
                                                    <tr>
                                                        <th class="px-0 text-muted">#</th>
                                                        <th class="px-0 text-muted">Nama</th>
                                                        <th class="px-0 text-muted">Username</th>
                                                        <th class="px-0 text-muted">Email</th>
                                                        <th class="px-0 text-muted">Role</th>
                                                        <th class="px-0 text-muted text-end">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (count($users) === 0): ?>
                                                        <tr>
                                                            <td colspan="6" class="text-center">Belum ada pengguna.</td>
                                                        </tr>
                                                    <?php else: ?>
                                                        <?php foreach ($users as $u): ?>
                                                            <tr>
                                                                <td class="px-0"><?= htmlspecialchars($u['id_user']) ?></td>
                                                                <td class="px-0"><?= htmlspecialchars($u['nama']) ?></td>
                                                                <td class="px-0"><?= htmlspecialchars($u['username']) ?></td>
                                                                <td class="px-0"><?= htmlspecialchars($u['email']) ?></td>
                                                                <td class="px-0"><?= htmlspecialchars($u['role']) ?></td>
                                                                <td class="px-0 text-end">
                                                                    <a href="../edit_user.php?id=<?= urlencode($u['id_user']) ?>"
                                                                        class="btn btn-sm btn-primary me-1">Edit</a>
                                                                    <a href="../hapus_user.php?id=<?= urlencode($u['id_user']) ?>"
                                                                        class="btn btn-sm btn-danger"
                                                                        onclick="return confirm('Yakin ingin menghapus user ini?')">Hapus</a>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        <?php else: ?>
                                            <div class="alert alert-info">Hanya pengguna dengan peran <strong>admin</strong> yang
                                                dapat melihat dan mengelola akun pengguna.</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        </div>
                    <div class="col-lg-6">
                        <div class="card">

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