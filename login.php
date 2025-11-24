<?php
// FILE: login.php

include 'koneksi.php';
redirectIfLoggedIn();

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email_or_username = $_POST['email_or_username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email_or_username) || empty($password)) {

        $error = "Email/Username dan Password harus diisi.";
    } else {

        $stmt = $conn->prepare("SELECT id_user, nama, password, role FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $email_or_username, $email_or_username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            $login_berhasil = false;

            if (password_verify($password, $user['password'])) {
                $login_berhasil = true;
            } else if ($password === $user['password']) {
                $login_berhasil = true;
            }

            if ($login_berhasil) {


                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['role'] = $user['role'];

                header("Location: dashboard/index.php");
                exit;
            } else {
                $error = "Password salah.";
            }
        } else {
            $error = "Email atau Username tidak ditemukan.";
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
    <title>Login Page - Clean & Responsive</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/login.css">


</head>

<body>
    <div class="login-main-container">

        <div class="image-section">
            <img src="assets/images/login.svg" alt="Login Illustration">
            <center>
                <img src="assets/images/logoreka.PNG" alt="logorekaduit" style="position: absolute; bottom: 20px; margin-left: -320px; width: 200px; margin-bottom: 20px;">
            </center>
        </div>

        <div class="form-section">

            <center>
                <h2 class="mb-2">Login</h2>
                <p class="">Glad To See You,Welcome Back!</p>
            </center>

            <form method="POST" action="login.php">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger mb-4"><?= $error ?></div>
                <?php endif; ?>

                <div class="mb-4">
                    <label for="email_or_username" class="form-label visually-hidden">Username or Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z" />
                            </svg></span>

                        <input type="text" class="form-control" id="email_or_username" name="email_or_username" placeholder="Username or Email">
                    </div>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label visually-hidden">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-lock" viewBox="0 0 16 16">
                                <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 0V4s0 .5-.245.78-.564.42-.945.42.5-.245.78-.564.42-.945.42-.945V1a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1v2.5a.5.5 0 0 1-1 0V1a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2zM3.5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5v-2zM2 11.5a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2h-9a2 2 0 0 1-2-2v-2z" />
                            </svg></span>

                        <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-5">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">Remember me</label>
                    </div>
                    <a href="#" class="form-text-link">Forgot password?</a>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-custom-primary">LOGIN</button>
                </div>

                <p class="text-center mt-5">
                    Don't have an account? <a href="signup.php" class="form-text-link">Register here</a>
                </p>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>