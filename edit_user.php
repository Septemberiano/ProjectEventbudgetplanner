<?php
session_start();
include_once 'koneksi.php';

// Hanya admin yang boleh mengakses
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['flash_error'] = 'Akses ditolak.';
    header('Location: dashboard/index.php');
    exit();
}

// id wajib
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['flash_error'] = 'ID user tidak valid.';
    header('Location: dashboard/index.php');
    exit();
}

$id = (int) $_GET['id'];

// Ambil data user untuk ditampilkan di form
$sql = "SELECT id_user, nama, username, email, role FROM users WHERE id_user = ? LIMIT 1";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    error_log('[edit_user] prepare failed: ' . $conn->error);
    $_SESSION['flash_error'] = 'Terjadi kesalahan server.';
    header('Location: dashboard/index.php');
    exit();
}
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    $stmt->close();
    $_SESSION['flash_error'] = 'User tidak ditemukan.';
    header('Location: dashboard/index.php');
    exit();
}
$user = $res->fetch_assoc();
$stmt->close();

// Jika form disubmit (POST) — proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = ($_POST['role'] === 'admin') ? 'admin' : 'user';
    $password = $_POST['password'] ?? '';

    if ($nama === '' || $username === '' || $email === '') {
        $_SESSION['flash_error'] = 'Nama, username, dan email wajib diisi.';
        header("Location: edit_user.php?id={$id}");
        exit();
    }

    // Update tanpa mengubah password jika kosong
    if ($password === '') {
        $u_sql = "UPDATE users SET nama = ?, username = ?, email = ?, role = ? WHERE id_user = ?";
        $u_stmt = $conn->prepare($u_sql);
        if (!$u_stmt) {
            error_log('[edit_user] update prepare failed: ' . $conn->error);
            $_SESSION['flash_error'] = 'Terjadi kesalahan server saat menyimpan.';
            header("Location: edit_user.php?id={$id}");
            exit();
        }
        $u_stmt->bind_param('ssssi', $nama, $username, $email, $role, $id);
        if ($u_stmt->execute()) {
            $_SESSION['flash_success'] = 'Perubahan user tersimpan.';
            $u_stmt->close();
            header('Location: dashboard/index.php');
            exit();
        } else {
            error_log('[edit_user] update execute failed: ' . $u_stmt->error);
            $_SESSION['flash_error'] = 'Gagal menyimpan perubahan.';
            $u_stmt->close();
            header("Location: edit_user.php?id={$id}");
            exit();
        }
    } else {
        // Update termasuk password
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $u_sql = "UPDATE users SET nama = ?, username = ?, email = ?, role = ?, password = ? WHERE id_user = ?";
        $u_stmt = $conn->prepare($u_sql);
        if (!$u_stmt) {
            error_log('[edit_user] update prepare failed: ' . $conn->error);
            $_SESSION['flash_error'] = 'Terjadi kesalahan server saat menyimpan.';
            header("Location: edit_user.php?id={$id}");
            exit();
        }
        $u_stmt->bind_param('sssssi', $nama, $username, $email, $role, $hashed, $id);
        if ($u_stmt->execute()) {
            $_SESSION['flash_success'] = 'Perubahan user tersimpan.';
            $u_stmt->close();
            header('Location: dashboard/index.php');
            exit();
        } else {
            error_log('[edit_user] update execute failed: ' . $u_stmt->error);
            $_SESSION['flash_error'] = 'Gagal menyimpan perubahan.';
            $u_stmt->close();
            header("Location: edit_user.php?id={$id}");
            exit();
        }
    }
}

// Tampilkan form edit
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit User</title>
    <link rel="stylesheet" href="dashboard/assets/css/styles.min.css">
</head>

<body class="p-4">
    <div class="container">
        <h3>Edit User</h3>
        <?php if (isset($_SESSION['flash_error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['flash_success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['flash_success']) ?></div>
            <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>

        <form method="post" action="edit_user.php?id=<?= urlencode($user['id_user']) ?>">
            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input class="form-control" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input class="form-control" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Role</label>
                <select name="role" class="form-select">
                    <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Password (kosongkan jika tidak ingin mengganti)</label>
                <input type="password" class="form-control" name="password">
            </div>
            <div>
                <a href="dashboard/index.php" class="btn btn-secondary">Batal</a>
                <button class="btn btn-primary" type="submit">Simpan</button>
            </div>
        </form>
    </div>
</body>

</html>