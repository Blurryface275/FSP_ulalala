<?php
session_start();
header('Content-Type: application/json');

// Cek login
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak. Silakan login.']);
    exit;
}
// Koneksi database
$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    echo json_encode(['success' => false, 'message' => 'Koneksi database gagal.']);
    exit;
}
require_once("class/group.php");
// Validasi request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metode request tidak valid.']);
    exit;
}
if (!isset($_POST['username'], $_POST['group_id'])) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap.']);
    exit;
}
$group_id = intval($_POST['group_id']);
$username = trim($_POST['username']);
if ($group_id <= 0 || $username === "") {
    echo json_encode(['success' => false, 'message' => 'ID grup atau username tidak valid.']);
    exit;
}
// Jalankan delete
$groupManager = new group($mysqli);
try {
    $result = $groupManager->deleteMemberGrup($group_id, $username);
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => "Member ($username) berhasil dihapus dari grup."
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => "Member tidak ditemukan di dalam grup."
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => "Terjadi error: " . $e->getMessage()
    ]);
}
exit;
