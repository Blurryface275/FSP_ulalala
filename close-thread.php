<?php
session_start();
$mysqli = new mysqli("localhost", "root", "", "fullstack");
require_once("class/thread.php");

$idthread = $_GET['thread_id'] ?? null;
$idgrup = $_GET['group_id'] ?? null;
$username_login = $_SESSION['username']; // Ambil dari session login

if ($idthread && $idgrup) {
    $threadObj = new thread($mysqli);

    // Jalankan fungsi closeThread
    if ($threadObj->closeThread($idthread, $username_login)) {
        header("Location: detail-group.php?id=$idgrup&status=success_close");
        exit;
    } else {
        echo "Gagal menutup thread atau Anda tidak memiliki akses.";
    }
} else {
    echo "Data tidak lengkap.";
}
