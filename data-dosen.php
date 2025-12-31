<?php
session_start();
if (!isset($_SESSION['username'])) {
    $_SESSION['error_message'] = "Anda harus login dahulu!";
    header('Location: login.php');
    exit();
}

$user_role = $_SESSION['role'] ?? '';
$is_admin = $_SESSION['isadmin'] ?? 0;

// Ambil pesan sukses jika ada
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'] ?? '';
    unset($_SESSION['success_message']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Dosen</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .foto {
            max-width: 150px;
            border-radius: 8px;
            /* Tambahan sedikit agar foto lebih rapi */
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

        .teks-merah {
            color: red;
            font-style: italic;
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

            <li><a href="change-password.php">Ubah Password</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>

    </div>

    <div class="page-container">
        <main class="content-main">
            <div class="content-box">
                <h1>Data Dosen</h1>

                <?php
                $mysqli = new mysqli("localhost", "root", "", "fullstack");
                if ($mysqli->connect_errno) {
                    die("Failed to connect to MySQL :" . $mysqli->connect_error);
                }

                require_once("class/dosen.php");
                $dosen = new dosen($mysqli);

                if (!empty($success_message)) {
                    echo "<div id='error-warning'>", htmlspecialchars($success_message), "</div>";
                }

                $limit = 5; // jumlah dosen per page
                $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
                $offset = ($page - 1) * $limit;
                $totalMahasiswa = $dosen->getTotalDosen();
                $totalPages = ceil($totalMahasiswa / $limit);

                $res = $dosen->displayDosen($limit, $offset);

                // Hapus border=1, gunakan struktur thead tbody
                echo "<table>
                        <thead>
                            <tr>
                                <th>Foto</th> 
                                <th>Nama</th> 
                                <th>NPK</th> 
                                <th colspan='2'>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>";

                while ($row = $res->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>";

                    $fotoDosen = "uploads/" . $row['nama'] . "_" . $row['npk'] . "." . $row['foto_extension'];

                    // cek apakah file benar-benar ada di folder
                    if (file_exists($fotoDosen)) {
                        echo "<img class='foto' src='" . $fotoDosen . "' alt='poster'>";
                    } else {
                        echo "<span class='teks-merah'>Poster tidak ditemukan</span>";
                    }

                    echo "</td>";

                    echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['npk']) . "</td>";

                    // Link ini otomatis jadi tombol karena CSS table td a
                    echo "<td><a href='edit-dosen.php?npk=" . $row['npk'] . "'>Edit</a></td>";
                    echo "<td><a href='delete-dosen.php?npk=" . $row['npk'] . "' onclick='return confirm(\"Yakin ingin menghapus dosen ini?\");' class='btn--delete'>Delete</a></td>";

                    echo "</tr>";
                }

                echo "</tbody></table>";

                // Pagination
                echo "<div class='pagination'>";

                if ($page > 1) {
                    echo "<a href='data-dosen.php?page=" . ($page - 1) . "'>&laquo; Previous</a>";
                }

                for ($i = 1; $i <= $totalPages; $i++) {
                    if ($i == $page) {
                        echo "<a class='active' href='data-dosen.php?page=$i'>$i</a>";
                    } else {
                        echo "<a href='data-dosen.php?page=$i'>$i</a>";
                    }
                }

                if ($page < $totalPages) {
                    echo "<a href='data-dosen.php?page=" . ($page + 1) . "'>Next &raquo;</a>";
                }

                echo "</div>";
                ?>
            </div>
        </main>
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