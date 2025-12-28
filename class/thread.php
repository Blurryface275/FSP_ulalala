<?php
require_once("parent.php");
class thread
{
    private $mysqli;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function createThread($username, $idgrup, $status)
    {
        $query = "INSERT INTO thread (username_pembuat, idgrup, tanggal_pembuatan, status) 
                  VALUES (?, ?, NOW(), ?)";

        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param("sis", $username, $idgrup, $status);
        return $stmt->execute();
    }

    // Fungsi untuk mengubah status thread menjadi 'Close'
    public function closeThread($idthread, $username_login)
    {
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

    // fungsi untuk mengecek isi thread dalam grup
    public function checkThread($idthread)
    {
        $query = "SELECT status FROM thread WHERE idthread = ?";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param("i", $idthread);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $status_thread = $row['status'];
        }
        return $stmt->get_result();
    }

    public function getStatusThread($thread_id)
    {
        $sql = "SELECT status FROM thread WHERE idthread = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("i", $thread_id);
        $stmt->execute();

        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            // Langsung kembalikan string status-nya (misal: 'Open')
            return $row['status'];
        }

        // defaultnya jika thread tidak ditemukan
        return 'Closed';
    }

    // Di dalam class thread
    public function getThreads($group_id)
    {
        $query = "SELECT idthread, tanggal_pembuatan, username_pembuat, status
              FROM `thread`
              WHERE idgrup = ?
              ORDER BY tanggal_pembuatan DESC";

        $stmt = $this->mysqli->prepare($query);
        if (!$stmt) {
            die("Prepare error: " . $this->mysqli->error);
        }

        $stmt->bind_param("i", $group_id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
