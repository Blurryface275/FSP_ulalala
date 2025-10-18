<?php
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: " . $mysqli->connect_error);
}
require_once("class/akun.php");
$akun = new akun();

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $row = $akun->getAccount($username, $password);

    if ($row) {
        // login berhasil
        $_SESSION['username'] = $row['username'];
        $_SESSION['isadmin']  = $row['isadmin'];
        $_SESSION['role']     = $row['nrp_mahasiswa'] ? 'mahasiswa' : ($row['npk_dosen'] ? 'dosen' : 'unknown');
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['login_error'] = "Username atau password salah!"; // Set pesan error di session
        header("Location: login.php");
        exit;
    }
}
