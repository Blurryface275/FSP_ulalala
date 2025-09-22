<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa</title>
    <link rel="stylesheet" href="style.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .foto {
            max-width: 150px;

        }
    </style>
</head>

<body>
    <h1>Data Mahasiswa</h1>
  <?php
$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: " . $mysqli->connect_error);
}

$sql = "select * from mahasiswa";

$stmt = $mysqli->prepare($sql);

// Check if the prepare() call failed
if ($stmt === false) {
    die("Error preparing statement: " . $mysqli->error);
}

$stmt->execute();
$res = $stmt->get_result();

echo "<table border=1 cell-spacing=0><th>Foto</th> <th>Nama</th> <th>NRP</th> <th colspan='2'>Aksi</th>";

while ($row = $res->fetch_assoc()) {
    echo "<tr>";
    echo "<td>";

    $fotoMhs = "uploads/" . $row['nrp'] . "." . $row['foto_extension'];

    if (file_exists($fotoMhs)) {
        echo "<img class='foto' src='" . $fotoMhs . "' alt='poster'>";
    } else {
        echo "<span class='teks-merah'>Poster tidak ditemukan</span>";
    }

    echo "</td>";

    echo "<td>" . $row['nama'] . "</td>";
    echo "<td>" . $row['nrp'] . "</td>";
    echo "<td><a href='edit-mahasiswa.php?npk=" . $row['nrp'] . "'>Edit</a></td>";
    echo "<td><a href='delete-mahasiswa.php?npk=" . $row['nrp'] . "' onclick='return confirm(\"Yakin ingin menghapus mahasiswa ini?\");'>Delete</a></td>";
    echo "<input type='hidden' name='nrp_lama' value='" . $row['nrp'] . "'>";
    echo "</tr>";
}

echo "</table>";

// Close the statement and connection
$stmt->close();
$mysqli->close();
?>
</body>

</html>