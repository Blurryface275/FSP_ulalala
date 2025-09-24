<?php
session_start();

// Jika belum login, paksa ke login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Tentukan sapaan
$greeting = "Hallo, ";
if ($_SESSION['isadmin'] == 1) {
    $greeting .= "Admin " . $_SESSION['username'];
} elseif ($_SESSION['role'] == 'mahasiswa') {
    $greeting .= "Mahasiswa " . $_SESSION['username'];
} elseif ($_SESSION['role'] == 'dosen') {
    $greeting .= "Dosen " . $_SESSION['username'];
} else {
    $greeting .= $_SESSION['username'];
}
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage - Fullstack</title>
    <link rel="stylesheet" href="style.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <!-- Sidebar -->
    <div id="sidebar" class="sidebar">
        <h2 class="logo"><i class="fa-solid fa-sun"></i> Sunset</h2>
        <ul>
            <li><a href="data-dosen.php"><i class="fa-solid fa-user-tie"></i><span> Data Dosen</span></a></li>
            <li><a href="data-mahasiswa.php"><i class="fa-solid fa-user-graduate"></i><span> Data Mahasiswa</span></a></li>
            <li><a href="insert-dosen.php"><i class="fa-solid fa-user-plus"></i><span> Tambah Dosen</span></a></li>
            <li><a href="insert-mahasiswa.php"><i class="fa-solid fa-user-plus"></i><span> Tambah Mahasiswa</span></a></li>
        </ul>
    </div>

    <!-- Konten utama -->
    <div class="main-content">
        <header>
            <span id="toggle-btn" class="toggle-btn"><i class="fa-solid fa-bars"></i></span>
            <h1>Homepage Project</h1>
        </header>

        <section>
            <p>Selamat datang di homepage project Fullstack Anda.
                Gunakan menu samping untuk mengelola data dosen dan mahasiswa.</p>
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