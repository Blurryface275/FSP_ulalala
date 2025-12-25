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

require_once("class/thread.php");
$threadObj = new thread($mysqli);

// Ambil thread_id dari URL (Parameter yang dikirim dari link 'Chat')
$thread_id = $_GET['thread_id'] ?? null;

// Ambil detail status thread untuk validasi
$status_thread = 'Open';
if ($thread_id) {
    $status_thread = $threadObj->getStatusThread($thread_id);
}

// Cek jika thread_id tidak ada, kembalikan ke detail grup
if (!$thread_id) {
    header("Location: detail-group.php?id=" . $group_id);
    exit();
}

$logged_in_username = $_SESSION['username'];
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
                <?php if ($status_thread == 'Open'): ?>
                    <input type="text" id="message-input" placeholder="Ketik pesan di sini..." autocomplete="off">
                    <button id="btn-send" type="submit">Kirim</button>
                <?php else: ?>
                    <input type="text" disabled placeholder="Thread ini sudah ditutup..." style="background: #eee; cursor: not-allowed;">
                    <button disabled style="background: #ccc; cursor: not-allowed;">Closed</button>
                <?php endif; ?>
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
                    dataType: 'json', // Pastikan memberitahu jQuery ini adalah JSON
                    success: function(response) {
                        console.log("Data diterima:", response); // Cek di console F12

                        if (response && response.length > 0) {
                            response.forEach(chat => {
                                let isMine = (chat.username === "<?= $_SESSION['username'] ?>");

                                let html = `
                                <div class="bubble ${isMine ? 'mine' : 'others'}">
                                    <span class="meta">${chat.nama_asli} • ${chat.tanggal_pembuatan}</span>
                                    <div class="text">${chat.isi}</div> 
                                </div>`;
                                
                                $('#chat-window').append(html);
                                lastChatId = parseInt(chat.idchat);
                            });

                            // Auto scroll ke paling bawah
                            $('#chat-window').scrollTop($('#chat-window')[0].scrollHeight);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Ajax Error:", error);
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