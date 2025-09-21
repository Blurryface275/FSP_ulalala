<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mahasiswa</title>
    <link rel="stylesheet" href="style.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <?php
    $mysqli = new mysqli("localhost", "root", "", "fullstack");
    if ($mysqli->connect_errno) {
        die("Failed to connect to MySQL: " . $mysqli->connect_error);
    }

    // cek apakah ada npk
    if (!isset($_GET['npk'])) {
        echo "NRP tidak ditemukan!";
    }
    $npk = $_GET['nrp'];
    // kalo form  edit udah disubmit nanti diarahin ke sini
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $nama_baru = $_POST['nama'];
        $nrp_baru = $_POST['NRP'];
        $nrp_lama = $_POST['nrp_lama'];
        $ext_baru = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);

        $target = "uploads/" . $nrp_baru . "." . $ext_baru; //kasih nama pakai npk dan extension

        // Pindahkan file ke folder uploads
        move_uploaded_file($_FILES['foto']['tmp_name'], $target);

        $sql = "UPDATE mahasiswa SET nrp=?, nama=?, foto_extension=? WHERE nrp=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ssss", $nrp_baru, $nama_baru, $ext_baru, $nrp_lama);
        if ($stmt->execute()) {
            header("Location: data-dosen.php");
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
    }


    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $nrp = $_GET['npk'] ?? null;
        if (!$nrp) die("NPK tidak ditemukan!");

        $stmt = $mysqli->prepare("SELECT * FROM mahasiswa WHERE nrp=?");
        $stmt->bind_param("s", $nrp);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_assoc();
        if (!$data) die("Data tidak ditemukan!");
    }
    ?>
    <h2>Edit Dosen</h2>
    <form action="edit-dosen.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="nrp_lama" value="<?php echo $data['npk']; ?>">
        <p>
            <label for="nama">Nama : </label>
            <input type="text" name="nama" id="nama">
        </p>
        <p>
            <label for="NRP">NPK : </label>
            <input type="text" name="NRP" id="NRP">
        </p>
        <p>
            <label for="foto">Foto : </label>
            <input type="file" name="foto" id="foto">
        </p>
        <button type="submit" name="insert">Insert</button>
    </form>
</body>

</html>