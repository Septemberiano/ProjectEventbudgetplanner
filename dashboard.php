<?php
session_start();
include 'koneksi.php'; 


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}


$nama_user = $_SESSION['nama'] ?? 'Pengguna';
$role_user = $_SESSION['role'] ?? 'User';
$user_id = $_SESSION['user_id'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Rekaduit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">
            <h3>Rekaduit</h3>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item active">
                <a class="nav-link" href="#"><i class="fas fa-home me-2"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="fas fa-chart-line me-2"></i> Laporan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#"><i class="fas fa-users me-2"></i> Manajemen User</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
            </li>
        </ul>
        <div class="sidebar-footer">
            <p>Role: <?= htmlspecialchars($role_user) ?></p>
        </div>
    </div>
    <div class="main-content">
        <header class="navbar navbar-light bg-light">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h1">Selamat Datang, <?= htmlspecialchars($nama_user) ?>!</span>
                <a href="logout.php" class="btn btn-logout-mobile d-block d-md-none"><i class="fas fa-sign-out-alt"></i></a>
            </div>
        </header>

        <main class="p-4">
            <h2>Ringkasan Aplikasi</h2>
            <p class="lead text-muted">Akses cepat ke metrik dan informasi utama Anda.</p>
            
            <div class="row row-cols-1 row-cols-md-3 g-4 mt-3">
                
                <div class="col">
                    <div class="card card-custom shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-muted">Total Pemasukan</h5>
                            <p class="card-text fs-3 fw-bold">Rp 10.500.000</p>
                            <span class="text-success"><i class="fas fa-arrow-up"></i> 12% dari bulan lalu</span>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card card-custom shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-muted">Total Pengeluaran</h5>
                            <p class="card-text fs-3 fw-bold">Rp 4.250.000</p>
                            <span class="text-danger"><i class="fas fa-arrow-down"></i> 5% dari bulan lalu</span>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card card-custom shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-muted">Saldo Saat Ini</h5>
                            <p class="card-text fs-3 fw-bold text-primary">Rp 6.250.000</p>
                            <span class="text-info"><i class="fas fa-wallet"></i> <?= date('F Y') ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <h3 class="mt-5">Informasi Akun</h3>
            <div class="card p-3 shadow-sm">
                 <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>ID Sesi:</strong> <?= htmlspecialchars($user_id) ?></li>
                    <li class="list-group-item"><strong>Hak Akses:</strong> <span class="badge bg-info"><?= htmlspecialchars($role_user) ?></span></li>
                    <li class="list-group-item">Gunakan menu samping untuk navigasi.</li>
                </ul>
            </div>

        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>