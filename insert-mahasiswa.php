<?php
$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    die("Koneksi gagal: " . $mysqli->connect_error);
}

require_once("class/mahasiswa.php");
$mahasiswa = new mahasiswa($mysqli);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try{
    // Ambil data dari form
    $nrp    = $_POST['nrp'];
    $nama   = $_POST['nama'];
    $angkatan = $_POST['angkatan'];
    $tgl_lahir  = $_POST['tgl'];
    $gender = $_POST['gender']; // Tambahan gender

    $mahasiswa->insertMahasiswaBaru($nrp, $nama, $gender, $tgl_lahir, $angkatan);

        echo "<script>alert('Data berhasil disimpan!'); window.location.href='data-mahasiswa.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('" . $e->getMessage() . "');</script>";
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Mahasiswa</title>
    <link rel="stylesheet" href="login-style.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="box">
        <h1>Tambah Mahasiswa</h1>
        <form action="" method="POST" enctype="multipart/form-data">

            <p>
                <label for="nama">Nama : </label>
                <input type="text" name="nama" id="nama">
            </p>
            <p>
                <label for="nrp">NRP : </label>
                <input type="text" name="nrp" id="nrp">
            </p>
            <p>
                <label for="gender">Gender : </label>
                <input type="radio" name="gender" id="pria" value="Pria">
                <label for="pria">Pria</label>

                <input type="radio" name="gender" id="wanita" value="Wanita">
                <label for="wanita">Wanita</label>
            </p>
            <p>
                <label for="tgl">Tanggal lahir : </label>
                <input type="date" name="tgl" id="tgl">
            </p>

            <p>
                <label for="angkatan">Angkatan : </label>
                <input type="number" name="angkatan" id="angkatan">
            </p>

            <p>
                <label for="foto">Foto : </label>
                <input type="file" name="foto" id="foto">
            </p>
            <button type="submit" name="submit">Insert</button> <!-- Namanya inserts biar beda sama dosen -->

        </form>
    </div>
</body>

</html>