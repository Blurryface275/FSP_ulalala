<?php
session_start();

if (!isset($_SESSION['username'])) {
    $_SESSION['error_message'] = "Anda harus login dahulu!";
    header('Location: login.php');
    exit();
}

// ngambil pesan sukses 
$success_message = $_SESSION['success_message'] ?? '';
unset($_SESSION['success_message']);

$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL :" . $mysqli->connect_error);
}

// Ambil role dan username yang lagi login
$user_role = $_SESSION['role'] ?? 'unknown';
$logged_in_username = $_SESSION['username'];
$is_admin = $_SESSION['isadmin'] ?? 0;

require_once("class/group.php");
$group = new group($mysqli);

$limit = 5; // Jumlah grup per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Pake parameter role
$totalGroups = $group->getTotalGroups($user_role);
$totalPages = ceil($totalGroups / $limit);

$res = $group->displayGroup($limit, $offset, $user_role);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Group</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .foto {
            max-width: 150px;
        }

        #error-warning {
            color: green; 
            border: 1px solid green;
            padding: 10px;
            margin-bottom: 20px;
            background-color: #e9ffea;
            border-radius: 5px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div id="sidebar" class="sidebar">
        <div style="display: flex; align-items: center; gap: 10px; padding: 0 20px; margin-bottom: 20px;">
            <div class="toggle-btn" id="toggle-btn">☰</div>
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

    <div class="content-box">
        <h1>Data Group</h1>
        
        <?php if (!empty($success_message)): ?>
            <div id='error-warning'><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>

        <?php
        echo "<table border=1 cell-spacing=0><th>ID Group</th> <th>Nama Group</th> <th>Aksi</th>";
        
        // --- Tampilan tabel sama tombol ---
        while ($row = $res->fetch_assoc()) {
            $idgrup = $row['idgrup'];
            $is_member = $group->isMember($logged_in_username, $idgrup); // Cek status member

            echo "<tr>";
            echo "<td>" . $idgrup . "</td>";
            echo "<td>" . $row['nama'] . "</td>";
            echo "<td>";

           if ($is_admin == 1 || $user_role == 'dosen') {
        echo "<a href=\"detail-group.php?id=" . $idgrup . "\">Kelola Grup</a>";
    } elseif ($user_role == 'mahasiswa') {
        if ($is_member) {
            // JIKA SUDAH BERGABUNG DLM GRUP
            echo "<a href=\"detail-group.php?id=" . $idgrup . "\">Lihat Grup</a>";
        } else {
            // JIKA BELUM BERGABUNG DLM GRUP
            echo "<a href=\"detail-group.php?id=" . $idgrup . "\">Masuk Grup</a>"; 
        }
    } else {
        echo "<a href=\"detail-group.php?id=" . $idgrup . "\">Detail</a>";
    }

    echo "</td>";
    echo "</tr>";
}
        echo "</table>";

        // --- Tampilan ---
        echo "<div class='pagination'>";
        if ($page > 1) {
            echo "<a href='data-group.php?page=" . ($page - 1) . "'>&laquo; Previous</a>";
        }
        for ($i = 1; $i <= $totalPages; $i++) {
            if ($i == $page) {
                echo "<a class='active' href='data-group.php?page=$i'>$i</a>";
            } else {
                echo "<a href='data-group.php?page=$i'>$i</a>";
            }
        }
        if ($page < $totalPages) {
             echo "<a href='data-group.php?page=" . ($page + 1) . "'>Next &raquo;</a>";
        }
        echo "</div>";
        ?>
    </div>
    
    <script>
        $(function() {
            $("#toggle-btn").on("click", function() {
                $("#sidebar").toggleClass("collapsed");
                $(".content-box").toggleClass("expanded"); // ini kalo content-boxnya perlu diperluas
            });
        });
    </script>
</body>

</html>