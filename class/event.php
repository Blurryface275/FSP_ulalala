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

    public function deleteEvent($event_id, $group_id)
    {
        $sql = "DELETE FROM event WHERE idevent = ? AND idgrup = ?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("ii", $event_id, $group_id);

        $success = $stmt->execute();
        $stmt->close();

        return $success; // Mengembalikan true jika berhasil, false jika gagal
    }

    // Di dalam class event
    public function updateEvent($judul, $tanggal, $keterangan, $jenis, $event_id)
    {
        $sql = "UPDATE event SET judul=?, tanggal=?, keterangan=?, jenis=? WHERE idevent=?";
        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param("ssssi", $judul, $tanggal, $keterangan, $jenis, $event_id);

        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function getEventById($event_id)
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM event WHERE idevent=?");
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getEventsByGroup($group_id)
    {
        $query = "SELECT judul, tanggal, idevent FROM event WHERE idgrup = ? ORDER BY tanggal DESC";
        $stmt = $this->mysqli->prepare($query);
        $stmt->bind_param("i", $group_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Mengembalikan semua data sebagai array asosiatif
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
