<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Dosen</title>
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
    <h1>Data Dosen</h1>
    <?php
    $mysqli = new mysqli("localhost", "root", "", "fullstack");
    if ($mysqli->connect_errno)
        die("Failed to connect to MySQL :" . $mysqli->connect_error);

    $sql = "select * from dosen";

    $stmt = $mysqli->prepare($sql);
    $stmt->execute();
    $res = $stmt->get_result();

    echo "<table border=1 cell-spacing=0><th>Foto</th> <th>Nama</th> <th>NPK</th> <th colspan='2'>Aksi</th>";

    while ($row = $res->fetch_assoc()) {
        echo "<tr>";
        echo "<td>";

        $fotoDosen = "uploads/" . $row['npk'] . "." . $row['foto_extension'];

        // cek apakah file benar-benar ada di folder
        if (file_exists($fotoDosen)) {
            echo "<img class='foto' src='" . $fotoDosen . "' alt='poster'>";
        } else {
            echo "<span class='teks-merah'>Poster tidak ditemukan</span>";
        }

        echo "</td>";

        echo "<td>" . $row['nama'] . "</td>";
        echo "<td>" . $row['npk'] . "</td>";

        echo "<td><a href='edit-dosen.php?npk=" . $row['npk'] . "'>Edit</a></td>";
        echo "<td><a href='delete-dosen.php?npk=" . $row['npk'] . "' onclick='return confirm(\"Yakin ingin menghapus dosen ini?\");'>Delete</a></td>";
        echo "<input type='hidden' name='npk_lama' value='" . $row['npk'] . "'>";
        echo "</tr>";
    }

    echo "</table>";
    ?>
</body>

</html>