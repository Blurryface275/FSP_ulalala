<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Dosen</title>
</head>
<body>
<?php
$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: " . $mysqli->connect_error);
}

if (!isset($_GET['npk'])) {
    die("NPK tidak ditemukan!");
}
if ($_SERVER['REQUEST_METHOD'] == 'GET') 
$npk =  $_GET['npk'];

$stmt = $mysqli->prepare("DELETE FROM dosen WHERE npk=?");
$stmt->bind_param("s", $npk);

if ($stmt->execute()) {
    header("Location: data-dosen.php"); // kembali ke halaman utama
    exit;
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
?>

</body>
</html>