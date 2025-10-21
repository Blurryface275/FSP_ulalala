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
  session_start();
       if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {

    $_SESSION['error_message'] = "Anda harus login dahulu!";
 
    header('Location: login.php');
    
    exit(); 
}
    $mysqli = new mysqli("localhost", "root", "", "fullstack");
    if ($mysqli->connect_errno) {
        die("Failed to connect to MySQL: " . $mysqli->connect_error);
    }
    require_once("class/dosen.php");
    $dsn = new dosen($mysqli);
    $error_message = "";
    $npk_to_edit = '';
    $data = [];

    if (isset($_GET['npk'])) {
        $npk_to_edit = $_GET['npk'];
        $data = $dsn->fetchDosen($npk_to_edit);

        if (!$data) {
            die("Data dosen tidak ditemukan!");
        }
    } else {
        die("NPK dosen tidak ditemukan!");
    }

    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        $hasil = $dsn->executeUpdateDosen($_POST, $_FILES, $data);

        if ($hasil === true) {
            header("Location: data-dosen.php");
            exit;
        } else {
            $error_message = $hasil;

            // isi ulang data biar form tetep keisi
            $data['nama'] = $_POST['nama'];
            $data['npk'] = $_POST['npk'];
        }
    }
    ?>

    <div class="box">
        <h2>Edit Dosen</h2>
        <a href="data-dosen.php" id="tombol-panah-img">
            <img src="93634.png" alt="Ke Data Dosen"> </a>
        <?php
           if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {

    $_SESSION['error_message'] = "Anda harus login dahulu!";
 
    header('Location: login.php');
    
    exit(); 
}
        if (!empty($error_message)) {
            echo '<div class="error-warning">' . $error_message . '</div>';
        }
        ?>
        <form action="edit-dosen.php?npk=<?php echo $data['npk']; ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="npk_lama" value="<?php echo $data['npk']; ?>">

            <p>
                <label for="nama">Nama:</label><br>
                <input type="text" name="nama" id="nama" value="<?php echo $data['nama']; ?>" required>
            </p>

            <p>
                <label for="npk">NPK:</label><br>
                <input type="text" name="npk" id="npk" value="<?php echo $data['npk']; ?>" required>
            </p>

            <p>
                <strong>Foto Saat Ini:</strong><br>
                <img src="uploads/<?php echo $data['nama'] . '_' . $data['npk'] . '.' . $data['foto_extension']; ?>" width="150" style="border-radius:8px; margin-top:10px;">
            </p>

            <p>
                <label for="foto">Edit Foto:</label><br>
                <input type="file" name="foto" id="foto" accept="image/*">
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