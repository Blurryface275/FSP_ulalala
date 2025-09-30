<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mahasiswa</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php
$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: " . $mysqli->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $nama_baru   = $_POST['nama'];
    $nrp_baru    = $_POST['nrp'];
    $nrp_lama    = $_POST['nrp_lama'];
    $gender_baru = $_POST['gender'];
    $tgl_baru    = $_POST['tgl'];
    $angkatan_baru = $_POST['angkatan'];

    // Ambil extension file (jika ada upload baru)
    $ext_baru = null;
    if (!empty($_FILES['foto']['name'])) {
        $ext_baru = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $target   = "uploads/" . $nrp_baru . "." . $ext_baru;

        // Hapus foto lama
        $stmt_old = $mysqli->prepare("SELECT foto_extention FROM mahasiswa WHERE nrp=?");
        $stmt_old->bind_param("s", $nrp_lama);
        $stmt_old->execute();
        $result = $stmt_old->get_result();
        if ($row = $result->fetch_assoc()) {
            $old_photo = "uploads/" . $nrp_lama . "." . $row['foto_extention'];
            if (file_exists($old_photo)) {
                unlink($old_photo);
            }
        }
        $stmt_old->close();

        move_uploaded_file($_FILES['foto']['tmp_name'], $target);
    } else {
        // Kalau tidak ada upload baru → ambil ekstensi lama
        $stmt_ext = $mysqli->prepare("SELECT foto_extention FROM mahasiswa WHERE nrp=?");
        $stmt_ext->bind_param("s", $nrp_lama);
        $stmt_ext->execute();
        $ext_baru = $stmt_ext->get_result()->fetch_assoc()['foto_extention'];
        $stmt_ext->close();
    }

    // Update semua kolom
    $sql = "UPDATE mahasiswa 
            SET nrp=?, nama=?, gender=?, tanggal_lahir=?, angkatan=?, foto_extention=? 
            WHERE nrp=?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("sssssss", $nrp_baru, $nama_baru, $gender_baru, $tgl_baru, $angkatan_baru, $ext_baru, $nrp_lama);

    if ($stmt->execute()) {
        // Rename file kalau NRP berubah
        if ($nrp_lama !== $nrp_baru && !empty($ext_baru)) {
            rename("uploads/" . $nrp_lama . "." . $ext_baru, "uploads/" . $nrp_baru . "." . $ext_baru);
        }
        header("Location: data-mahasiswa.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}

// --- Ambil data lama untuk ditampilkan di form
if (isset($_GET['nrp'])) {
    $nrp_to_edit = $_GET['nrp'];
    $stmt = $mysqli->prepare("SELECT * FROM mahasiswa WHERE nrp = ?");
    $stmt->bind_param("s", $nrp_to_edit);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
    if (!$data) {
        die("Data mahasiswa tidak ditemukan!");
    }
    $stmt->close();
} else {
    die("NRP mahasiswa tidak ditemukan!");
}
?>

<h2>Edit Mahasiswa</h2>
<form action="edit-mahasiswa.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="nrp_lama" value="<?php echo htmlspecialchars($data['nrp']); ?>">
    <p>
        <label for="nama">Nama : </label>
        <input type="text" name="nama" id="nama" value="<?php echo htmlspecialchars($data['nama']); ?>">
    </p>
    <p>
        <label for="nrp">NRP : </label>
        <input type="text" name="nrp" id="nrp" value="<?php echo htmlspecialchars($data['nrp']); ?>">
    </p>
    <p>
        <label>Gender : </label>
        <input type="radio" name="gender" value="Pria" <?php if($data['gender']=="Pria") echo "checked"; ?>> Pria
        <input type="radio" name="gender" value="Wanita" <?php if($data['gender']=="Wanita") echo "checked"; ?>> Wanita
    </p>
    <p>
        <label for="tgl">Tanggal Lahir : </label>
        <input type="date" name="tgl" id="tgl" value="<?php echo htmlspecialchars($data['tanggal_lahir']); ?>">
    </p>
    <p>
        <label for="angkatan">Angkatan : </label>
        <input type="number" name="angkatan" id="angkatan" value="<?php echo htmlspecialchars($data['angkatan']); ?>">
    </p>
    <p>
        <label for="foto">Foto : </label>
        <input type="file" name="foto" id="foto">
        <br>
        Foto sekarang: <img src="uploads/<?php echo $data['nrp'] . "." . $data['foto_extention']; ?>" width="100">
    </p>
    <button type="submit" name="submit">Simpan Perubahan</button>
</form>
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
