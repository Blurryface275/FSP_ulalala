<?php
session_start();
?>
<!DOCTYPE html>
<?php
if (isset($_SESSION['error_message'])) {
    // Tampilkan alert
    echo "<script>alert('{$_SESSION['error_message']}');</script>";

    unset($_SESSION['error_message']);
}
?>
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
        <a href="data-mahasiswa.php" id="tombol-panah-img">
            <img src="93634.png" alt="Ke Data Mahasiswa"> </a>

        <form action="mahasiswa-process.php" method="POST" enctype="multipart/form-data">

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
            <p>
                <label for="password">Password : </label> <!-- Password juga ditentuin sm admin -->
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