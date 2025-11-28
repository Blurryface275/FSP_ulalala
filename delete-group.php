<?php
session_start();
require_once("class/group.php");

$mysqli = new mysqli("localhost", "root", "", "fullstack");

if (!isset($_GET['id'])) {
    die("ID group tidak valid.");
}

$groupHandler = new group($mysqli);

$groupHandler->deleteGroup($_GET['id']);

$_SESSION['success_message'] = "Group berhasil dihapus!";
header("Location: data-group.php");
exit();
