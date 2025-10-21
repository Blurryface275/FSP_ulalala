<?php
    session_start();
?>
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
                <li><a href="change-password.php"> Ubah Password</a></li> 
    <li><a href="logout.php"> Logout</a></li>
        </ul>
    </div>

    <div class="content-box">
    <h1>Data Mahasiswa</h1>
    <?php
    $mysqli = new mysqli("localhost", "root", "", "fullstack");
    if ($mysqli->connect_errno) {
        die("Failed to connect to MySQL: " . $mysqli->connect_error);
    } 
    if (isset($_SESSION['success_message'])) {
        // Tampilkan alert sukses
        echo "<script>alert('{$_SESSION['success_message']}');</script>";
        
        // Hapus pesan
        unset($_SESSION['success_message']);
    }

    require_once("class/mahasiswa.php");
    $mahasiswa = new mahasiswa($mysqli);

    $limit = 5; // jumlah mahasiswa per page
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1; //supaya fix angka
    $offset = ($page - 1) * $limit;
    $totalMahasiswa = $mahasiswa->getTotalMahasiswa();
    $totalPages = ceil($totalMahasiswa / $limit);

    $res = $mahasiswa->displayMahasiswa($limit, $offset);

    echo "<table border=1 cell-spacing=0><th>Foto</th> <th>Nama</th> <th>NRP</th> <th>Angkatan</th> <th colspan='2'>Aksi</th>";

    while ($row = $res->fetch_assoc()) {
        echo "<tr>";
        echo "<td>";

        $fotoMhs = "uploads/" . $row['nrp'] . "." . $row['foto_extention'];

        if (file_exists($fotoMhs)) {
            echo "<img class='foto' src='" . $fotoMhs . "' alt='poster'>";
        } else {
            echo "<span class='teks-merah'>Poster tidak ditemukan</span>";
        }

        echo "</td>";

        echo "<td>" . $row['nama'] . "</td>";
        echo "<td>" . $row['nrp'] . "</td>";
        echo "<td>" . $row['angkatan'] . "</td>";
        echo "<td><a href='edit-mahasiswa.php?nrp=" . $row['nrp'] . "'>Edit</a></td>";
        echo "<td><a href='delete-mahasiswa.php?nrp=" . $row['nrp'] . "' onclick='return confirm(\"Yakin ingin menghapus mahasiswa ini?\");'>Delete</a></td>";
        
        echo "</tr>";
    }
    echo "</table>";

    echo "<div class='pagination'>";

    if ($page > 1) {
        echo "<a href='data-mahasiswa.php?page=" . ($page - 1) . "'>Prev</a>"; // kalo udah lebih dari 1 biar ada prev button
    }

    for ($i = 1; $i <= $totalPages; $i++) {
        echo "<a href='data-mahasiswa.php?page=$i'>$i</a>"; // sellau generate link sesuai dengan julah offset
    }

    if ($page < $totalPages) {
        echo "<a href='data-mahasiswa.php?page=" . ($page + 1) . "'>Next</a>"; // nambahin button next selama blom last apge

    }
    echo "</div>";
    ?>
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