<?php
session_start();

// Jika belum login, arahkan ke login.php
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Tentukan ucapan sesuai role
$greeting = "Halo, ";
if (isset($_SESSION['isadmin']) && $_SESSION['isadmin'] == 1) {
    $greeting .= "Admin " . $_SESSION['username'];
    $isAdmin = true;
} elseif (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'mahasiswa') {
        $greeting .= "Mahasiswa " . $_SESSION['username'];
    } elseif ($_SESSION['role'] == 'dosen') {
        $greeting .= "Dosen " . $_SESSION['username'];
        $isDosen = true;
    } else {
        $greeting .= $_SESSION['username'];
    }
    $isAdmin = false;
} else {
    $greeting .= $_SESSION['username'];
    $isAdmin = false;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage - Fullstack</title>
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


    <div class="main-content">
        <header>
            <h1>Homepage Project</h1>
        </header>

        <div class="greeting">
            <h2><?= $greeting; ?></h2>
        </div>

        <section>
            <?php if ($isAdmin): ?>
                <p>Selamat datang di dashboard admin. Gunakan menu di samping untuk mengelola semua data kecuali group.</p>
            <?php else: ?>
                <p>Selamat datang di halaman pengguna. Anda hanya dapat mengubah kata sandi Anda.</p>
            <?php endif; ?>
        </section>
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