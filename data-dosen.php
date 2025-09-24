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
    <style>
        .foto {
            max-width: 150px;
        }
    </style>
</head>

<body>
    <div id="sidebar" class="sidebar">
        <div style="display: flex; align-items: center; gap: 10px; padding: 0 20px; margin-bottom: 20px;">
            <div class="toggle-btn" id="toggle-btn">☰</div>
        </div>
        <ul>
            <li><a href="data-dosen.php">Data Dosen</a></li>
            <li><a href="data-mahasiswa.php">Data Mahasiswa</a></li>
            <li><a href="insert-dosen.php">Tambah Dosen</a></li>
            <li><a href="insert-mahasiswa.php">Tambah Mahasiswa</a></li>
        </ul>
    </div>
    
    <div class="content-box">
    <h1>Data Dosen</h1>
    <?php
    $mysqli = new mysqli("localhost", "root", "", "fullstack");
    if ($mysqli->connect_errno){
        die("Failed to connect to MySQL :" . $mysqli->connect_error);
    }
    
    require_once("class/dosen.php");
    $dosen = new dosen($mysqli);
    
    $res = $dosen->displayDosen();

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
    </div>
</body>

</html>