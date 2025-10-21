<?php
$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    die("Koneksi gagal: " . $mysqli->connect_error);
}
session_start();
require_once("class/mahasiswa.php");
$mahasiswa = new mahasiswa($mysqli);

require_once("class/akun.php");
$akun = new akun($mysqli);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Ambil data dari form
    $nrp    = $_POST['nrp'];
    $nama   = $_POST['nama'];
    $angkatan = $_POST['angkatan'];
    $tgl_lahir  = $_POST['tgl'];
    $gender = $_POST['gender'] ?? null;
    $password = $_POST['password'];
    
    if (empty($nrp) || empty($nama) || empty($angkatan) || empty($tgl_lahir) || empty($password) || empty($gender)) {
        $_SESSION['error_message'] = "Semua field wajib diisi!";

        header("Location: insert-mahasiswa.php");
        exit;
    }
    
    else if ($mahasiswa->isNrpExists($nrp)) {
        // Misal udah ada nrp yg sama, kasih message error di sini
        $_SESSION['error_message'] = "NRP '$nrp' wajib diisi!"; 

        header("Location: insert-mahasiswa.php");
        exit;
    }
    else{
        $mahasiswa->insertMahasiswaBaru($nrp, $nama, $gender, $tgl_lahir, $angkatan);
        $akun->insertAkunMahasiswa($password, $nrp);

        $_SESSION['success_message'] = "Data berhasil disimpan!";
        header("Location: data-mahasiswa.php");
        exit;
    }
}

?>
