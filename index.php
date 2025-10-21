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
        </div>
        <ul>
            <?php if ($isAdmin): ?>
                <li><a href="data-dosen.php">Data Dosen</a></li>
                <li><a href="data-mahasiswa.php">Data Mahasiswa</a></li>
                <li><a href="insert-dosen.php">Tambah Dosen</a></li>
                <li><a href="insert-mahasiswa.php">Tambah Mahasiswa</a></li>
            <?php endif; ?>

            <!-- Semua role (admin dan dosen) bisa ubah password dan logout -->
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
                <p>Selamat datang di dashboard admin. Gunakan menu di samping untuk mengelola data dosen dan mahasiswa.</p>
            <?php else: ?>
                <p>Selamat datang di halaman pengguna. Anda hanya dapat mengubah kata sandi Anda.</p>
            <?php endif; ?>
        </section>
    </div>

    <script>
        $(function() {
            $("#toggle-btn").on("click", function() {
                $("#sidebar").toggleClass("collapsed");
                $(".main-content").toggleClass("expanded");
            });
        });
    </script>
</body>
</html>
