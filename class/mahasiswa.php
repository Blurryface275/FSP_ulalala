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
    public function displayMahasiswa($limit, $offset)
    {
        $sql = "SELECT * FROM mahasiswa ORDER BY nama asc limit ? offset ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
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
        $targetFile   = "uploads/".$namaFileBaru;

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

    public function getTotalMahasiswa()
    {
        $sql = "SELECT COUNT(nrp) as total FROM mahasiswa";
        $result = $this->mysqli->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function isNRPExists($nrp){
        $sql = "SELECT nrp FROM mahasiswa WHERE nrp = ? LIMIT 1";
        $stmt = $this->mysqli->prepare($sql);

        $stmt->bind_param("s", $nrp); 
        

        if (!$stmt->execute()){
            $stmt->close();
            throw new Exception("Prepare statement gagal: " . $this->mysqli->error);
        }

        $stmt->store_result();
        $count = $stmt->num_rows; // ini buat cek jumlah baris yg direturn dari database
        $stmt->close();
        return $count > 0; // kalau misal count > 0 jadi ya nrp udah ada
    }
}
