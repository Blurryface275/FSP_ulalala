<?php
    $mysqli = new mysqli("localhost", "root", "", "fullstack");
    if ($mysqli->connect_errno) {
       die("Failed to connect to MySQL: " . $mysqli->connect_error);
    }

    if (isset($_POST['insert'])){
        $nama = $_POST['nama']; // nama dosen
        $NPK = $_POST['NPK']; // NPK dosen
    }
  
    $foto = $_FILES['foto'];
    $sql = "insert into dosen (npk, nama, foto_extension) values(?,?,?)";

    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $target = "uploads/" . $NPK . "." . $ext; //kasih nama pakai npk dan extension

    // Pindahkan file ke folder uploads
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $target)) {
        // Simpan ke database
        $sql = "INSERT INTO dosen (npk, nama, foto_extension) VALUES (?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("sss", $NPK, $nama, $ext);
        $stmt->execute();

        echo "Data dosen berhasil disimpan!";
    } else {
        echo "Gagal upload foto!";
    }

    
    header("Location: data-dosen.php");
    exit;
?>
<?php
    $mysqli = new mysqli("localhost", "root", "", "fullstack");
    if ($mysqli->connect_errno) {
       die("Failed to connect to MySQL: " . $mysqli->connect_error);
    }

    if (isset($_POST['inserts'])){//ini 'inserts' sesuai yang insert mhs namanya
        $nama = $_POST['nama']; // nama mhs
        $NRP = $_POST['NRP']; // nrp
    }
  
    $foto = $_FILES['foto'];
    $sql = "insert into mahasiswa (nrp, nama, gender, tanggal_lahir, angkatan, foto_extension) values(?,?,?,?,?,?)";

    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $target = "uploads/" . $NRP . "." . $ext; //kasih nama pakai nrp dan extension

    // Pindahkan file ke folder uploads
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $target)) {
        // Simpan ke database
        $sql = "INSERT INTO mahasiswa (nrp, nama, gender, tanggal_lahir, angkatan, foto_extension) VALUES (?,?,?,?,?,?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ssedss", $NPK, $nama, $ext);//ini enum sebutannnya e kah?
        $stmt->execute();

        echo "Data Mahasiswa berhasil disimpan!";
    } else {
        echo "Gagal upload foto!";
    }

    
    header("Location: data-dosen.php");
    exit;
?>