<?php
require_once("parent.php");

class Event
{
    private $mysqli;

    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    // Fungsi Pembantu: Membuat slug dari judul (untuk judul_slug)
    private function createSlug($text)
    {
        // Ganti semua karakter non-alfanumerik/spasi menjadi strip (-)
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        // Transliterasi (mengganti karakter internasional)
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // Hapus karakter yang tersisa yang bukan huruf, angka, atau strip
        $text = preg_replace('~[^-\w]+~', '', $text);
        // Trim strip di awal dan akhir
        $text = trim($text, '-');
        // Ubah ke huruf kecil
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    public function displayEvent($limit, $offset)
    {
        $sql = "SELECT * FROM event ORDER BY judul ASC LIMIT ? OFFSET ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result();
    }


    // Insert event baru
    // Insert event baru tanpa slug disimpan ke DB
    public function insertEventBaru($idgrup, $judul, $tanggal, $keterangan, $jenis, $poster_extension)
    {
        // Slug tetap dibuat
        $judul_slug = $this->createSlug($judul);

        // Hapus judul_slug dari query INSERT
        $sql = "INSERT INTO event (idgrup, judul, tanggal, keterangan, jenis, poster_extension) 
            VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("isssss", $idgrup, $judul, $tanggal, $keterangan, $jenis, $poster_extension);

        if (!$stmt->execute()) {
            throw new Exception("Error saat insert event: " . $stmt->error);
        }

        return [
            'idevent' => $this->mysqli->insert_id,
            'judul_slug' => $judul_slug   // tetap dikembalikan meski tidak disimpan di DB
        ];
    }


    // Hitung total event
    public function getTotalEvent()
    {
        $sql = "SELECT COUNT(idevent) as total FROM event";
        $result = $this->mysqli->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }
}
