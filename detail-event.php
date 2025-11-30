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

// Proses edit event
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['event_id'])) {
    // Ambil data dari form (yang dikirim lewat JS)
    $p_event_id = $_POST['event_id'];
    $p_group_id = $_POST['idgrup']; // Untuk redirect kembali
    $p_judul = $_POST['judul'];
    $p_tanggal = $_POST['tanggal'];
    $p_keterangan = $_POST['keterangan'];
    $p_jenis = $_POST['jenis'];

    // Validasi sederhana (opsional: pastikan tidak kosong)
    if (!empty($p_judul) && !empty($p_tanggal)) {
        // Query Update
        $stmt_upd = $mysqli->prepare("UPDATE event SET judul=?, tanggal=?, keterangan=?, jenis=? WHERE idevent=?");
        $stmt_upd->bind_param("ssssi", $p_judul, $p_tanggal, $p_keterangan, $p_jenis, $p_event_id);

        if ($stmt_upd->execute()) {
            // Sukses Update: Redirect (PRG Pattern) agar data refresh
            header("Location: detail-event.php?event_id=" . $p_event_id . "&group_id=" . $p_group_id);
            exit();
        } else {
            echo "<script>alert('Gagal mengupdate data: " . $mysqli->error . "');</script>";
        }
        $stmt_upd->close();
    }
}

$logged_in_username = $_SESSION['username'];
$logged_in_role = $_SESSION['role'];
$is_admin = $_SESSION['isadmin'] ?? 0;

if (!isset($_GET['event_id'])) {
    die("Event ID tidak ditemukan!");
}
if ($_SERVER['REQUEST_METHOD'] == 'GET')
    $event_id =  $_GET['event_id'];
$group_id = $_GET['group_id'] ?? null;

// cari username pembuat grup
$query_username = "select username_pembuat from grup where idgrup = ?";
$stmt_username = $mysqli->prepare($query_username);
$stmt_username->bind_param("i", $group_id);
$stmt_username->execute();
$result_username = $stmt_username->get_result();
if ($result_username && $result_username->num_rows > 0) {
    $row = $result_username->fetch_assoc();
    $username_pembuat = $row['username_pembuat'];
}

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


$stmt->close();
$mysqli->close();
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