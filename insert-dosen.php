<?php
session_start();
if (!isset($_SESSION['username'])) {
    $_SESSION['error_message'] = "Anda harus login dahulu!";
    header('Location: login.php');
    exit();
}

$user_role = $_SESSION['role'] ?? '';
$is_admin = $_SESSION['isadmin'] ?? 0;
?>


<!DOCTYPE html>
<?php
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'] ?? '';


    unset($_SESSION['error_message']);
}
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Dosen</title>
    <link rel="stylesheet" href="style.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        #error-warning {
            color: red;
            border: 1px solid red;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #ffeaea;
            border-radius: 5px;
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


            <?php endif; ?>

            <li><a href="change-password.php">Ubah Password</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="box">
            <!-- Semisal error message itu ada -->
            <?php if (!empty($error_message)): ?>
                <div id="error-warning"><?= $error_message ?></div>
            <?php endif; ?>
            <h1>Tambah Dosen</h1>


            <form action="dosen-process.php" method="POST" enctype="multipart/form-data">
                <p>
                    <label for="nama">Nama : </label>
                    <input type="text" name="nama" id="nama">
                </p>
                <p>
                    <label for="npk">NPK : </label>
                    <input type="text" name="npk" id="npk">
                </p>
                <p>
                    <label for="foto">Foto : </label>
                    <input type="file" name="foto" id="foto">
                </p>
                <p>
                    <label for="password">Password : </label> <!-- Karena password ditentukan oleh admin -->
                    <input type="password" name="password" id="password">
                </p>
                <button type="submit" name="submit" class="btn">Insert</button>
            </form>
        </div>
    </div>
    <script>
        const toggleBtn = document.getElementById('toggle-btn');
        const sidebar = document.getElementById('sidebar');

        toggleBtn.addEventListener('click', function() {
            if (window.innerWidth > 768) {
                // Mode Desktop: Mengecilkan sidebar (Collapsed)
                sidebar.classList.toggle('collapsed');
            } else {
                // Mode Mobile: Memunculkan/Menyembunyikan sidebar (Show)
                sidebar.classList.toggle('show');
            }
        });

        // Tambahan: Klik di luar sidebar untuk menutup saat di mobile
        document.addEventListener('click', function(event) {
            const isClickInside = sidebar.contains(event.target) || toggleBtn.contains(event.target);

            if (!isClickInside && window.innerWidth <= 768 && sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
            }
        });

        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const body = document.body;

        // 1. Cek simpanan preferensi user di local storage saat halaman dimuat
        if (localStorage.getItem('theme') === 'dark') {
            body.classList.add('dark-mode');
            themeIcon.innerText = '☀️'; // Ganti jadi matahari jika mode dark
        }

        // 2. Event Listener Klik
        themeToggle.addEventListener('click', () => {
            body.classList.toggle('dark-mode');

            // Update icon dan simpan ke Local Storage
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