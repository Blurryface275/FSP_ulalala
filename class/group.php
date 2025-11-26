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

    // jadi private karena dipakai dalam class ini saja
    private function generateRegistrationCode($length = 8)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $code;
    }

    public function insertGroupBaru($group_name, $description, $creator_username, $group_type = 'Privat')
    {
        $registration_code = $this->generateRegistrationCode(8);
        $current_datetime = date('Y-m-d H:i:s');

        $sql = "INSERT INTO grup (nama, deskripsi, kode_pendaftaran, username_pembuat, tanggal_pembentukan, jenis) 
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->mysqli->prepare($sql);

        $stmt->bind_param("ssssss", $group_name, $description, $registration_code, $creator_username, $current_datetime, $group_type);

        if (!$stmt->execute()) {
            throw new Exception("Error saat insert group: " . $stmt->error);
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

    // Insert Member Grup
    public function insertMemberGrup($idgrup, $nrp)
    {
        // 1. Cari username dari tabel akun
        $sql = "SELECT username FROM akun WHERE nrp_mahasiswa = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("s", $nrp);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 0) {
            throw new Exception("NRP tidak ditemukan pada tabel akun.");
        }

        $row = $result->fetch_assoc();
        $username = $row['username'];

        // 2. Insert ke member_grup
        $sqlInsert = "INSERT INTO member_grup (idgrup, username) VALUES (?, ?)";
        $stmtInsert = $this->mysqli->prepare($sqlInsert);
        $stmtInsert->bind_param("is", $idgrup, $username);

        if (!$stmtInsert->execute()) {
            // duplicate entry code = 1062
            if ($stmtInsert->errno == 1062) {
                return false; // sudah ada
            }
            throw new Exception("Error saat insert member grup: " . $stmtInsert->error);
        }

        return $stmtInsert->affected_rows > 0;
    }


    //hapus member
    public function deleteMemberGrup($idgrup, $username)
    {
        $sql = "DELETE FROM member_grup WHERE idgrup = ? AND username = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("is", $idgrup, $username);

        if (!$stmt->execute()) {
            throw new Exception("Error saat menghapus member grup: " . $stmt->error);
        }

        // Mengembalikan TRUE jika ada 1 baris atau lebih yang terhapus
        return $stmt->affected_rows > 0;
    }
}
