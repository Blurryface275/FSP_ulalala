<?php
require_once("parent.php");
class group
{
    private $mysqli;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    // Ambil semua group
    public function displayGroup($limit, $offset)
    {
        $sql = "SELECT * FROM grup ORDER BY nama asc limit ? offset ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Insert group baru
    public function insertGroupBaru($group_name, $description)
    {
        $sql = "INSERT INTO grup (group_name, description) 
                    VALUES (?, ?)";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("ss", $group_name, $description);

        if (!$stmt->execute()) {
            throw new Exception("Error saat insert: " . $stmt->error);
        }

        return true; // kalau berhasil
    }


    // Hitung total grup
    public function getTotalGroups()
    {
        $sql = "SELECT COUNT(idgrup) as total FROM grup";
        $result = $this->mysqli->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}
