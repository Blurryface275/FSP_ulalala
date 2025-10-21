<?php
session_start(); // wajib sebelum manipulasi session


$_SESSION = []; // kosongin array session


if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}


session_destroy();

// kembalikan ke halaman login
header("Location: login.php");
exit;
?>
