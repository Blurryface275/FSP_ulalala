<?php
session_start();
$mysqli = new mysqli("localhost", "root", "", "fullstack");

if ($mysqli->connect_errno) {
    die("Koneksi gagal: " . $mysqli->connect_error);
}
if (!isset($_SESSION['username'])) { 
    $_SESSION['error_message'] = "Anda harus login dahulu!";
    header('Location: login.php');
    exit(); 
}

require_once("class/akun.php");
$akun = new akun($mysqli);

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_SESSION['username'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi input
    if ($new_password !== $confirm_password) {
        $message = '<div class="message-error">Password baru dan konfirmasi password tidak cocok!</div>';
    } elseif (strlen($new_password) < 6) {
        $message = '<div class="message-error">Password baru harus minimal 6 karakter.</div>';
    } else {
        // Update password
        $updateSuccess = $akun->updatePassword($username, $old_password, $new_password);

        if ($updateSuccess) {
            // Jika berhasil, tampilkan pesan dan arahkan ke login
            $_SESSION['success_message'] = "Kata sandi berhasil diubah! Silakan login kembali.";

            // Hancurkan session agar user harus login ulang
            session_destroy();

            header("Location: login.php");
            exit;
        } else {
            $message = '<div class="message-error">Kata sandi lama salah atau terjadi kesalahan database.</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Password</title>
    <link rel="stylesheet" href="login-style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="box">
        <h2>Ubah Kata Sandi</h2>
        <a href="index.php" id="tombol-panah-img">
            <img src="93634.png" alt="Ke Data Dosen"> 
        </a>

        <!-- Tampilkan pesan error/sukses -->
        <?php if (!empty($message)) echo $message; ?>

        <form method="POST">
            <p>
                <label for="old_password">Kata Sandi Lama:</label>
                <input type="password" id="old_password" name="old_password" required>
            </p>
            <p>
                <label for="new_password">Kata Sandi Baru:</label>
                <input type="password" id="new_password" name="new_password" required>
            </p>
            <p>
                <label for="confirm_password">Konfirmasi Kata Sandi Baru:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </p>
            <button type="submit">Ubah Password</button>
        </form>

        <p style="text-align: center; margin-top: 15px;">
            <a href="index.php">Kembali ke Homepage</a>
        </p>
    </div>
</body>
</html>
