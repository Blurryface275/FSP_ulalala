<?php

require_once("parent.php");

class mahasiswa
{
    private $mysqli;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    // methods
    public function displayMahasiswa()
    {
        $sql = "SELECT * FROM mahasiswa";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function insertMahasiswaBaru($nrp, $nama, $gender, $tgl_lahir, $angkatan)
    {
        // Tangkap file foto
        $valid_extension = ['jpg', 'jpeg', 'png'];
        $ext  = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $valid_extension)) {
            die("Ekstensi file tidak valid! Hanya jpg/jpeg/png.");
        }

        // Nama file disimpan dengan format: NRP.extension
        $namaFileBaru = $nrp . "." . $ext;
        $targetFile   = "uploads/" . $namaFileBaru;

        // Pindahkan file ke folder uploads
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $targetFile)) {
            // Simpan data ke database
            $sql = "INSERT INTO mahasiswa (nrp, nama, gender, tanggal_lahir, angkatan, foto_extention) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param("ssssss", $nrp, $nama, $gender, $tgl_lahir, $angkatan, $ext);

            if ($stmt->execute()) {
                echo "<script>alert('Data berhasil disimpan!');</script>";
            }
        }

        return true; // kalau berhasil

    }
}
