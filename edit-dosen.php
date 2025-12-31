<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Dosen</title>
    <link rel="stylesheet" href="style.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .error-warning {
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

    <?php
    session_start();
    if (!isset($_SESSION['username'])) {
        $_SESSION['error_message'] = "Anda harus login dahulu!";
        header('Location: login.php');
        exit();
    }
    $user_role = $_SESSION['role'] ?? '';
    $is_admin = $_SESSION['isadmin'] ?? 0;
    $mysqli = new mysqli("localhost", "root", "", "fullstack");
    if ($mysqli->connect_errno) {
        die("Failed to connect to MySQL: " . $mysqli->connect_error);
    }
    require_once("class/dosen.php");
    $dsn = new dosen($mysqli);
    $error_message = "";
    $npk_to_edit = '';
    $data = [];

    if (isset($_GET['npk'])) {
        $npk_to_edit = $_GET['npk'];
        $data = $dsn->fetchDosen($npk_to_edit);

        if (!$data) {
            die("Data dosen tidak ditemukan!");
        }
    } else {
        die("NPK dosen tidak ditemukan!");
    }

    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        $hasil = $dsn->executeUpdateDosen($_POST, $_FILES, $data);

        if ($hasil === true) {
            header("Location: data-dosen.php");
            exit;
        } else {
            $error_message = $hasil;

            // isi ulang data biar form tetep keisi
            $data['nama'] = $_POST['nama'];
            $data['npk'] = $_POST['npk'];
        }
    }
    ?>
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
            <h2>Edit Dosen</h2>

            <?php
            if (!empty($error_message)) {
                echo '<div class="error-warning">' . $error_message . '</div>';
            }
            ?>
            <form action="edit-dosen.php?npk=<?php echo $data['npk']; ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="npk_lama" value="<?php echo $data['npk']; ?>">

                <p>
                    <label for="nama">Nama:</label><br>
                    <input type="text" name="nama" id="nama" value="<?php echo $data['nama']; ?>" required>
                </p>

                <p>
                    <label for="npk">NPK:</label><br>
                    <input type="text" name="npk" id="npk" value="<?php echo $data['npk']; ?>" required>
                </p>

                <p>
                    <strong>Foto Saat Ini:</strong><br>
                    <img src="uploads/<?php echo $data['nama'] . '_' . $data['npk'] . '.' . $data['foto_extension']; ?>" width="150" style="border-radius:8px; margin-top:10px;">
                </p>

                <p>
                    <label for="foto">Edit Foto:</label><br>
                    <input type="file" name="foto" id="foto" accept="image/*">
                </p>
                <button type="submit" name="submit" class="btn">Simpan Perubahan</button>

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