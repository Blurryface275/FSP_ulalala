<?php
session_start();
$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: " . $mysqli->connect_error);
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // cek username + password
    $sql = "SELECT username, password, isadmin, nrp_mahasiswa, npk_dosen 
            FROM akun 
            WHERE username=? AND password=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // login berhasil -> simpan session
        $_SESSION['username'] = $row['username'];
        $_SESSION['isadmin']  = $row['isadmin'];
        $_SESSION['role']     = $row['nrp_mahasiswa'] ? 'mahasiswa' : ($row['npk_dosen'] ? 'dosen' : 'unknown');

        header("Location: index.php");
        exit;
    } else {
        // gagal login
        echo "Username atau password salah!";
    }
}
?>
