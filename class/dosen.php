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
    public function displayDosen($limit, $offset)
    {
        $sql = "SELECT * FROM dosen  ORDER BY nama asc limit ? offset ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
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

    public function executeUpdateDosen($postData, $fileData, $oldData)
    {
        $npk_baru = $postData['nrp'];
        $nama_baru = $postData['nama'];
        $npk_lama = $postData['nrp_lama'];

        $ext_lama = $oldData['foto_extention'];

        // Validasi sederhana
        if ($nama_baru == "" || $npk_baru == "") {
            return "Nama dan NPK harus diisi!";
        }

        if ($npk_baru != $npk_lama) {
            $cek = $this->mysqli->prepare("SELECT npk FROM mahasiswa WHERE npk = ?");
            $cek->bind_param("s", $npk_baru);
            $cek->execute();
            $hasil = $cek->get_result();

            if ($hasil->num_rows > 0) {
                return "NPK sudah digunakan oleh mahasiswa lain!";
            }
        }

        // Cek apakah tidak ada perubahan
        if (
            $npk_baru == $oldData['npk'] &&
            $nama_baru == $oldData['nama'] &&
            empty($fileData['foto']['name'])
        ) {
            return "Tidak ada perubahan data.";
        }

        // upload foto baru 
        $ext_baru = $ext_lama;
        if (!empty($fileData['foto']['name'])) {
            $ext = strtolower(pathinfo($fileData['foto']['name'], PATHINFO_EXTENSION));
            if ($ext != "jpg" && $ext != "jpeg" && $ext != "png") {
                return "Format foto harus JPG atau PNG!";
            }

            $ext_baru = $ext;
            $target = "uploads/" . $npk_baru . "." . $ext_baru;
            if (move_uploaded_file($fileData['foto']['tmp_name'], $target)) {
                // Hapus foto lama kalau NRP berubah
                $foto_lama = "uploads/" . $npk_lama . "." . $ext_lama;
                if (file_exists($foto_lama) && $foto_lama != $target) {
                    unlink($foto_lama);
                }
            } else {
                return "Gagal mengupload foto!";
            }
        }
        $sql = "UPDATE dosen 
            SET npk=?, nama=?, foto_extention=? 
            WHERE npk=?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param(
            "ssss",
            $npk_baru,
            $nama_baru,
            $ext_baru,
            $npk_lama
        );

        if ($stmt->execute()) {
            return true;
        } else {
            return "Gagal update data mahasiswa!";
        }
    }

        //ambil data berdasarkan npk
    public function fetchDosen($npk)
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM dosen WHERE npk = ?");
        $stmt->bind_param("s", $npk);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $data;
    }

    public function getTotalDosen()
    {
        $sql = "SELECT COUNT(npk) as total FROM dosen";
        $result = $this->mysqli->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}
