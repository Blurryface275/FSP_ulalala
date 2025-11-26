<?php
session_start();

$idgrup_aktif = $_GET['idgrup'] ?? null;

$success_message = $_SESSION['success_message'] ?? null;
$error_message   = $_SESSION['error_message'] ?? null;

// Hapus pesan setelah diambil
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);

// Validasi ID GRUP
if (!$idgrup_aktif || !is_numeric($idgrup_aktif) || $idgrup_aktif <= 0) {
    $idgrup_aktif = null;
    $error_message_form = "ID Grup tidak ditemukan. Event tidak dapat dibuat.";
} else {
    $idgrup_aktif = (int)$idgrup_aktif;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Event</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .alert-success {
            padding: 10px;
            background: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }

        .alert-danger {
            padding: 10px;
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
            margin-bottom: 15px;
            border-radius: 5px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="box">
        <h2>Tambah Event untuk Grup : <?= $idgrup_aktif ? htmlspecialchars($idgrup_aktif) : '' ?></h2>

        <!-- 🔴 ALERT: ID Grup Tidak Valid -->
        <?php if (!$idgrup_aktif): ?>
            <div class="alert-danger">
                <?= $error_message_form ?>
            </div>
        <?php endif; ?>


        <!-- 🟩 ALERT SUKSES -->
        <?php if ($success_message): ?>
            <div class="alert-success">
                <?= htmlspecialchars($success_message) ?>
            </div>
        <?php endif; ?>

        <!-- 🔴 ALERT ERROR -->
        <?php if ($error_message): ?>
            <div class="alert-danger">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>


        <!-- FORM INPUT EVENT -->
        <?php if ($idgrup_aktif): ?>
            <form action="event-process.php" method="POST" enctype="multipart/form-data">

                <input type="hidden" name="idgrup" value="<?= htmlspecialchars($idgrup_aktif) ?>">

                <label for="judul">Judul Event:</label>
                <input type="text" id="judul" name="judul" required><br><br>

                <label for="tanggal">Tanggal & Waktu Event:</label>
                <input type="datetime-local" id="tanggal" name="tanggal" required><br><br>

                <label for="keterangan">Keterangan (Deskripsi Detail):</label>
                <textarea id="keterangan" name="keterangan" rows="5" required></textarea><br><br>

                <label for="jenis">Jenis Event:</label>
                <select id="jenis" name="jenis" required>
                    <option value="Privat">Privat (Hanya untuk anggota grup)</option>
                    <option value="Publik">Publik (Dapat dilihat semua orang)</option>
                </select><br><br>

                <label for="poster">Upload Poster (Opsional):</label>
                <input type="file" id="poster" name="poster" accept="image/png, image/jpeg, image/jpg"><br><br>

                <button class="btn" type="submit" name="add_event">Tambah Event</button>
            </form>
        <?php endif; ?>

    </div>
</body>

</html>