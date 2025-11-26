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

    //untuk buat kode regis grup
    public function generateRegistrationCode($length = 8)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';
        // Loop untuk mengenerate karakter
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $code;
    }

    // Insert group baru
    public function insertGroupBaru($group_name, $description, $creator_username)
    {
        $registration_code = $this->generateRegistrationCode(8);
        $current_datetime = date('Y-m-d H:i:s'); 
        $group_type = 'Privat'; // Default jenis grup 
        
        $sql = "INSERT INTO grup (nama, deskripsi, kode_pendaftaran, username_pembuat, tanggal_pembentukan, jenis) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->mysqli->prepare($sql);
        
        $stmt->bind_param("ssssss", $group_name, $description, $registration_code, $creator_username, $current_datetime, $group_type);

        if (!$stmt->execute()) {
            throw new Exception("Error saat insert: " . $stmt->error);
        }

        return [
            'idgrup' => $this->mysqli->insert_id, 
            'kode_pendaftaran' => $registration_code
        ];
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
