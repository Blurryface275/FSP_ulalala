<?php
session_start();
$mysqli = new mysqli("localhost", "root", "", "fullstack");

if ($mysqli->connect_errno) {
    die("Koneksi gagal: " . $mysqli->connect_error);
}

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

require_once("class/akun.php");
$akun = new akun($mysqli);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_SESSION['username'];
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $message = '<div class="message-error">Password baru dan konfirmasi password tidak cocok!</div>';
    } elseif (strlen($new_password) < 6) {
        $message = '<div class="message-error">Password baru harus minimal 6 karakter.</div>';
    } else {
        try {
            if ($akun->updatePassword($username, $old_password, $new_password)) {
                $message = '<div class="message-success">Kata sandi berhasil diubah! Silakan login kembali.</div>';
                // Hancurkan sesi agar pengguna harus login ulang, ini mau dipake ga ges? rasanya udah diajarin
                session_destroy();
                // Redirect ke halaman login setelah beberapa detik (menggunakan JS)
                echo '<script>setTimeout(function(){ window.location.href = "login.php"; }, 3000);</script>';
            } else {
                $message = '<div class="message-error">Gagal: Kata sandi lama salah atau terjadi kesalahan database.</div>';
            }
        } catch (Exception $e) {
            $message = '<div class="message-error">Error: ' . $e->getMessage() . '</div>';
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
        <?php echo $message; ?>
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
        <p style="text-align: center; margin-top: 15px;"><a href="index.php">Kembali ke Homepage</a></p>
    </div>
</body>

</html>