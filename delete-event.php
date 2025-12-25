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

    require_once("class/event.php");
    $eventObj = new Event($mysqli);

    // Validasi input
    $event_id = $_GET['event_id'] ?? null;
    $group_id = $_GET['group_id'] ?? null;

    if (!$event_id || !$group_id) {
        die("Parameter tidak lengkap!");
    }

    // Panggil fungsi dari class
    if ($eventObj->deleteEvent($event_id, $group_id)) {
        // Redirect dilakukan di sini
        header("Location: detail-group.php?id=" . urlencode($group_id));
        exit;
    } else {
        echo "Gagal menghapus event.";
    }
    ?>
</body>

</html>