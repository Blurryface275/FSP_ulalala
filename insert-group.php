<?php
session_start();

$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;

unset($_SESSION['success_message']);
unset($_SESSION['error_message']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Group</title>
    <link rel="stylesheet" href="login-style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-weight: bold;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>

<body>
    <div class="box">
        <h2>Tambah Group Baru</h2><br><br>

        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form action="group-process.php" method="POST">
            <label for="group_name">Nama Group:</label><br>
            <input type="text" id="group_name" name="group_name" required><br><br>

            <label for="description">Deskripsi:</label><br>
            <textarea id="description" name="description" required></textarea><br><br>

            <label for="group_type">Jenis Group:</label><br>
            <select id="group_type" name="group_type" required>
                <option value="Privat">Privat</option>
                <option value="Publik">Publik</option>
            </select><br><br>
            <button class="btn" type="submit" value="Tambah Group" name="add_group">Tambah Group</button>
        </form>
    </div>
</body>

</html>
<script>
    $(function() {
        $("#toggle-btn").on("click", function() {
            $("#sidebar").toggleClass("collapsed");
            $(".main-content").toggleClass("expanded");
        });
    });
</script>