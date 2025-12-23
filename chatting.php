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
$logged_in_role = $_SESSION['role'] ?? '';
$is_admin = $_SESSION['isadmin'] ?? 0;
$group_id = $_GET['id'] ?? null;
$success_message = $_SESSION['success_message'] ?? null;
unset($_SESSION['success_message']);

$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['error_message']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Chat Thread</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="style.css">
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

            <!-- Semua role dapat ubah password & logout -->
            <li><a href="change-password.php">Ubah Password</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>

    </div>

    <div class="main-content" id="main-content">
        <div class="chat-full-wrapper">
            <div class="chat-header">
                <h2>Thread: <?= htmlspecialchars($thread_id) ?></h2>
            </div>

            <div id="chat-window" class="chat-messages">
            </div>

            <div class="chat-input-container">
                <input type="text" id="message-input" placeholder="Ketik pesan di sini..." autocomplete="off">
                <button id="btn-send" type="submit">Kirim</button>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Toggle Sidebar agar chat tetap full
            $("#toggle-btn").on("click", function() {
                $("#sidebar").toggleClass("collapsed");
                $(".main-content").toggleClass("expanded");
            });

            let lastChatId = 0;
            const threadId = <?= json_encode($_GET['thread_id']) ?>;

            function fetchMessages() {
                $.ajax({
                    url: 'get-chat.php',
                    method: 'GET',
                    data: {
                        thread_id: threadId,
                        last_id: lastChatId
                    },
                    success: function(response) {
                        if (response.length > 0) {
                            response.forEach(chat => {
                                let isMine = (chat.username === "<?= $_SESSION['username'] ?>");
                                let html = `
                            <div class="bubble ${isMine ? 'mine' : 'others'}">
                                <span class="meta">${chat.username} • ${chat.waktu}</span>
                                <div class="text">${chat.pesan}</div>
                            </div>`;
                                $('#chat-window').append(html);
                                lastChatId = chat.idchat;
                            });
                            // Auto scroll ke bawah setiap ada chat baru
                            $('#chat-window').animate({
                                scrollTop: $('#chat-window')[0].scrollHeight
                            }, 500);
                        }
                    }
                });
            }

            // Jalankan Pull-Message setiap 2 detik
            setInterval(fetchMessages, 2000);

            // Kirim pesan saat tombol diklik
            $('#btn-send').click(function() {
                let msg = $('#message-input').val();
                if (msg.trim() === "") return;

                $.ajax({
                    url: 'send-chat.php',
                    method: 'POST',
                    data: {
                        thread_id: threadId,
                        pesan: msg
                    },
                    success: function() {
                        $('#message-input').val("");
                        fetchMessages();
                    }
                });
            });
        });
    </script>

</body>

</html>