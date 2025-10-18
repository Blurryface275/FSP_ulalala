<?php
require_once __DIR__ . "/data.php";

//nama class harus kecil
class classParent{
    protected $mysqli;

    public function __construct(){
        $this->mysqli = new mysqli(SERVER,UID, PWD, DB);
    }

    function __destruct()
    {
        $this->mysqli->close();
    } 
}
