<?php
$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    die("Koneksi gagal: " . $mysqli->connect_error);
}

require_once("class/dosen.php");
$dosen = new dosen($mysqli);

require_once("class/akun.php");
$akun = new akun($mysqli);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $npk       = $_POST['npk'];
        $nama      = $_POST['nama'];
        $foto      = $_FILES['foto'];
        $password  = $_POST['password'];

        $dosen->insertDosenBaru($npk, $nama, $foto);
        $akun->insertAkunDosen($password, $npk);
        
        echo "<script>alert('Data berhasil disimpan!'); window.location.href='data-dosen.php';</script>";
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
    <title>Tambah Dosen</title>
    <link rel="stylesheet" href="login-style.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="box">
        <h1>Tambah Dosen</h1>
        <form action="" method="POST" enctype="multipart/form-data">
            <p>
                <label for="nama">Nama : </label>
                <input type="text" name="nama" id="nama">
            </p>
            <p>
                <label for="npk">NPK : </label>
                <input type="text" name="npk" id="npk">
            </p>
            <p>
                <label for="foto">Foto : </label>
                <input type="file" name="foto" id="foto">
            </p>
            <p>
                <label for="password">Password : </label> <!-- Karena password ditentukan oleh admin -->
                <input type="password" name="password" id="password">
            </p>
            <button type="submit" name="submit">Insert</button> 
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