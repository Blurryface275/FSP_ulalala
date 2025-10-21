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
</head>

<body>
    <?php
    $mysqli = new mysqli("localhost", "root", "", "fullstack");
    if ($mysqli->connect_errno) {
        die("Failed to connect to MySQL: " . $mysqli->connect_error);
    }
    // cek apakah ada npk
    if (!isset($_GET['npk'])) {
        echo "NPK tidak ditemukan!";
    }

    $npk = $_GET['npk'];

    // kalo form  edit udah disubmit nanti diarahin ke sini
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $nama_baru = $_POST['nama'];
        $npk_baru  = $_POST['NPK'];
        $npk_lama  = $_POST['npk_lama'];

        // Cek apakah ada file foto baru yang diunggah
        if (!empty($_FILES['foto']['name'])) {
            $ext_baru = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $target   = "uploads/" . $nama_baru . "_" . $npk_baru . "." . $ext_baru;
            move_uploaded_file($_FILES['foto']['tmp_name'], $target);

            $sql = "UPDATE dosen SET npk=?, nama=?, foto_extension=? WHERE npk=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("ssss", $npk_baru, $nama_baru, $ext_baru, $npk_lama);
        } else {
            // kalo misal gada foto baru
            $target   = "uploads/" . $nama_baru . "_" . $npk_baru . "." . $ext_baru;
            move_uploaded_file($_FILES['foto']['tmp_name'], $target);
            $sql = "UPDATE dosen SET npk=?, nama=?, foto_extension=? WHERE npk=?";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sss", $npk_baru, $nama_baru, $ext_baru, $npk_lama);
        }

        if ($stmt->execute()) {
            header("Location: data-dosen.php");
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
    }
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $npk = $_GET['npk'] ?? null;
        if (!$npk) die("NPK tidak ditemukan!");

        $stmt = $mysqli->prepare("SELECT * FROM dosen WHERE npk=?");
        $stmt->bind_param("s", $npk);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_assoc();
        if (!$data) die("Data tidak ditemukan!");
    }
    ?>
    
    <div class="box">
        <h2>Edit Dosen</h2>

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