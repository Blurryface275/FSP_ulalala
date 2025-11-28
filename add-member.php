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

if (!isset($_POST['nrp'], $_POST['group_id'])) {
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap.']);
    exit;
}

$group_id = intval($_POST['group_id']);
$nrp = trim($_POST['nrp']);

if ($group_id <= 0 || $nrp === "") {
    echo json_encode(['success' => false, 'message' => 'ID grup atau NRP tidak valid.']);
    exit;
}

// Jalankan insert
$groupManager = new group($mysqli);

try {
    $result = $groupManager->insertMemberGrup($group_id, $nrp);

    if ($result) {
        // ambil username untuk response
        $stmt = $mysqli->prepare("SELECT username FROM akun WHERE nrp_mahasiswa = ?");
        $stmt->bind_param("s", $nrp);
        $stmt->execute();
        $userRow = $stmt->get_result()->fetch_assoc();
        echo json_encode([
            'success' => true,
            'message' => "Mahasiswa ($nrp) berhasil ditambahkan.",
            'username' => $userRow['username']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => "Mahasiswa sudah ada di dalam grup."
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => "Terjadi error: " . $e->getMessage()
    ]);
}

exit;
