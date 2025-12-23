<?php
require_once("parent.php");

class chat
{
    private $mysqli;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    // Di dalam class chat
    public function insertChat($thread_id, $username, $pesan)
    {
        // Gunakan kolom 'isi' dan 'tanggal_pembuatan'
        $query = "INSERT INTO chat (idthread, username_pembuat, isi, tanggal_pembuatan) VALUES (?, ?, ?, NOW())";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param("iss", $thread_id, $username, $pesan);
        return $stmt->execute();
    }

    public function getChat($thread_id, $last_id = 0)
    {
        // Sesuaikan nama kolom
        $query = "SELECT idchat, idthread, username_pembuat as username, isi, tanggal_pembuatan 
              FROM chat 
              WHERE idthread = ? AND idchat > ? 
              ORDER BY tanggal_pembuatan ASC";

        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param("ii", $thread_id, $last_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
