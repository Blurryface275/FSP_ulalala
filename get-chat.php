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

$thread_id = $_GET['thread_id'];
$last_id = $_GET['last_id'] ?? 0;

$data = $chatObj->getChat($thread_id, $last_id);

header('Content-Type: application/json');
echo json_encode($data);