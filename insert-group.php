<?php
session_start();

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
    <div class="main-content">
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
    </div>
</body>

</html>

 <script>
        const toggleBtn = document.getElementById('toggle-btn');
        const sidebar = document.getElementById('sidebar');

        toggleBtn.addEventListener('click', function() {
            if (window.innerWidth > 768) {
                // Mode Desktop ngecilin sidebar
                sidebar.classList.toggle('collapsed');
            } else {
                // Mode Mobile Memunculkan/Menyembunyikan sidebar 
                sidebar.classList.toggle('show');
            }
        });

        // Klik di luar sidebar untuk nutup saat di mobile
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

        // Event Listener Klik
        themeToggle.addEventListener('click', () => {
            body.classList.toggle('dark-mode');

            // Update icon trs disimpen
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