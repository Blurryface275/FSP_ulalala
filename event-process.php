<?php
session_start();

$mysqli = new mysqli("localhost", "root", "", "fullstack"); 
if ($mysqli->connect_errno) {
    $_SESSION['error_message'] = "Koneksi database gagal.";
    header("Location: insert-event.php?idgrup=" . $_POST['idgrup']);
    exit;
}

require_once("Event.php"); 
$eventManager = new Event($mysqli);

if (isset($_POST['add_event'])) {
    
    // Ambil data POST
    $idgrup     = (int)($_POST['idgrup'] ?? 0);
    $judul      = $_POST['judul'];
    $tanggal    = $_POST['tanggal']; 
    $keterangan = $_POST['keterangan'];
    $jenis      = $_POST['jenis'];
    $poster_extension = null; // jika tidak ada poster

    // Cek input 
    if ($idgrup <= 0 || empty($judul) || empty($tanggal)) {
        $_SESSION['error_message'] = "Data wajib (ID Grup, Judul, Tanggal) tidak lengkap.";
        header("Location: insert-event.php?idgrup=" . $idgrup);
        exit;
    }
    
    // Menghandle Upload Poster
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] == 0) {
        $file_info = pathinfo($_FILES['poster']['name']);
        $extension = strtolower($file_info['extension']);
        
        if (strlen($extension) > 4 || !in_array($extension, ['jpg', 'jpeg', 'png'])) {
            $_SESSION['error_message'] = "Format file poster tidak didukung atau ekstensi terlalu panjang.";
            header("Location: insert-event.php?idgrup=" . $idgrup);
            exit;
        }
        
        $poster_extension = $extension;
    }
    
    try {
        $result = $eventManager->insertEventBaru(
            $idgrup, 
            $judul, 
            $tanggal,
            $keterangan, 
            $jenis, 
            $poster_extension
        );
        
        // Jika insert berhasil dan ada poster, simpan file poster dengan idevent baru
        if ($result && $poster_extension) {
            $new_filename = $result['idevent'] . '.' . $poster_extension;
            $target_dir = "posters/"; // Pastikan folder 'posters' ada dan bisa ditulis (writable)
            $target_file = $target_dir . $new_filename;
            
            if (!move_uploaded_file($_FILES['poster']['tmp_name'], $target_file)) {
                $_SESSION['error_message'] = "Event berhasil dibuat, tetapi gagal upload poster.";
            }
        }
        
        $_SESSION['success_message'] = "Event '{$judul}' berhasil ditambahkan! Kode: {$result['judul_slug']}";

        header("Location: detail-group.php?id=" . $idgrup);
        exit;

    } catch (Exception $e) {
        $_SESSION['error_message'] = "Gagal membuat event: " . $e->getMessage();
        header("Location: insert-event.php?idgrup=" . $idgrup);
        exit;
    }

} else {
    header("Location: index.php"); 
    exit;
}
?>