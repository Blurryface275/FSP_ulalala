<?php

require_once("parent.php");

class dosen
{
    private $mysqli;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    // Ambil semua dosen
    public function displayDosen()
    {
        $sql = "SELECT * FROM dosen";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Insert dosen baru
    public function insertDosenBaru($npk, $nama, $foto)
    {
        // Validasi ekstensi foto
        $valid_extension = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $valid_extension)) {
            throw new Exception("Ekstensi file tidak valid! Hanya jpg/jpeg/png.");
        }

        // Nama file disimpan dengan format: NPK.extension
        $namaFileBaru = $nama."_".$npk . "." . $ext;
        $targetFile   = "uploads/" . $namaFileBaru;

        // Upload file ke folder
        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $targetFile)) {
            throw new Exception("Gagal upload file!");
        }

        // Simpan ke database
        $sql = "INSERT INTO dosen (npk, nama,foto_extension) 
                VALUES (?, ?, ?)";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("sss", $npk, $nama,$ext);

        if (!$stmt->execute()) {
            throw new Exception("Error saat insert: " . $stmt->error);
        }

        return true; // kalau berhasil
    }
}
