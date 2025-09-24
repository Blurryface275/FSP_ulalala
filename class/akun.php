<?php

require_once("parent.php");

class akun extends
classParent
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

    //untuk pengecekan di login
    public function getAccount($username, $password)
    {
        $sql = "SELECT username, password, isadmin, nrp_mahasiswa, npk_dosen 
            FROM akun 
            WHERE username=? AND password=?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); // kalau ada -> return row, kalau tidak -> null
    }


    public function insertMahasiswa($username, $password, $nrp, $isadmin = 0)
    {
        $sql = "INSERT INTO akun (username, password, nrp_mahasiswa, isadmin) VALUES (?, ?, ?, ?)";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("sssi", $username, $password, $nrp, $isadmin);
        return $stmt->execute();
    }

    public function insertDosen($username, $password, $npk, $isadmin = 0)
    {
        $sql = "INSERT INTO akun (username, password, npk_dosen, isadmin) VALUES (?, ?, ?, ?)";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("sssi", $username, $password, $npk, $isadmin);
        return $stmt->execute();
    }
}
