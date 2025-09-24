<?php
$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: " . $mysqli->connect_error);
}

if (isset($_POST['action']) && $_POST['action'] === 'register') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']); // plaintext dulu untuk tugas
    $role     = $_POST['role'];
    $id       = $_POST['id']; // bisa nrp atau npk
    $isadmin  = isset($_POST['isadmin']) ? 1 : 0;

    // cek username sudah dipakai atau belum
    $check = $mysqli->prepare("SELECT username FROM akun WHERE username=?");
    $check->bind_param("s", $username);
    $check->execute();
    $res = $check->get_result();
    if ($res->num_rows > 0) {
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

        $sql = "INSERT INTO akun (username, password, nrp_mahasiswa, isadmin) VALUES (?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sssi", $username, $password, $id, $isadmin);
    } elseif ($role === "dosen") {
        $cek_dsn = $mysqli->prepare("SELECT npk FROM dosen WHERE npk=?");
        $cek_dsn->bind_param("s", $id);
        $cek_dsn->execute();
        if ($cek_dsn->get_result()->num_rows === 0) {
            die("NPK tidak ditemukan di tabel dosen!");
        }

        $sql = "INSERT INTO akun (username, password, npk_dosen, isadmin) VALUES (?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sssi", $username, $password, $id, $isadmin);
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
    die("Akses tidak valid!");
}
