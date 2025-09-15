<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $konfirmasi_password = $_POST['konfirmasi_password'];

    if ($password != $konfirmasi_password) {
        header("location: reset_password.php?token=" . $token . "&pesan=tidaksama");
        exit();
    }
    
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    $sql = "UPDATE users SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE reset_token = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("ss", $password_hashed, $token);
    
    if ($stmt->execute()) {
        header("location: login.php?pesan=resetsukses");
    } else {
        die("Gagal mengupdate password.");
    }
}
?>