<?php
session_start();

// Cek Sesi Login
if (!isset($_SESSION['username'])) {
    $_SESSION['error_message'] = "Anda harus login dahulu!";
    header('Location: login.php');
    exit();
}

// Koneksi Database
$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: " . $mysqli->connect_error);
}

require_once("class/thread.php");
$threadObj = new thread($mysqli);

// Ambil ID Thread & Group dari URL
$thread_id = $_GET['thread_id'] ?? null;
$group_id  = $_GET['group_id'] ?? null;

// Validasi ID Thread
if (!$thread_id) {
    header("Location: data-group.php");
    exit();
}

// Cek Status Thread
$status_thread = $threadObj->getStatusThread($thread_id) ?? 'Closed';

$logged_in_username = $_SESSION['username'];
$user_role = $_SESSION['role'] ?? '';
$is_admin = $_SESSION['isadmin'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Thread #<?= htmlspecialchars($thread_id) ?></title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

    <div id="sidebar" class="sidebar">
        <div style="display: flex; align-items: center; gap: 10px; padding: 0 20px; margin-bottom: 20px;">
            <div class="toggle-btn" id="toggle-btn">☰</div>
            <div id="theme-toggle" style="cursor: pointer; font-size: 18px;">
                <span id="theme-icon">🌙</span>
            </div>
        </div>

        <ul>
            <?php if ($is_admin == 1): ?>
                <li><a href="data-dosen.php">Data Dosen</a></li>
                <li><a href="data-mahasiswa.php">Data Mahasiswa</a></li>
                <li><a href="insert-dosen.php">Tambah Dosen</a></li>
                <li><a href="insert-mahasiswa.php">Tambah Mahasiswa</a></li>
                <li><a href="data-group.php">Data Group</a></li>
                <li><a href="insert-group.php">Tambah Group</a></li>
            <?php elseif ($user_role == 'dosen'): ?>
                <li><a href="data-group.php">Data Group</a></li>
                <li><a href="insert-group.php">Tambah Group</a></li>
            <?php elseif ($user_role == 'mahasiswa'): ?>
                <li><a href="data-group.php">Data Group</a></li>
            <?php endif; ?>

            <li><a href="change-password.php">Ubah Password</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="chat-full-wrapper">

            <div class="chat-header">
                <div style="display:flex; align-items:center; gap:15px;">
                    <?php if ($group_id): ?>
                        <a href="detail-group.php?id=<?= $group_id ?>" style="color: white; text-decoration: none; font-size: 24px; display: flex; align-items: center;">&larr;</a>
                    <?php endif; ?>

                    <div>
                        <h2 style="margin:0; font-size: 16px; text-align:left;">Thread #<?= htmlspecialchars($thread_id) ?></h2>
                        <span style="font-size: 12px; opacity: 0.8; font-weight: normal;">Status: <?= htmlspecialchars($status_thread) ?></span>
                    </div>
                </div>
            </div>

            <div id="chat-window" class="chat-messages">
                <div style="text-align: center; color: gray; margin-top: 20px; font-size: 14px;">Memuat percakapan...</div>
            </div>

            <div class="chat-input-container">
                <?php if ($status_thread == 'Open'): ?>
                    <input type="text" id="message-input" placeholder="Ketik pesan..." autocomplete="off">
                    <button id="btn-send" class="btn">Kirim</button>
                <?php else: ?>
                    <input type="text" disabled placeholder="Thread ini telah ditutup." style="background: #f0f0f0; cursor: not-allowed; color: #999;">
                    <button disabled style="background: #ccc; cursor: not-allowed;">Closed</button>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <script>
        $(document).ready(function() {
            // --- 1. Sidebar Logic (Mobile Responsive) ---
            const toggleBtn = document.getElementById('toggle-btn');
            const sidebar = document.getElementById('sidebar');

            toggleBtn.addEventListener('click', function() {
                if (window.innerWidth > 768) {
                    sidebar.classList.toggle('collapsed');
                } else {
                    sidebar.classList.toggle('show');
                }
            });

            document.addEventListener('click', function(event) {
                const isClickInside = sidebar.contains(event.target) || toggleBtn.contains(event.target);
                if (!isClickInside && window.innerWidth <= 768 && sidebar.classList.contains('show')) {
                    sidebar.classList.remove('show');
                }
            });

            // --- 2. Theme Logic ---
            const themeToggle = document.getElementById('theme-toggle');
            const themeIcon = document.getElementById('theme-icon');
            const body = document.body;

            if (localStorage.getItem('theme') === 'dark') {
                body.classList.add('dark-mode');
                themeIcon.innerText = '☀️';
            }

            themeToggle.addEventListener('click', () => {
                body.classList.toggle('dark-mode');
                if (body.classList.contains('dark-mode')) {
                    themeIcon.innerText = '☀️';
                    localStorage.setItem('theme', 'dark');
                } else {
                    themeIcon.innerText = '🌙';
                    localStorage.setItem('theme', 'light');
                }
            });

            // --- 3. Chat Logic ---
            let lastChatId = 0;
            const threadId = <?= json_encode($thread_id) ?>;
            const currentUser = <?= json_encode($logged_in_username) ?>;
            const chatWindow = $('#chat-window');

            // Fungsi ambil pesan
            function fetchMessages() {
                $.ajax({
                    url: 'get-chat.php',
                    method: 'GET',
                    data: {
                        thread_id: threadId,
                        last_id: lastChatId
                    },
                    dataType: 'json',
                    success: function(response) {
                        // Bersihkan loading state saat pertama kali load
                        if (lastChatId === 0) {
                            if (response.length > 0) {
                                chatWindow.html('');
                            } else {
                                chatWindow.html('<div style="text-align:center; color:gray; margin-top:20px;">Belum ada pesan. Mulailah percakapan!</div>');
                            }
                        }

                        if (response && response.length > 0) {
                            // Hapus pesan kosong jika ada pesan baru masuk
                            if (chatWindow.text().includes("Belum ada pesan")) chatWindow.html('');

                            response.forEach(chat => {
                                let isMine = (chat.username === currentUser);

                                // Format Waktu sederhana (Jam:Menit)
                                let timeParts = chat.tanggal_pembuatan.split(' ');
                                let time = timeParts.length > 1 ? timeParts[1].substring(0, 5) : chat.tanggal_pembuatan;

                                let html = `
                                <div class="bubble ${isMine ? 'mine' : 'others'}">
                                    <span class="meta">${isMine ? 'Anda' : chat.nama_asli} • ${time}</span>
                                    <div class="text">${chat.isi}</div> 
                                </div>`;

                                chatWindow.append(html);
                                lastChatId = parseInt(chat.idchat);
                            });

                            // Auto scroll ke bawah
                            chatWindow.scrollTop(chatWindow[0].scrollHeight);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Gagal memuat pesan:", error);
                    }
                });
            }

            // Polling pesan setiap 2 detik
            setInterval(fetchMessages, 2000);
            fetchMessages(); // Load pertama kali

            // Fungsi Kirim Pesan
            function sendMessage() {
                let msgInput = $('#message-input');
                let msg = msgInput.val();

                if (msg.trim() === "") return;

                // Matikan input sementara agar tidak double submit
                msgInput.prop('disabled', true);

                $.ajax({
                    url: 'send-chat.php',
                    method: 'POST',
                    data: {
                        thread_id: threadId,
                        pesan: msg
                    },
                    success: function() {
                        msgInput.val("").prop('disabled', false).focus();
                        fetchMessages(); // Refresh chat area
                    },
                    error: function() {
                        alert("Gagal mengirim pesan. Cek koneksi internet.");
                        msgInput.prop('disabled', false);
                    }
                });
            }

            $('#btn-send').click(sendMessage);

            $('#message-input').keypress(function(e) {
                if (e.which == 13) {
                    sendMessage();
                }
            });
        });
    </script>

</body>

</html>