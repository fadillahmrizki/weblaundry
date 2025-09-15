<?php
// Detail koneksi database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_websiteku";

// Membuat koneksi
$koneksi = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($koneksi->connect_error) {
    // Jika koneksi gagal, hentikan script dan tampilkan pesan error
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Jika berhasil, tidak akan ada output apa-apa.
// Koneksi siap digunakan di file lain.
?>