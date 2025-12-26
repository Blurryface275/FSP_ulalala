<?php
session_start();

if (!isset($_SESSION['username'])) {
    die("Akses ditolak. Silakan login terlebih dahulu.");
}

$mysqli = new mysqli("localhost", "root", "", "fullstack"); 

require_once("class/thread.php"); 
$threadManager = new thread($mysqli);

if (isset($_POST['add_thread'])) {
    
    $idgrup   = (int)($_POST['idgrup'] ?? 0);
    $status   = $_POST['status'] ?? 'Open'; // Mengambil status dari pilihan user
    $username = $_SESSION['username'];      // Mengambil username dari login aktif

    if ($idgrup <= 0) {
        $_SESSION['error_message'] = "ID Grup tidak valid.";
        header("Location: insert-thread.php?idgrup=" . $idgrup);
        exit;
    }
    
    try {
        // Panggil fungsi dengan urutan: $username, $idgrup, $status
        $result = $threadManager->createThread($username, $idgrup, $status);
        
        if ($result) {
            $_SESSION['success_message'] = "Thread baru berhasil dibuat!";
            header("Location: detail-group.php?id=" . $idgrup);
            exit;
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Gagal: " . $e->getMessage();
        header("Location: insert-thread.php?idgrup=" . $idgrup);
        exit;
    }
}