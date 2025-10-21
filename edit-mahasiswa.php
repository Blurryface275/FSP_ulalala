<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mahasiswa</title>
    <link rel="stylesheet" href="asset/login-style.css">
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
    require_once 'securityCek.php';
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

    <div class="box">
        <h2>Edit Data Mahasiswa</h2>
        <a href="data-mahasiswa.php" id="tombol-panah-img">
            <img src="93634.png" alt="Ke Data Dosen"> </a>
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
            <button type="submit" name="submit">Simpan Perubahan</button>
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