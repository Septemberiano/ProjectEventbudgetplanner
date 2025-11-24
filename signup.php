<?php
// FILE: signup.php

include 'koneksi.php';
redirectIfLoggedIn(); 

$error = '';
$success = '';


$nama = '';
$username = '';
$email = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nama = $_POST['name'] ?? '';
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validasi input
    if (empty($nama) || empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Semua kolom harus diisi.";
    } elseif ($password !== $confirm_password) {
        $error = "Konfirmasi password tidak cocok.";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
         $error = "Format email tidak valid.";
    } else {
        
        $stmt = $conn->prepare("SELECT id_user FROM users WHERE username = ? OR email = ?");
        
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username atau Email sudah terdaftar.";
        } else {
            
            // 1. HASH PASSWORD (AMAN UNTUK LOGIN)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // 2. SIMPAN PASSWORD MENTAH (UNSAFE, HANYA UNTUK DEBUGGING)
            $debug_password = $password; 

            
            // 3. QUERY INSERT DENGAN debug_password
            $stmt = $conn->prepare("INSERT INTO users (nama, username, email, password, debug_password) VALUES (?, ?, ?, ?, ?)");
            // Perhatian: Tambahkan 's' di bind_param untuk debug_password
            $stmt->bind_param("sssss", $nama, $username, $email, $hashed_password, $debug_password);

            if ($stmt->execute()) {
                $success = "Pendaftaran berhasil! Silakan Login.";
            
                $nama = $username = $email = ''; 
            } else {
                $error = "Terjadi kesalahan saat pendaftaran: " . $conn->error;
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Page - Rekaduit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="login-main-container">
        <div class="image-section">
           <img src="assets/images/signup.svg" alt="Sign Up Illustration" style="width: 900px;">
            <img src="assets/images/logoreka.PNG" alt="logo rekaduit" style="position: absolute; bottom: 20px; margin-left: -100px; width: 200px; margin-bottom: 80px;">
        </div>
        
        <div class="form-section">
            <h2 class="mb-5">Create Account</h2>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <form method="POST" action="signup.php">
                <div class="mb-4">
                    <label for="name" class="form-label visually-hidden">Full Name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Full Name" value="<?= $nama ?>" required>
                    </div>
                </div>
                 <div class="mb-4">
                    <label for="username" class="form-label visually-hidden">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-at"></i></span>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Username" value="<?= $username ?>" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="email" class="form-label visually-hidden">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email Address" value="<?= $email ?>" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label visually-hidden">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    </div>
                </div>
                
                <div class="mb-5">
                    <label for="confirm_password" class="form-label visually-hidden">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-custom-primary">REGISTER</button>
                </div>
                
                <p class="text-center mt-5">
                    Already have an account? <a href="login.php" class="form-text-link">Login here</a>
                </p>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js" crossorigin="anonymous"></script> 
</body>
</html>