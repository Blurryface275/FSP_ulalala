<?php
$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    die("Koneksi gagal: " . $mysqli->connect_error);
}
session_start();
require_once("class/dosen.php");
$dosen = new dosen($mysqli);

$error_message = "";

require_once("class/akun.php");
$akun = new akun($mysqli);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
        $npk       = $_POST['npk'];
        $nama      = $_POST['nama'];
        $foto      = $_FILES['foto'];
        $password  = $_POST['password'];

    if (empty($npk) || empty($nama) || $_FILES['foto']['error'] == UPLOAD_ERR_NO_FILE || empty($password)) {
        $_SESSION['error_message'] = "Semua field wajib diisi!";

        header("Location: insert-dosen.php");
        exit;
    }
    
    else if ($dosen->fetchDosen($npk)) {
        // Misal udah ada npk yg sama, kasih message error di sini
        $_SESSION['error_message'] = "NPK '$npk' sudah didaftarkan sebelumnya!"; 

        header("Location: insert-dosen.php");
        exit;
    }
    else{
        $dosen->insertDosenBaru($npk, $nama, $foto);
        $akun->insertAkunDosen($password, $npk);

        $_SESSION['success_message'] = "Data berhasil disimpan!";
        header("Location: data-dosen.php");
        exit;
    }
}

?>


