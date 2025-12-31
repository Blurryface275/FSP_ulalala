<?php
session_start();

// Cek session login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$success_message = $_SESSION['success_message'] ?? null;
$error_message = $_SESSION['error_message'] ?? null;

unset($_SESSION['success_message']);
unset($_SESSION['error_message']);

$user_role = $_SESSION['role'] ?? '';
$is_admin = $_SESSION['isadmin'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Group</title>
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
            <?php
            // Admin
            if ($is_admin == 1): ?>
                <li><a href="data-dosen.php">Data Dosen</a></li>
                <li><a href="data-mahasiswa.php">Data Mahasiswa</a></li>
                <li><a href="insert-dosen.php">Tambah Dosen</a></li>
                <li><a href="insert-mahasiswa.php">Tambah Mahasiswa</a></li>
                <li><a href="data-group.php">Data Group</a></li>
                <li><a href="insert-group.php">Tambah Group</a></li>
            <?php
            // Dosen
            elseif ($user_role == 'dosen'): ?>
                <li><a href="data-group.php">Data Group</a></li>
                <li><a href="insert-group.php">Tambah Group</a></li>
            <?php
            // Mahasiswa
            elseif ($user_role == 'mahasiswa'): ?>
                <li><a href="data-group.php">Data Group</a></li>
            <?php endif; ?>

            <li><a href="change-password.php">Ubah Password</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="page-container">
        <main class="content-main">
            <div class="form-container">
                <h2>Tambah Group Baru</h2>

                <?php if ($success_message): ?>
                    <div class="message-success">
                        <?= htmlspecialchars($success_message); ?>
                    </div>
                <?php endif; ?>

                <?php if ($error_message): ?>
                    <div id="error-warning">
                        <?= htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>

                <form action="group-process.php" method="POST">
                    <label for="group_name">Nama Group:</label>
                    <input type="text" id="group_name" name="group_name" placeholder="Masukkan nama grup..." required>

                    <label for="description">Deskripsi:</label>
                    <textarea id="description" name="description" placeholder="Deskripsi grup..." required></textarea>

                    <label for="group_type">Jenis Group:</label>
                    <select id="group_type" name="group_type" required>
                        <option value="Privat">Privat</option>
                        <option value="Publik">Publik</option>
                    </select>

                    <button class="btn btn--edit" type="submit" value="Tambah Group" name="add_group" style="width: 100%; margin-top: 20px;">Tambah Group</button>
                </form>
            </div>
        </main>
    </div>

    <script>
        const toggleBtn = document.getElementById('toggle-btn');
        const sidebar = document.getElementById('sidebar');

        toggleBtn.addEventListener('click', function() {
            if (window.innerWidth > 768) {
                // Mode Desktop
                sidebar.classList.toggle('collapsed');
            } else {
                // Mode Mobile
                sidebar.classList.toggle('show');
            }
        });

        // Klik di luar sidebar untuk menutup saat di mobile
        document.addEventListener('click', function(event) {
            const isClickInside = sidebar.contains(event.target) || toggleBtn.contains(event.target);

            if (!isClickInside && window.innerWidth <= 768 && sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
            }
        });

        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const body = document.body;

        // Cek posisi terakhir mode gelap/terang 
        if (localStorage.getItem('theme') === 'dark') {
            body.classList.add('dark-mode');
            themeIcon.innerText = '☀️';
        }

        // Event Listener Klik Theme
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
    </script>
</body>

</html>