<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Dosen</title>
    <link rel="stylesheet" href="login-style.css">
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

            // isi ulang data biar form tetap terisi
            $data['nama'] = $_POST['nama'];
            $data['nrp'] = $_POST['nrp'];
            $data['gender'] = $_POST['gender'];
            $data['tanggal_lahir'] = $_POST['tgl'];
            $data['angkatan'] = $_POST['angkatan'];
        }
    }
    ?>

    <div class="box">
        <h2>Edit Dosen</h2>
        <a href="data-dosen.php" id="tombol-panah-img">
            <img src="93634.png" alt="Ke Data Dosen"> </a>
        <form action="edit-dosen.php?npk=<?php echo $npk; ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="npk_lama" value="<?php echo $data['npk']; ?>">

            <p>
                <label for="nama">Nama:</label><br>
                <input type="text" name="nama" id="nama" value="<?php echo $data['nama']; ?>" required>
            </p>

            <p>
                <label for="NPK">NPK:</label><br>
                <input type="text" name="NPK" id="NPK" value="<?php echo $data['npk']; ?>" required>
            </p>

            <p>
                <strong>Foto Saat Ini:</strong><br>
                <img src="uploads/<?php echo $data['nama'] . '_' . $data['npk'] . '.' . $data['foto_extension']; ?>" width="150" style="border-radius:8px; margin-top:10px;">
            </p>

            <p>
                <label for="foto">Edit Foto:</label><br>
                <input type="file" name="foto" id="foto" accept="image/*">
            </p>

            <button type="submit" name="insert"><i class="fa-solid fa-save"></i> Simpan Perubahan</button>
        </form>
    </div>
</body>

</html>
<script>
    $(function() {
        $("#toggle-btn").on("click", function() {
            $("#sidebar").toggleClass("collapsed");
            $(".main-content").toggleClass("expanded");
        });
    });
</script>