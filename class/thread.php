<?php
require_once("parent.php");
class thread
{
    private $mysqli;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    // Fungsi untuk membuat thread baru
    public function createThread($username, $idgrup, $status)
    {
        // Query disesuaikan dengan kolom: username_pembuat, idgrup, tanggal_pembuatan, status
        $query = "INSERT INTO thread (username_pembuat, idgrup, tanggal_pembuatan, status) 
              VALUES (?, ?, NOW(), ?)";

        $stmt = $this->mysqli->prepare($query);

        // "sis" -> string (username), integer (idgrup), string (status)
        $stmt->bind_param("sis", $username, $idgrup, $status);

        return $stmt->execute();
    }
    // Fungsi untuk mengubah status menjadi 'Close'
    public function closeThread($idthread, $username_login)
    {
        // Pastikan hanya pembuat yang bisa menutup (security check di level query)
        $stmt = $this->mysqli->prepare("UPDATE thread SET status = 'Close' WHERE idthread = ? AND username_pembuat = ?");
        $stmt->bind_param("is", $idthread, $username_login);
        return $stmt->execute();
    }

    // Fungsi tambahan untuk mengambil data thread per grup
    public function getThreadsByGroup($idgrup)
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM thread WHERE idgrup = ? ORDER BY tanggal_pembuatan DESC");
        $stmt->bind_param("i", $idgrup);
        $stmt->execute();
        return $stmt->get_result();
    }
}
