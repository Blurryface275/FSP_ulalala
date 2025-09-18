<?php
   $mysqli = new mysqli("localhost", "root", "", "fullstack");
   if ($mysqli->connect_errno) {
       echo "Failed to connect to MySQL: " . $mysqli->connect_error;
   }

    if (isset($_POST['login'])){
        $username = $_POST['username'];
        $password = $_POST['password'];
    }

    // pengecekan di sini, jadi kalo query ini mengembalikan hasil maka username sm password itu benar
    $sql="select username, password from akun where username=? and password=?"; 
    $stmt=$mysqli->prepare($sql); 
    $stmt->bind_param("ss",$username, $password); //masukin username + password ke parameter
    $stmt->execute();
    $result = $stmt->get_result();

    if($row = $result->fetch_assoc()){
        //Kalo username sm password coock
        echo "Login berhasil! Selamat datang...";
    }
    else{
        // Tidak cocok username sm passwordnya
        echo "Username atau password salah!";
    }

    header("Location: home.php");
    exit;
?>