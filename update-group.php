<?php
session_start();

if (!isset($_SESSION['username'])) {
    $_SESSION['error_message'] = "Anda harus login dahulu!";
    header('Location: login.php');
    exit();
}

$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    die("Koneksi gagal: " . $mysqli->connect_error);
}

require_once("class/group.php");
$groupHandler = new group($mysqli);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $idgroup    = $_POST['idgroup'];  // wajib ada
    $groupName  = $_POST['group_name'];
    $deskripsi  = $_POST['description'];
    $group_type = $_POST['group_type'];

    if (empty($idgroup) || empty($groupName) || empty($deskripsi)) {
        $_SESSION['error_message'] = "Semua field tidak boleh kosong.";
        header('Location: edit-group.php?id=' . $idgroup);
        exit();
    }

    try {
        $groupHandler->updateGroup(
            $idgroup,
            $groupName,
            $deskripsi,
            $group_type
        );

        $_SESSION['success_message'] = "Group berhasil diupdate!";
        header("Location: index.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Gagal update Group: " . $e->getMessage();
        header('Location: edit-group.php?id=' . $idgroup);
        exit();
    }

} else {
    header("Location: index.php");
    exit();
}
