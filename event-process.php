<?php
session_start();

// 1. Konfigurasi dan Koneksi Database
// Ganti dengan detail koneksi Anda
$mysqli = new mysqli("localhost", "root", "", "fullstack"); 
if ($mysqli->connect_errno) {
    $_SESSION['error_message'] = "Koneksi database gagal.";
    header("Location: insert-event.php?idgrup=" . $_POST['idgrup']);
    exit;
}

// 2. Include Class Event
require_once("Event.php"); 
$eventManager = new Event($mysqli);

if (isset($_POST['add_event'])) {
    
    // Ambil data POST
    $idgrup     = (int)($_POST['idgrup'] ?? 0);
    $judul      = trim($_POST['judul'] ?? '');
    $tanggal    = trim($_POST['tanggal'] ?? ''); // Datetime-local akan memberi format YYYY-MM-DDThh:mm
    $keterangan = trim($_POST['keterangan'] ?? '');
    $jenis      = trim($_POST['jenis'] ?? '');
    
    $poster_extension = null; // Default: tidak ada poster

    // Cek input dasar
    if ($idgrup <= 0 || empty($judul) || empty($tanggal)) {
        $_SESSION['error_message'] = "Data wajib (ID Grup, Judul, Tanggal) tidak lengkap.";
        header("Location: insert-event.php?idgrup=" . $idgrup);
        exit;
    }
    
    // 3. Menghandle Upload Poster
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] == 0) {
        $file_info = pathinfo($_FILES['poster']['name']);
        $extension = strtolower($file_info['extension']);
        
        // Batasan (Sesuai skema varchar(4))
        if (strlen($extension) > 4 || !in_array($extension, ['jpg', 'jpeg', 'png'])) {
            $_SESSION['error_message'] = "Format file poster tidak didukung atau ekstensi terlalu panjang.";
            header("Location: insert-event.php?idgrup=" . $idgrup);
            exit;
        }
        
        // Simpan ekstensi untuk dimasukkan ke DB
        $poster_extension = $extension;
    }
    
    try {
        // 4. Panggil fungsi insertEventBaru
        $result = $eventManager->insertEventBaru(
            $idgrup, 
            $judul, 
            $tanggal, // Format YYYY-MM-DDThh:mm sudah diterima oleh DATETIME MySQL
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
                // Jika gagal move, ini hanya peringatan (data event sudah masuk DB)
                $_SESSION['error_message'] = "Event berhasil dibuat, tetapi gagal upload poster.";
            }
        }
        
        // Jika tidak ada error, set pesan sukses
        $_SESSION['success_message'] = "Event '{$judul}' berhasil ditambahkan! Kode: {$result['judul_slug']}";

        // Redirect ke halaman detail grup atau halaman list event
        header("Location: detail-group.php?id=" . $idgrup);
        exit;

    } catch (Exception $e) {
        $_SESSION['error_message'] = "Gagal membuat event: " . $e->getMessage();
        header("Location: insert-event.php?idgrup=" . $idgrup);
        exit;
    }

} else {
    // Jika tidak diakses melalui POST submit
    header("Location: index.php"); 
    exit;
}
?>