<?php


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $identifier = trim($_POST["identifier"]);
    $password   = $_POST["password"];

    // Tentukan apakah input berupa email atau username
    if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {

        // ----- INPUT ADALAH EMAIL -----
        $stmt = $conn->prepare("SELECT id, username, email, password 
                                FROM users 
                                WHERE email = ?");
        $stmt->bind_param("s", $identifier);

    } else {

        // ----- INPUT ADALAH USERNAME -----
        $stmt = $conn->prepare("SELECT id, username, email, password 
                                FROM users 
                                WHERE username = ?");
        $stmt->bind_param("s", $identifier);
    }

    // Eksekusi
    $stmt->execute();
    $result = $stmt->get_result();

    // Cek hasil
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            $_SESSION["user_id"] = $row['id'];
            header("Location: dashboard/index.php");
            exit;
        } else {
            echo "Password salah";
        }

    } else {
        echo "Username / Email tidak ditemukan";
    }

    $stmt->close();
}
?>
