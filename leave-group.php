<?php
session_start();
require_once("class/group.php");

$mysqli = new mysqli("localhost", "root", "", "fullstack");

if (!isset($_GET['id'])) {
    echo json_encode([
        "success" => false,
        "message" => "ID group tidak valid."
    ]);
    exit;
}

$groupHandler = new group($mysqli);

try {
    $groupHandler->deleteMemberGrup($_GET['id'],$_GET['username']);

    echo json_encode([
        "success" => true,
        "message" => "Group berhasil dihapus!"
    ]);
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
exit;
