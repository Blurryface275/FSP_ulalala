<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mahasiswa</title>
    <link rel="stylesheet" href="style.css">
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
    require_once("class/mahasiswa.php");
    $mhs = new mahasiswa($mysqli);
    $error_message = "";
    $nrp_to_edit = '';
    $data = [];

    if (isset($_GET['nrp'])) {
        $nrp_to_edit = $_GET['nrp'];
        $data = $mhs->fetchMahasiswa($nrp_to_edit);

        if (!$data) {
            die("Data mahasiswa tidak ditemukan!");
        }
    } else {
        die("NRP mahasiswa tidak ditemukan!");
    }

    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        $hasil = $mhs->executeUpdateMahasiswa($_POST, $_FILES, $data);

        if ($hasil === true) {
            header("Location: data-mahasiswa.php");
            exit;
        } else {
            $error_message = $hasil;

            // isi ulang data biar form tetep keisi
            $data['nama'] = $_POST['nama'];
            $data['nrp'] = $_POST['nrp'];
            $data['gender'] = $_POST['gender'];
            $data['tanggal_lahir'] = $_POST['tgl'];
            $data['angkatan'] = $_POST['angkatan'];
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
            <h2>Edit Data Mahasiswa</h2>
            <?php
            if (!empty($error_message)) {
                echo '<div class="error-warning">' . $error_message . '</div>';
            }
            ?>
            <form action="edit-mahasiswa.php?nrp=<?php echo $data['nrp']; ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="nrp_lama" value="<?php echo $data['nrp']; ?>">
                <p>
                    <label for="nama">Nama : </label>
                    <input type="text" name="nama" id="nama" value="<?php echo $data['nama']; ?>" required>
                </p>
                <p>
                    <label for="nrp">NRP : </label>
                    <input type="text" name="nrp" id="nrp" value="<?php echo $data['nrp']; ?>" required>
                </p>
                <p>
                    <label>Gender : </label>
                    <input type="radio" name="gender" value="Pria" <?php if ($data['gender'] == "Pria") echo "checked"; ?> required> Pria
                    <input type="radio" name="gender" value="Wanita" <?php if ($data['gender'] == "Wanita") echo "checked"; ?>> Wanita
                </p>
                <p>
                    <label for="tgl">Tanggal Lahir : </label>
                    <input type="date" name="tgl" id="tgl" value="<?php echo $data['tanggal_lahir']; ?>" required>
                </p>
                <p>
                    <label for="angkatan">Angkatan : </label>
                    <input type="number" name="angkatan" id="angkatan" value="<?php echo $data['angkatan']; ?>" required>
                </p>
                <p>
                    <label for="foto">Ubah Foto : </label>
                    <input type="file" name="foto" id="foto">
                    <br>
                <p>
                    Foto sekarang: <img src="uploads/<?php echo $data['nrp'] . '.' . $data['foto_extention']; ?>" style="max-width: 150px;">
                </p>
                </p>
                <button type="submit" name="submit" class="btn">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</body>

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

    // Cek simpanan preferensi user di local storage saat halaman dimuat
    if (localStorage.getItem('theme') === 'dark') {
        body.classList.add('dark-mode');
        themeIcon.innerText = '☀️'; // Ganti jadi matahari jika mode dark
    }

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

</html>