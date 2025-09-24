<?php

require_once("parent.php");

class mahasiswa {
    private $mysqli;

    public function __construct($mysqli) {
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

}


?>
