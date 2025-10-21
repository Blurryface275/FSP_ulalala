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

    public function executeUpdateMahasiswa($postData, $fileData, $oldData)
    {
        $nrp_baru = $postData['nrp'];
        $nama_baru = $postData['nama'];
        $gender_baru = $postData['gender'];
        $tgl_lahir_baru = $postData['tgl'];
        $angkatan_baru = $postData['angkatan'];
        $nrp_lama = $postData['nrp_lama'];

        $ext_lama = $oldData['foto_extention'];

        // Validasi sederhana
        if ($nama_baru == "" || $nrp_baru == "") {
            return "Nama dan NRP harus diisi!";
        }

        if ($nrp_baru != $nrp_lama) {
            $cek = $this->mysqli->prepare("SELECT nrp FROM mahasiswa WHERE nrp = ?");
            $cek->bind_param("s", $nrp_baru);
            $cek->execute();
            $hasil = $cek->get_result();

            if ($hasil->num_rows > 0) {
                return "NRP sudah digunakan oleh mahasiswa lain!";
            }
        }

        // Cek apakah tidak ada perubahan
        if (
            $nrp_baru == $oldData['nrp'] &&
            $nama_baru == $oldData['nama'] &&
            $gender_baru == $oldData['gender'] &&
            $tgl_lahir_baru == $oldData['tanggal_lahir'] &&
            $angkatan_baru == $oldData['angkatan'] &&
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
            $target = "uploads/" . $nrp_baru . "." . $ext_baru;
            if (move_uploaded_file($fileData['foto']['tmp_name'], $target)) {
                // Hapus foto lama kalau NRP berubah
                $foto_lama = "uploads/" . $nrp_lama . "." . $ext_lama;
                if (file_exists($foto_lama) && $foto_lama != $target) {
                    unlink($foto_lama);
                }
            } else {
                return "Gagal mengupload foto!";
            }
        }
        $sql = "UPDATE mahasiswa 
            SET nrp=?, nama=?, gender=?, tanggal_lahir=?, angkatan=?, foto_extention=? 
            WHERE nrp=?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param(
            "ssssiss",
            $nrp_baru,
            $nama_baru,
            $gender_baru,
            $tgl_lahir_baru,
            $angkatan_baru,
            $ext_baru,
            $nrp_lama
        );

        if ($stmt->execute()) {
            return true;
        } else {
            return "Gagal update data mahasiswa!";
        }
    }

    //ambil data berdasarkan nrp
    public function fetchMahasiswa($nrp)
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM mahasiswa WHERE nrp = ?");
        $stmt->bind_param("s", $nrp);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $data;
    }

    public function getTotalMahasiswa()
    {
        $sql = "SELECT COUNT(nrp) as total FROM mahasiswa";
        $result = $this->mysqli->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function isNRPExists($nrpBaru, $nrpLama = null)
    {
        if ($nrpLama !== null) {
            $sql = "SELECT nrp FROM mahasiswa WHERE nrp = ? AND nrp != ? LIMIT 1";
            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param("ss", $nrpBaru, $nrpLama);
        } else {
            $sql = "SELECT nrp FROM mahasiswa WHERE nrp = ? LIMIT 1";
            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param("s", $nrpBaru);
        }
        if (!$stmt->execute()) {
            $stmt->close();
            throw new Exception("Prepare statement gagal: " . $this->mysqli->error);
        }
        $stmt->store_result();
        $count = $stmt->num_rows;
        $stmt->close();

        return $count > 0;
    }
}
