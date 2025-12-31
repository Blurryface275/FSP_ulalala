<?php
require_once("parent.php");

class chat
{
    private $mysqli;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function insertChat($thread_id, $username, $pesan)
    {
        $query = "INSERT INTO chat (idthread, username_pembuat, isi, tanggal_pembuatan) VALUES (?, ?, ?, NOW())";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param("iss", $thread_id, $username, $pesan);
        return $stmt->execute();
    }

    public function getChat($thread_id, $last_id = 0)
    {
        $query = "SELECT 
                c.idchat, 
                c.idthread, 
                c.username_pembuat AS username, 
                COALESCE(m.nama, d.nama) AS nama_asli, 
                c.isi, 
                c.tanggal_pembuatan 
              FROM chat c
              INNER JOIN akun a ON c.username_pembuat = a.username
              LEFT JOIN mahasiswa m ON a.nrp_mahasiswa = m.nrp
              LEFT JOIN dosen d ON a.npk_dosen = d.npk
              WHERE c.idthread = ? AND c.idchat > ? 
              ORDER BY c.tanggal_pembuatan ASC";

        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param("ii", $thread_id, $last_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
