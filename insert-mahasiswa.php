<?php
    $mysqli = new mysqli("localhost", "root", "", "fullstack");
    if ($mysqli->connect_errno) {
       die("Failed to connect to MySQL: " . $mysqli->connect_error);
    }

    if (isset($_POST['inserts'])){//ini 'inserts' sesuai yang insert mhs namanya
        $nama = $_POST['nama']; // nama mhs
        $NRP = $_POST['NRP']; // nrp
    }
  
    $foto = $_FILES['foto'];
    $sql = "insert into mahasiswa (nrp, nama, gender, tanggal_lahir, angkatan, foto_extension) values(?,?,?,?,?,?)";

    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $target = "uploads/" . $NRP . "." . $ext; //kasih nama pakai nrp dan extension

    // Pindahkan file ke folder uploads
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $target)) {
        // Simpan ke database
        $sql = "INSERT INTO mahasiswa (nrp, nama, gender, tanggal_lahir, angkatan, foto_extention) VALUES (?,?,?,?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ssedss", $NPK, $nama, $ext);//ini enum sebutannnya e kah?
        $stmt->execute();

        echo "Data Mahasiswa berhasil disimpan!";
        header("Location: data-mahasiswa.php");
        exit;
    } else {
        echo "Gagal upload foto!";
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
            <label for="NRP">NRP : </label>
            <input type="text" name="NRP" id="NRP">
        </p>
          <p>
            <label for="gender">Gender : </label> <!-- ini gimana ya cara bikin option gender??? -->
            <label for="pria">Pria</label>
            <input type="radio" name="pria" id="pria">
            <label for="wanita">Wanita</label>
            <input type="radio" name="wanita" id="wanita">
        </p>

         <p>
            <label for="tgl">Tanggal lahir : </label> <!-- ini gimana ya cara bikin kalender??? -->
            <input type="date" name="tgl" id="tgl">
        </p>

          <p>
            <label for="angkatan">Angkatan : </label> <!-- ini bener kah? -->
            <input type="number" name="angkatan" id="angkatan">
        </p>

        <p>
            <label for="foto">Foto : </label>
            <input type="file" name="foto" id="foto">
        </p>
        <button type="submit" name="inserts">Insert</button>  <!-- Namanya inserts biar beda sama dosen -->
        
    </form>
</body>
</html>