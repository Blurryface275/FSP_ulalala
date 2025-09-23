<?php
    $mysqli = new mysqli("localhost", "root", "", "fullstack");
    if ($mysqli->connect_errno) {
       die("Failed to connect to MySQL: " . $mysqli->connect_error);
    }

    if (isset($_POST['insert'])){
        $nama = $_POST['nama']; // nama dosen
        $NPK = $_POST['NPK']; // NPK dosen
    }
  
    $foto = $_FILES['foto'];
    $sql = "insert into dosen (npk, nama, foto_extension) values(?,?,?)";

    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $target = "uploads/" . $NPK . "." . $ext; //kasih nama pakai npk dan extension

    // Pindahkan file ke folder uploads
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $target)) {
        // Simpan ke database
        $sql = "INSERT INTO dosen (npk, nama, foto_extension) VALUES (?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sss", $NPK, $nama, $ext);
        $stmt->execute();

        echo "Data dosen berhasil disimpan!";
    } else {
        echo "Gagal upload foto!";
    }

    
    header("Location: data-dosen.php");
    exit;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Dosen</title>
    <link rel="stylesheet" href="style.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<form action="" method="POST" enctype="multipart/form-data">

        <p>
            <label for="nama">Nama : </label>
            <input type="text" name="nama" id="nama">
        </p>
        <p>
            <label for="NPK">NPK : </label>
            <input type="text" name="NPK" id="NPK">
        </p>
        <p>
            <label for="foto">Foto : </label>
            <input type="file" name="foto" id="foto">
        </p>
        <button type="submit" name="insert">Insert</button>
        
    </form>
</body>
</html>