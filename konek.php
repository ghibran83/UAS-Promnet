<?php
// Settingan database
$host = "localhost";      
$username = "root";       
$password = "";           
$database = "simba"; 

// connect ke database
$koneksi = mysqli_connect($host, $username, $password, $database);

// Cek nyambung ga
if (!$koneksi) {
    // Kalo gagal, stop dulu dan kasih tau errornya
    die("gagal konek db: " . mysqli_connect_error());
}

?>