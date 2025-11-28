<?php
session_start();

header("Content-Type: application/json");

if (!isset($_SESSION['username'])) {
    echo json_encode(["success" => false, "message" => "Anda harus login dahulu."]);
    exit;
}

$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    echo json_encode(["success" => false, "message" => "Koneksi gagal."]);
    exit;
}

require_once "class/group.php";
$groupHandler = new group($mysqli);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "message" => "Invalid request."]);
    exit;
}

$idgroup    = $_POST['idgroup'];
$groupName  = $_POST['group_name'];
$deskripsi  = $_POST['description'];
$group_type = $_POST['group_type'];

if (empty($idgroup) || empty($groupName) || empty($deskripsi)) {
    echo json_encode(["success" => false, "message" => "Semua field wajib diisi."]);
    exit;
}

try {
    $groupHandler->updateGroup($idgroup, $groupName, $deskripsi, $group_type);

    echo json_encode([
        "success" => true,
        "message" => "Group berhasil diperbarui."
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
exit;
