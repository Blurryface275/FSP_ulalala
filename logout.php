<?php
session_start(); // wajib sebelum manipulasi session

// Hapus semua data di session
$_SESSION = []; // kosongkan array session

// Hapus cookie session (opsional tapi disarankan)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hancurkan session sepenuhnya
session_destroy();

// Arahkan ke halaman login
header("Location: login.php");
exit;
?>
