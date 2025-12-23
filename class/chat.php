<?php
require_once("parent.php");

class chat {
    private $mysqli;

    public function __construct($mysqli) {
        $this->mysqli = $mysqli;
    }

    // Fungsi Insert: Sesuai kolom idthread, username, pesan, dan waktu (NOW())
    public function insertChat($thread_id, $username, $pesan) {
        $query = "INSERT INTO chat (idthread, username_pembuat, isi, tanggal_pembuatan) VALUES (?, ?, ?, NOW())";
        $stmt = $this->mysqli->prepare($query);
        
        // i = integer (idthread), s = string (username), s = string (pesan)
        $stmt->bind_param("iss", $thread_id, $username, $pesan);
        return $stmt->execute();
    }

    // Fungsi Get: Mengambil data berdasarkan idthread dan idchat terakhir
    public function getChat($thread_id, $last_id = 0) {
        // Kita ambil semua kolom (*) sesuai permintaan Anda
        $query = "SELECT idchat, idthread, username_pembuat, isi, tanggal_pembuatan
                  FROM chat 
                  WHERE idthread = ? AND idchat > ? 
                  ORDER BY waktu ASC";
        
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param("ii", $thread_id, $last_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $chats = [];
        while ($row = $result->fetch_assoc()) {
            $chats[] = $row;
        }
        return $chats; // Mengembalikan array untuk di-encode ke JSON di file proses
    }
}