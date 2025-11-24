<?php
session_start();
include_once '../koneksi.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
  header("Location: ../login.php");
  exit();
}

?>



<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Flexy Free Bootstrap Admin Template by WrapPixel</title>
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
                href="pengeluaran.php" aria-expanded="false">
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
                    <a href="./authentication-login.html" class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
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
          <!--  Row 1 -->
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
                          <span class="round-8 text-bg-primary rounded-circle me-1 d-inline-block"></span>
                          Ample
                        </li>
                        <li class="list-inline-item text-info">
                          <span class="round-8 text-bg-info rounded-circle me-1 d-inline-block"></span>
                          Pixel Admin
                        </li>
                      </ul>
                    </div>
                  </div>
                  <?php
                  // Siapkan data untuk grafik Sales Overview (per event)
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
                  if ($resv = $conn->query($sql_sv)) {
                    while ($rv = $resv->fetch_assoc()) {
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
                  $net = $tot_income_sv - $tot_expense_sv;
                  ?>

                  <div class="row mt-4">
                    <div class="col-12 mb-3">
                      <div class="d-flex gap-4 align-items-center">
                        <div>
                          <h6 class="mb-0">Total Pemasukan</h6>
                          <strong class="fs-3 text-success">Rp <?= number_format($tot_income_sv, 0, ',', '.') ?></strong>
                        </div>
                        <div>
                          <h6 class="mb-0">Total Pengeluaran</h6>
                          <strong class="fs-3 text-danger">Rp <?= number_format($tot_expense_sv, 0, ',', '.') ?></strong>
                        </div>
                        <div>
                          <h6 class="mb-0">Net</h6>
                          <strong class="fs-3" style="color:<?= $net >= 0 ? '#198754' : '#dc3545' ?>">Rp <?= number_format($net, 0, ',', '.') ?></strong>
                        </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div id="sales-overview-chart" style="height:360px;"></div>
                    </div>
                  </div>

                  <script>
                    (function() {
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
                              formatter: function(v) {
                                return 'Rp ' + Number(v).toLocaleString();
                              }
                            }
                          },
                          tooltip: {
                            y: {
                              formatter: function(v) {
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
                        var t = setInterval(function() {
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
                        <a href="javascript:void(0)" class="text-muted" id="finance-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                          <i class="ti ti-dots fs-7"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="finance-dropdown">
                          <li><a class="dropdown-item" href="javascript:void(0)">Refresh</a></li>
                        </ul>
                      </div>
                    </div>
                  </div>

                  <?php
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
                  if ($resf = $conn->query($sql_fin)) {
                    while ($rowf = $resf->fetch_assoc()) {
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
                  ?>

                  <div class="mt-3 mb-3">
                    <div class="d-flex gap-3 align-items-center">
                      <div>
                        <h6 class="mb-1">Total Pemasukan</h6>
                        <strong class="fs-4 text-success">Rp <?= number_format($tot_pemasukan, 0, ',', '.') ?></strong>
                      </div>
                      <div>
                        <h6 class="mb-1">Total Pengeluaran</h6>
                        <strong class="fs-4 text-danger">Rp <?= number_format($tot_pengeluaran, 0, ',', '.') ?></strong>
                      </div>
                    </div>
                  </div>

                  <div id="event-finance-chart" style="height:260px;"></div>

                  <script>
                    window.addEventListener('load', function() {
                      var labels = <?= json_encode($chart_labels, JSON_HEX_TAG) ?>;
                      var income = <?= json_encode($income_data) ?>;
                      var expense = <?= json_encode($expense_data) ?>;

                      if (typeof ApexCharts === 'undefined') {
                        console.warn('ApexCharts belum dimuat — chart akan dicoba setelah library tersedia.');
                        var waitInterval = setInterval(function() {
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
                              formatter: function(val) {
                                return 'Rp ' + Number(val).toLocaleString();
                              }
                            }
                          },
                          tooltip: {
                            y: {
                              formatter: function(val) {
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
                      <select class="form-select theme-select border-0" aria-label="Default select example">
                        <option value="all">Semua</option>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                      </select>
                    </div>
                  </div>
                  <div class="table-responsive mt-4">
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                      <?php
                      $users = [];
                      $sql = "SELECT id_user, nama, username, email, role, created_at FROM users ORDER BY id_user DESC";
                      if ($res = $conn->query($sql)) {
                        while ($r = $res->fetch_assoc()) {
                          $users[] = $r;
                        }
                        $res->free();
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
                                  <a href="../edit_user.php?id=<?= urlencode($u['id_user']) ?>" class="btn btn-sm btn-primary me-1">Edit</a>
                                  <a href="../hapus_user.php?id=<?= urlencode($u['id_user']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus user ini?')">Hapus</a>
                                </td>
                              </tr>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </tbody>
                      </table>
                    <?php else: ?>
                      <div class="alert alert-info">Hanya pengguna dengan peran <strong>admin</strong> yang dapat melihat dan mengelola akun pengguna.</div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <!-- Card -->
            <div class="card">
              <div class="card-body">
                <h4 class="card-title mb-0">Recent Comments</h4>
              </div>
              <div class="comment-widgets scrollable mb-2 common-widget" style="height: 465px" data-simplebar="">
                <!-- Comment Row -->
                <div class="d-flex flex-row comment-row border-bottom p-3 gap-3">
                  <div>
                    <span><img src="./assets/images/profile/user-3.jpg" class="rounded-circle" alt="user"
                        width="50" /></span>
                  </div>
                  <div class="comment-text w-100">
                    <h6 class="fw-medium">James Anderson</h6>
                    <p class="mb-1 fs-2 text-muted">
                      Lorem Ipsum is simply dummy text of the printing and
                      type etting industry
                    </p>
                    <div class="comment-footer mt-2">
                      <div class="d-flex align-items-center">
                        <span class="
                              badge
                              bg-info-subtle
                              text-info
                              
                            ">Pending</span>
                        <span class="action-icons">
                          <a href="javascript:void(0)" class="ps-3"><i class="ti ti-edit fs-5"></i></a>
                          <a href="javascript:void(0)" class="ps-3"><i class="ti ti-check fs-5"></i></a>
                          <a href="javascript:void(0)" class="ps-3"><i class="ti ti-heart fs-5"></i></a>
                        </span>
                      </div>
                      <span class="
                            text-muted
                            ms-auto
                            fw-normal
                            fs-2
                            d-block
                            mt-2
                            text-end
                          ">April 14, 2025</span>
                    </div>
                  </div>
                </div>
                <!-- Comment Row -->
                <div class="d-flex flex-row comment-row border-bottom active p-3 gap-3">
                  <div>
                    <span><img src="./assets/images/profile/user-5.jpg" class="rounded-circle" alt="user"
                        width="50" /></span>
                  </div>
                  <div class="comment-text active w-100">
                    <h6 class="fw-medium">Michael Jorden</h6>
                    <p class="mb-1 fs-2 text-muted">
                      Lorem Ipsum is simply dummy text of the printing and
                      type setting industry.
                    </p>
                    <div class="comment-footer mt-2">
                      <div class="d-flex align-items-center">
                        <span class="
                              badge
                              bg-success-subtle
                              text-success
                              
                            ">Approved</span>
                        <span class="action-icons active">
                          <a href="javascript:void(0)" class="ps-3"><i class="ti ti-edit fs-5"></i></a>
                          <a href="javascript:void(0)" class="ps-3"><i class="ti ti-circle-x fs-5"></i></a>
                          <a href="javascript:void(0)" class="ps-3"><i class="ti ti-heart text-danger fs-5"></i></a>
                        </span>
                      </div>
                      <span class="
                            text-muted
                            ms-auto
                            fw-normal
                            fs-2
                            text-end
                            mt-2
                            d-block
                          ">April 14, 2025</span>
                    </div>
                  </div>
                </div>
                <!-- Comment Row -->
                <div class="d-flex flex-row comment-row border-bottom p-3 gap-3">
                  <div>
                    <span><img src="./assets/images/profile/user-6.jpg" class="rounded-circle" alt="user"
                        width="50" /></span>
                  </div>
                  <div class="comment-text w-100">
                    <h6 class="fw-medium">Johnathan Doeting</h6>
                    <p class="mb-1 fs-2 text-muted">
                      Lorem Ipsum is simply dummy text of the printing and
                      type setting industry.
                    </p>
                    <div class="comment-footer mt-2">
                      <div class="d-flex align-items-center">
                        <span class="
                              badge
                              bg-danger-subtle
                              text-danger
                              
                            ">Rejected</span>
                        <span class="action-icons">
                          <a href="javascript:void(0)" class="ps-3"><i class="ti ti-edit fs-5"></i></a>
                          <a href="javascript:void(0)" class="ps-3"><i class="ti ti-check fs-5"></i></a>
                          <a href="javascript:void(0)" class="ps-3"><i class="ti ti-heart fs-5"></i></a>
                        </span>
                      </div>
                      <span class="
                            text-muted
                            ms-auto
                            fw-normal
                            fs-2
                            d-block
                            mt-2
                            text-end
                          ">April 14, 2025</span>
                    </div>
                  </div>
                </div>
                <!-- Comment Row -->
                <div class="d-flex flex-row comment-row p-3 gap-3">
                  <div>
                    <span><img src="./assets/images/profile/user-4.jpg" class="rounded-circle" alt="user"
                        width="50" /></span>
                  </div>
                  <div class="comment-text w-100">
                    <h6 class="fw-medium">James Anderson</h6>
                    <p class="mb-1 fs-2 text-muted">
                      Lorem Ipsum is simply dummy text of the printing and
                      type setting industry.
                    </p>
                    <div class="comment-footer mt-2">
                      <div class="d-flex align-items-center">
                        <span class="
                              badge
                              bg-info-subtle
                              text-info
                              
                            ">Pending</span>
                        <span class="action-icons">
                          <a href="javascript:void(0)" class="ps-3"><i class="ti ti-edit fs-5"></i></a>
                          <a href="javascript:void(0)" class="ps-3"><i class="ti ti-check fs-5"></i></a>
                          <a href="javascript:void(0)" class="ps-3"><i class="ti ti-heart fs-5"></i></a>
                        </span>
                      </div>
                      <span class="
                            text-muted
                            ms-auto
                            fw-normal
                            fs-2
                            d-block
                            text-end
                            mt-2
                          ">April 14, 2025</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="card">
            
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