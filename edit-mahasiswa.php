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
    
    // Check if the form has been submitted
    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        $nama_baru = $_POST['nama'];
        $nrp_baru = $_POST['NRP'];
        $nrp_lama = $_POST['nrp_lama'];
        $ext_baru = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $target = "uploads/" . $nrp_baru . "." . $ext_baru;

        // Handle file upload only if a new file is selected
        if (!empty($_FILES['foto']['name'])) {
            // Delete old photo
            $stmt_old = $mysqli->prepare("SELECT foto_extension FROM mahasiswa WHERE nrp=?");
            $stmt_old->bind_param("s", $nrp_lama);
            $stmt_old->execute();
            $result = $stmt_old->get_result();
            if ($row = $result->fetch_assoc()) {
                $old_photo = "uploads/" . $nrp_lama . "." . $row['foto_extension'];
                if (file_exists($old_photo)) {
                    unlink($old_photo);
                }
            }
            $stmt_old->close();

            // Move the new file
            move_uploaded_file($_FILES['foto']['tmp_name'], $target);
        } else {
            // If no new photo, keep the old extension
            $stmt_ext = $mysqli->prepare("SELECT foto_extension FROM mahasiswa WHERE nrp=?");
            $stmt_ext->bind_param("s", $nrp_lama);
            $stmt_ext->execute();
            $ext_baru = $stmt_ext->get_result()->fetch_assoc()['foto_extension'];
            $stmt_ext->close();
        }
        
        // Update database record
        $sql = "UPDATE mahasiswa SET nrp=?, nama=?, foto_extension=? WHERE nrp=?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ssss", $nrp_baru, $nama_baru, $ext_baru, $nrp_lama);
        if ($stmt->execute()) {
            // Rename the photo file if NRP has changed
            if ($nrp_lama !== $nrp_baru) {
                rename("uploads/" . $nrp_lama . "." . $ext_baru, $target);
            }
            header("Location: data-mahasiswa.php"); // Redirect to data-mahasiswa.php
            exit;
        } else {
            echo "Error: " . $stmt->error;
        }
    }
    
    // This block runs on the initial page load via GET request
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
            <label for="NRP">NRP : </label>
            <input type="text" name="NRP" id="NRP" value="<?php echo htmlspecialchars($data['nrp']); ?>">
        </p>
        <p>
            <label for="foto">Foto : </label>
            <input type="file" name="foto" id="foto">
        </p>
        <button type="submit" name="submit">Simpan Perubahan</button>
    </form>
</body>

</html>