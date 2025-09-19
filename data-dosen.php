<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Dosen</title>
    <style>
        .foto{
            max-width:150px;
           
        }
    </style>
</head>
<body>
    <h1>Data Dosen</h1>
<?php
    $mysqli = new mysqli("localhost","root","", "fullstack");
    if ($mysqli -> connect_errno)
        die("Failed to connect to MySQL :". $mysqli->connect_error);

        $sql = "select * from dosen";

        $stmt = $mysqli->prepare($sql);
        $stmt->execute();
        $res=$stmt->get_result();

        echo "<table border=1 cell-spacing=0><th>Foto</th> <th>Nama</th> <th>NPK</th>";

        while($row = $res->fetch_assoc()) {
            echo "<tr>";
            echo "<td>";
            
            $fotoDosen = "uploads/".$row['npk'].".".$row['foto_extension'];
            
            // cek apakah file benar-benar ada di folder
            if (file_exists($fotoDosen)) {
                echo "<img class='foto' src='".$fotoDosen."' alt='poster'>";
            } else {
                echo "<span class='teks-merah'>Poster tidak ditemukan</span>";
            }
            
            echo "</td>";
            
        echo "<td>".$row['nama']."</td>";
        echo "<td>".$row['npk']."</td>" ; 
        
        echo"</tr>";
        }

        echo "</table>";
?>
</body>
</html>
