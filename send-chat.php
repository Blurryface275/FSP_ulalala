<?php
session_start();
if (!isset($_SESSION['username'])) {
    $_SESSION['error_message'] = "Anda harus login dahulu!";
    header('Location: login.php');
    exit();
}
$mysqli = new mysqli("localhost", "root", "", "fullstack");
if ($mysqli->connect_errno) {
    die("Failed to connect to MySQL: " . $mysqli->connect_error);
}

include "class/chat.php";

$chatObj = new chat($mysqli);

$thread_id = $_POST['thread_id'];
$pesan = $_POST['pesan'];
$username = $_SESSION['username']; // Diambil dari akun yang login

$success = $chatObj->insertChat($thread_id, $username, $pesan);
echo json_encode(['status' => $success]);