<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Mahasiswa</title>
    <link rel="stylesheet" href="style.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <?php
    $mysqli = new mysqli("localhost", "root", "", "fullstack");
    if ($mysqli->connect_errno) {
        die("Failed to connect to MySQL: " . $mysqli->connect_error);
    }

    if (!isset($_GET['nrp'])) {
        die("NRP tidak ditemukan!");
    }
    if ($_SERVER['REQUEST_METHOD'] == 'GET')
        $nrp =  $_GET['nrp'];

    $stmt = $mysqli->prepare("DELETE FROM mahasiswa WHERE nrp=?");
    $stmt->bind_param("s", $nrp);

    if ($stmt->execute()) {
        header("Location: data-mahasiswa.php"); // kembali ke halaman utama
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $mysqli->close();
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