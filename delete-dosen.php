<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Dosen</title>
    <link rel="stylesheet" href="style.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <?php
    session_start();
    if (!isset($_SESSION['username'])) {
        $_SESSION['error_message'] = "Anda harus login dahulu!";
        header('Location: login.php');
        exit();
    }
    $mysqli = new mysqli("localhost", "root", "", "fullstack");
    if ($mysqli->connect_errno) {
        die("Failed to connect to MySQL: " . $mysqli->connect_error);
    }

    require_once("class/dosen.php");
    $dosenObj = new dosen($mysqli);

    if (!isset($_GET['npk'])) {
        die("NPK tidak ditemukan!");
    }

    $npk = $_GET['npk'];

    // Panggil fungsi dari class
    if ($dosenObj->deleteDosen($npk)) {
        header("Location: data-dosen.php");
        exit;
    } else {
        echo "Gagal menghapus data dosen.";
    }
    ?>

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