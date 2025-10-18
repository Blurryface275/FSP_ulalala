<?php

require_once("parent.php");

class akun extends classParent
{
    // constractor
    public function __construct()
    {
        parent::__construct();
    }
    // methods
    //untuk pengecekan di regis
    public function usernameExists($username)
    {
        $sql = "SELECT username FROM akun WHERE username=?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res->num_rows > 0;
    }

    public function getAccount($username, $password)
    {
        $sql = "SELECT username, password, isadmin, nrp_mahasiswa, npk_dosen FROM akun WHERE username=?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        if ($row && password_verify($password, $row['password'])) {
            return $row; // Login berhasil
        }
        return null; // Login gagal
    }

    public function updatePassword($username, $oldPassword, $newPassword) 
    {
        // 1. Ambil hash password lama
        $stmt = $this->mysqli->prepare("SELECT password FROM akun WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            return false; 
        }
        
        $row = $result->fetch_assoc();
        $hashed_old_password = $row['password'];
        $stmt->close();
        
        // 2. Verifikasi password lama
        if (!password_verify($oldPassword, $hashed_old_password)) {
            return false; // Password lama salah
        }

        // 3. Hash password baru
        $hashed_new_password = password_hash($newPassword, PASSWORD_BCRYPT);
        
        // 4. Update password baru di database
        $stmt_update = $this->mysqli->prepare("UPDATE akun SET password = ? WHERE username = ?");
        $stmt_update->bind_param("ss", $hashed_new_password, $username);
        
        return $stmt_update->execute();
    }


    public function insertAkunMahasiswa($password, $nrp, $isadmin = 0)
    {
        $encrypt_pwd = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO akun (username, password, nrp_mahasiswa, isadmin) VALUES (?, ?, ?, ?)";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("sssi", $nrp, $encrypt_pwd, $nrp, $isadmin);
        return $stmt->execute();
    }

    public function insertAkunDosen($password, $npk, $isadmin = 0)
    {
        $encrypt_pwd = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO akun (username, password, npk_dosen, isadmin) VALUES (?, ?, ?, ?)";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("sssi", $npk, $encrypt_pwd, $npk, $isadmin);
        return $stmt->execute();
    }
}

