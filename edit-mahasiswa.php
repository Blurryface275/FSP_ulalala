<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mahasiswa</title>
    <link rel="stylesheet" href="login-style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <?php
    $mysqli = new mysqli("localhost", "root", "", "fullstack");
    if ($mysqli->connect_errno) {
        die("Failed to connect to MySQL: " . $mysqli->connect_error);
    }

    // --- Ambil data lama untuk ditampilkan di form
    if (isset($_GET['nrp'])) {
        $nrp_to_edit = $_GET['nrp'];
        $stmt = $mysqli->prepare("SELECT * FROM mahasiswa WHERE nrp = ?");
        $stmt->bind_param("s", $nrp_to_edit);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_assoc();
        if (!$data) {
            die("Data mahasiswa tidak ditemukan!");
        }
        $stmt->close();
    } else {
        die("NRP mahasiswa tidak ditemukan!");
    }

    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        $nama_baru   = $_POST['nama'];
        $nrp_baru    = $_POST['nrp'];
        $nrp_lama    = $_POST['nrp_lama'];
        $gender_baru = $_POST['gender'];
        $tgl_baru    = $_POST['tgl'];
        $angkatan_baru = $_POST['angkatan'];

        // Ambil extension file (jika ada upload baru)
        $ext_baru = null;
        if (!empty($_FILES['foto']['name'])) {
            $ext_baru = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $target   = "uploads/" . $nrp_baru . "." . $ext_baru;

            move_uploaded_file($_FILES['foto']['tmp_name'], $target);

            $sql = "UPDATE mahasiswa SET nrp=?, nama=?, gender=?, tanggal_lahir=?, angkatan=?, foto_extention=? WHERE nrp=?";
            $stmt_update = $mysqli->prepare($sql);
            $stmt_update->bind_param("ssssiss", $nrp_baru, $nama_baru, $gender_baru, $tgl_baru, $angkatan_baru, $ext_baru, $nrp_lama);
        } else {
            // Semisalnya ga ada upload baru → ambil ekstensi lama
            $ext_baru = $data['foto_extention'];

            $sql = "UPDATE mahasiswa SET nrp=?, nama=?, gender=?, tanggal_lahir=?, angkatan=?, foto_extention=? WHERE nrp=?";
            $stmt_update = $mysqli->prepare($sql);
            $stmt_update->bind_param("ssssiss", $nrp_baru, $nama_baru, $gender_baru, $tgl_baru, $angkatan_baru, $ext_baru, $nrp_lama);
        }

        if ($stmt_update->execute()) {
            header("Location: data-mahasiswa.php");
            exit;
        } else {
            echo "Error: " . $stmt_update->error;
        }
    }


    ?>
    
    <div class="box">
        <h2>Edit Data Mahasiswa</h2>
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