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

$search_nrp = $nrp . '%';

$query = "SELECT m.nrp, m.nama, a.username 
          FROM mahasiswa m 
          JOIN akun a ON m.nrp = a.nrp_mahasiswa 
          LEFT JOIN member_grup mg ON a.username = mg.username AND mg.idgrup = ? 
          WHERE mg.username IS NULL 
          AND m.nrp LIKE ? 
          ORDER BY m.nama ASC 
          LIMIT 10";

$stmt = $mysqli->prepare($query);
if ($stmt) {
    $stmt->bind_param("is", $group_id, $search_nrp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<ul class='member-default-list'>";
        while ($student = $result->fetch_assoc()) {
            echo "<li id='student-" . htmlspecialchars($student['nrp']) . "'>";
            echo "<div class='member-item-flex'>";

            // Tampilan Nama (NRP)
            echo htmlspecialchars($student['nama']) . " (" . htmlspecialchars($student['nrp']) . ")";

            // Tombol Tambah
            echo "<button class='add-member-btn' 
                        data-nrp='" . htmlspecialchars($student['nrp']) . "' 
                        data-nama='" . htmlspecialchars($student['nama']) . "' 
                        data-username='" . htmlspecialchars($student['username']) . "'>Tambah</button>";

            echo "</div>";
            echo "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Tidak ada mahasiswa ditemukan.</p>";
    }
    $stmt->close();
} else {
    echo "<p>Terjadi kesalahan query.</p>";
}

$mysqli->close();
