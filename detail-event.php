<?php
session_start();
if (!isset($_SESSION['username'])) {
    $_SESSION['error_message'] = "Anda harus login dahulu!";
    header('Location: login.php');
    exit();
}
$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: " . $mysqli->connect_error);
}

$logged_in_username = $_SESSION['username'];
$logged_in_role = $_SESSION['role'];
$is_admin = $_SESSION['isadmin'] ?? 0;

if (!isset($_GET['event_id'])) {
    die("Event ID tidak ditemukan!");
}
if ($_SERVER['REQUEST_METHOD'] == 'GET')
    $event_id =  $_GET['event_id'];
$stmt = $mysqli->prepare("SELECT * FROM event WHERE idevent=?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $result->num_rows > 0) {
    $event = $result->fetch_assoc();
    $judul = $event['judul'];
    $tanggal = $event['tanggal'];
    $keterangan = $event['keterangan'];
    $jenis = $event['jenis'];
    $poster_extension = $event['poster_extension'];
} else {
    die("Event tidak ditemukan!");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Event</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div id="sidebar" class="sidebar">
        <div style="display: flex; align-items: center; gap: 10px; padding: 0 20px; margin-bottom: 20px;">
            <div class="toggle-btn" id="toggle-btn">☰</div>
        </div>

        <ul>
            <?php
            // Admin
            if (isset($_SESSION['isadmin']) && $_SESSION['isadmin'] == 1): ?>

                <li><a href="data-dosen.php">Data Dosen</a></li>
                <li><a href="data-mahasiswa.php">Data Mahasiswa</a></li>
                <li><a href="insert-dosen.php">Tambah Dosen</a></li>
                <li><a href="insert-mahasiswa.php">Tambah Mahasiswa</a></li>
                <li><a href="data-group.php">Data Group</a></li>
                <li><a href="insert-group.php">Tambah Group</a></li>

            <?php
            // Dosen
            elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'dosen'): ?>

                <li><a href="data-group.php">Data Group</a></li>
                <li><a href="insert-group.php">Tambah Group</a></li>

            <?php
            // Mahasiswa
            elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'mahasiswa'): ?>

                <li><a href="data-group.php">Data Group</a></li>

            <?php endif; ?>

            <li><a href="change-password.php">Ubah Password</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>

    </div>
    <div class="main-content-wrapper">
        <div class="content-box detail-event">
            <h1>Detail Event</h1>
            <!-- Placeholder poster -->
            <div class="poster">
                <?php
                $poster_path = "posters/event_" . $event_id . "." . $poster_extension;
                if (file_exists($poster_path)) {
                    echo "<img src='" . htmlspecialchars($poster_path) . "' alt='Poster Event' class='event-poster'>";
                } else {
                    echo "<img src='posters/placeholder.png' alt='Poster tidak tersedia' class='event-poster-placeholder'>";
                }
                ?>
            </div>
            <p><strong>Judul:</strong> <?= $judul ?></p>
            <p><strong>Tanggal:</strong> <?= $tanggal ?></p>
            <p><strong>Keterangan:</strong> <?= $keterangan ?></p>
            <p><strong>Jenis:</strong> <?= $jenis ?></p>

        </div>
    </div>
</body>

</html>