<?php
session_start(); 

if (!isset($_SESSION['username'])) { 
    $_SESSION['error_message'] = "Anda harus login dahulu!";
    header('Location: login.php');
    exit(); 
}

$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    die("Koneksi gagal: " . $mysqli->connect_error);
}

require_once("class/group.php");
$groupHandler = new group($mysqli);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $groupName  = $_POST['group_name']; 
    $deskripsi  = $_POST['description'];
    $group_type = $_POST['group_type'];

    if (empty($groupName) || empty($deskripsi)) {
        $_SESSION['error_message'] = "Nama Group dan Deskripsi tidak boleh kosong.";
        header('Location: insert-group.php');
        exit();
    }
    
    try {
        $creator_username = $_SESSION['username']; 
        $new_group_data = $groupHandler->insertGroupBaru($groupName, $deskripsi,$creator_username,$group_type); 
        
        $new_group_id = $new_group_data['idgrup'];
        $registration_code = $new_group_data['kode_pendaftaran'];

        // Redirect Sukses
        $_SESSION['success_message'] = "Group berhasil dibuat! Kode pendaftaran: " . $registration_code;
        header("Location: index.php?id=" . $new_group_id . "&code=" . $registration_code);
        exit();

    } catch (Exception $e) {
        // Penanganan error
        $_SESSION['error_message'] = "Gagal membuat Group: " . $e->getMessage();
        header('Location: insert-group.php');
        exit();
    }
} else {
    header("Location: index.php"); 
    exit();
}
?>