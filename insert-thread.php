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
    <title>Tambah Thread</title>
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
        <h2>Tambah Thread untuk Grup : <?= $idgrup_aktif ? htmlspecialchars($idgrup_aktif) : '' ?></h2>

        <!-- ALERT: ID Grup Tidak Valid -->
        <?php if (!$idgrup_aktif): ?>
            <div class="alert-danger">
                <?= $error_message_form ?>
            </div>
        <?php endif; ?>


        <!-- ALERT SUKSES -->
        <?php if ($success_message): ?>
            <div class="alert-success">
                <?= htmlspecialchars($success_message) ?>
            </div>
        <?php endif; ?>

        <!-- ALERT ERROR -->
        <?php if ($error_message): ?>
            <div class="alert-danger">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>


        <!-- FORM INPUT EVENT -->
        <?php if ($idgrup_aktif): ?>
            <form action="thread-process.php" method="POST">
                <input type="hidden" name="idgrup" value="<?= htmlspecialchars($idgrup_aktif) ?>">

                <label for="status">Status Thread:</label>
                <select id="status" name="status" required>
                    <option value="Open">Open (Anggota grup bisa chat)</option>
                    <option value="Close">Close (Anggota grup tidak bisa chat)</option>
                </select><br><br>

                <button class="btn" type="submit" name="add_thread">Tambah Thread</button>
            </form>
        <?php endif; ?>

    </div>
</body>

</html>