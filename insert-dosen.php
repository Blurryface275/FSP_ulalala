<?php
$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    die("Koneksi gagal: " . $mysqli->connect_error);
}

if (isset($_POST['submit'])) {
    // Ambil data dari form
    $nrp    = $_POST['nrp'];
    $nama   = $_POST['nama'];
    $angkatan = $_POST['angkatan'];
    $tgl_lahir  = $_POST['tgl'];
    $gender = $_POST['gender']; // Tambahan gender

    // Tangkap file foto
    $valid_extension = ['jpg', 'jpeg', 'png'];
    $ext  = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $valid_extension)) {
        die("Ekstensi file tidak valid! Hanya jpg/jpeg/png.");
    }

    // Nama file disimpan dengan format: NRP.extension
    $namaFileBaru = $nrp . "." . $ext;
    $targetFile   = "uploads/" . $namaFileBaru;

    // Pindahkan file ke folder uploads
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $targetFile)) {
        // Simpan data ke database
        $sql = "INSERT INTO mahasiswa (nrp, nama, gender, tanggal_lahir, angkatan, foto_extention) 
                    VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ssssss", $nrp, $nama, $gender, $tgl_lahir, $angkatan, $ext);

        if ($stmt->execute()) {
            echo "<script>alert('Data berhasil disimpan!');</script>";
        } else {
            header("Location : insert-mahasiswa.php");
            echo "Error saat insert: " . $stmt->error;
        }
    } else {
        echo "Gagal upload file!";
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
    <link rel="stylesheet" href="style.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
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
            <label for="gender">Gender : </label>
            <input type="radio" name="gender" id="pria" value="Pria">
            <label for="pria">Pria</label>

            <input type="radio" name="gender" id="wanita" value="Wanita">
            <label for="wanita">Wanita</label>
        </p>
        <p>
            <label for="tgl">Tanggal lahir : </label> <!-- ini gimana ya cara bikin kalender??? -->
            <input type="date" name="tgl" id="tgl">
        </p>
        <p>
            <label for="foto">Foto : </label>
            <input type="file" name="foto" id="foto">
        </p>
        <button type="submit" name="submit">Insert</button> <!-- Namanya inserts biar beda sama dosen -->
    </form>
</body>

</html>