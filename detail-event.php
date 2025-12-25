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

require_once("class/event.php");
require_once("class/group.php");
$eventObj = new event($mysqli);
$groupObj = new group($mysqli);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
    $success = $eventObj->updateEvent(
        $_POST['judul'],
        $_POST['tanggal'],
        $_POST['keterangan'],
        $_POST['jenis'],
        $_POST['event_id']
    );

    if ($success) {
        header("Location: detail-event.php?event_id=" . $_POST['event_id'] . "&group_id=" . $_POST['idgrup']);
        exit();
    } else {
        echo "<script>alert('Gagal mengupdate data');</script>";
    }
}

$event_id = $_GET['event_id'] ?? die("Event ID tidak ditemukan!");
$group_id = $_GET['group_id'] ?? null;
$event = $eventObj->getEventById($event_id);
if (!$event) die("Event tidak ditemukan!");
$username_pembuat = $groupObj->getCreatorUsername($group_id);
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
                $poster_path = "posters/" . $event_id . "." . $poster_extension;
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
            <!-- cek apakah user yg login adalah pembaut grup, jika iya nanti tampilin button edit -->
            <?php if ($logged_in_username === $username_pembuat): ?>
                <button
                    id="btnEditEvent"
                    class="edit-group-btn"
                    data-id="<?= (int)$event_id ?>"
                    data-group-id="<?= (int)$group_id ?>">
                    Edit Event
                </button>
            <?php endif; ?>
        </div>
    </div>
    <!-- Modal Edit Event -->
    <?php if ($logged_in_username === $username_pembuat): ?>
        <div id="editEventModal">
            <div class="modal-content">
                <h3>Edit Event</h3>

                <label>Judul Event:</label>
                <input type="text" id="editEventJudul" value="<?= htmlspecialchars($judul) ?>">

                <label>Tanggal & Waktu:</label>
                <input type="datetime-local" id="editEventTanggal"
                    value="<?= date('Y-m-d\TH:i', strtotime($tanggal)) ?>">

                <label>Keterangan:</label>
                <textarea id="editEventKeterangan"><?= htmlspecialchars($keterangan) ?></textarea>

                <label>Jenis Event:</label>
                <select id="editEventJenis">
                    <option value="Publik" <?= $jenis == 'Publik' ? 'selected' : '' ?>>Publik</option>
                    <option value="Privat" <?= $jenis == 'Privat' ? 'selected' : '' ?>>Privat</option>
                </select>

                <button id="saveEventEdit" class="edit-group-btn">Simpan</button>
                <button id="closeEventModal" class="edit-group-btn">Batal</button>
            </div>
        </div>
    <?php endif; ?>
</body>
<script>
    $(function() {
        // Toggle Sidebar
        $("#toggle-btn").on("click", function() {
            $("#sidebar").toggleClass("collapsed");
            $(".main-content-wrapper").toggleClass("expanded");
        });

        $("#btnEditEvent").on("click", function() {
            $("#editEventModal").fadeIn();
        });

        $("#closeEventModal").on("click", function() {
            $("#editEventModal").fadeOut();
        });

        $("#saveEventEdit").on("click", function() {
            const form = $('<form action="detail-event.php" method="POST"></form>');
            form.append('<input type="hidden" name="event_id" value="<?= (int)$event_id ?>">');
            form.append('<input type="hidden" name="idgrup" value="<?= (int)$group_id ?>">');
            form.append('<input type="hidden" name="judul" value="' + $('#editEventJudul').val() + '">');
            form.append('<input type="hidden" name="tanggal" value="' + $('#editEventTanggal').val() + '">');
            form.append('<input type="hidden" name="keterangan" value="' + $('#editEventKeterangan').val() + '">');
            form.append('<input type="hidden" name="jenis" value="' + $('#editEventJenis').val() + '">');

            $('body').append(form);
            form.submit();
        });
    })
</script>

</html>