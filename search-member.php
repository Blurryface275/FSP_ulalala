<?php
session_start();

// Cek sesi login
if (!isset($_SESSION['username'])) {
    exit;
}

header('Content-Type: text/html; charset=utf-8');

$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    echo "<p>Gagal koneksi database.</p>";
    exit;
}

// Ambil input
$nrp = trim($_GET['nrp'] ?? '');
$group_id = (int)($_GET['group_id'] ?? 0);

// Validasi Group ID (Penting agar tidak error logic)
if ($group_id <= 0) {
    echo "<p>Error: ID Group tidak valid.</p>";
    exit;
}

require_once("class/mahasiswa.php");
$mhsObj = new mahasiswa($mysqli);

// Panggil fungsi dari class
$results = $mhsObj->searchNonMember($group_id, $nrp);

if (!empty($results)) {
    echo "<ul class='member-default-list'>";
    foreach ($results as $student) {
        $valNrp = htmlspecialchars($student['nrp']);
        $valNama = htmlspecialchars($student['nama']);
        $valUser = htmlspecialchars($student['username']);

        echo "<li id='student-$valNrp'>";
        echo "<div class='member-item-flex'>";
        echo "$valNama ($valNrp)";
        echo "<button class='add-member-btn' 
                data-nrp='$valNrp' 
                data-nama='$valNama' 
                data-username='$valUser'>Tambah</button>";
        echo "</div>";
        echo "</li>";
    }
    echo "ul>";
} else {
    echo "<p>Tidak ada mahasiswa ditemukan.</p>";
}
