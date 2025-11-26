<?php
session_start();

$idgrup_aktif = $_GET['idgrup'] ?? null;

$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;

unset($_SESSION['success_message']);
unset($_SESSION['error_message']);

// Periksa validitas idgrup di sini
if (!$idgrup_aktif || !is_numeric($idgrup_aktif) || $idgrup_aktif <= 0) {
    // Jika tidak valid, set pesan error dan set $idgrup_aktif menjadi null untuk mencegah tampilan ID grup yang rusak
    $idgrup_aktif = null;
    $error_message_form = "ID Grup tidak ditemukan. Event tidak dapat dibuat.";
} else {
    // Jika valid, konversi ke integer
    $idgrup_aktif = (int)$idgrup_aktif;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Group</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        #error-warning {
            color: red;
            border: 1px solid red;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #ffeaea;
            border-radius: 5px;
            text-align: center;
        }

        .tab-content-item {
            display: none;
        }

        .tab-content-item.active {
            display: block;
        }
    </style>
</head>

<body>
    <div class="box">
        <h2>Tambah Event untuk Grup : <?= $idgrup_aktif ? htmlspecialchars($idgrup_aktif) : '' ?></h2>

        <?php
        // Tampilkan error jika ID Grup tidak valid
        if (!$idgrup_aktif):
        ?>
            <div class="alert alert-danger" style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 20px; background-color: #ffeaea; border-radius: 5px; text-align: center;">
                ID Grup tidak ditemukan. Event tidak dapat dibuat.
            </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
        <?php endif; ?>

        <?php if ($error_message): ?>
        <?php endif; ?>

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

                <button class="btn" type="submit" value="Tambah Event" name="add_event">Tambah Event</button>
            </form>
        <?php endif;
        ?>

    </div>
</body>

</html>