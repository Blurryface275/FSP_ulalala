<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Event</title>
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

    if (!isset($_GET['event_id'])) {
        die("Event ID tidak ditemukan!");
    }
    if ($_SERVER['REQUEST_METHOD'] == 'GET')
        $event_id =  $_GET['event_id'];
    $group_id = $_GET['group_id'];

    $stmt = $mysqli->prepare("DELETE FROM event WHERE idevent=? AND idgrup=?");
    $stmt->bind_param("ii", $event_id, $group_id);

    if ($stmt->execute()) {
        header("Location: detail-group.php?id=" . urlencode($_GET['group_id'])); // kembali ke halaman utama
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $mysqli->close();
    ?>
</body>

</html>