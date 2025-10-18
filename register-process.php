<?php
$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: " . $mysqli->connect_error);
}

require_once("class/akun.php");
$akun = new akun($mysqli); 

if (isset($_POST['action']) && $_POST['action'] === 'register') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role     = $_POST['role'];
    $id       = $_POST['id']; // bisa nrp atau npk
    $isadmin  = isset($_POST['isadmin']) ? 1 : 0;

    // cek username sudah dipakai atau belum
    if ($akun->usernameExists($username)) {
        die("Username sudah dipakai!");
    }

    // pastikan nrp/npk ada di tabel masing-masing (karena FK constraint)
    if ($role === "mahasiswa") {
        $cek_mhs = $mysqli->prepare("SELECT nrp FROM mahasiswa WHERE nrp=?");
        $cek_mhs->bind_param("s", $id);
        $cek_mhs->execute();
        if ($cek_mhs->get_result()->num_rows === 0) {
            die("NRP tidak ditemukan di tabel mahasiswa!");
        }

        if ($akun->insertAkunMahasiswa($username, $password, $id, $isadmin)) {
            header("Location: login.php");
            exit;
        }
    } elseif ($role === "dosen") {
        $cek_dsn = $mysqli->prepare("SELECT npk FROM dosen WHERE npk=?");
        $cek_dsn->bind_param("s", $id);
        $cek_dsn->execute();
        if ($cek_dsn->get_result()->num_rows === 0) {
            die("NPK tidak ditemukan di tabel dosen!");
        }

        if ($akun->insertAkunDosen($username, $password, $id, $isadmin)) {
            header("Location: login.php");
            exit;
        }
    } else {
        die("Role tidak valid!");
    }

    if ($stmt->execute()) {
        // redirect ke login
        header("Location: login.php");
        exit;
    } else {
        die("Gagal registrasi: " . $stmt->error);
    }
} else {
    // redirect ke register
    header("Location: register.php");
    exit;
}
