<?php
// 1. Memulai sesi
session_start();

// 2. Menghubungkan ke database
include 'koneksi.php';

// 3. Mengambil data dari form login
$username = $_POST['username'];
$password = $_POST['password'];

// 4. Membuat query untuk mencari user berdasarkan username
// PENTING: Gunakan prepared statements untuk keamanan dari SQL Injection
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();


// 5. Mengecek apakah user ditemukan
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // 6. Memverifikasi password (untuk sekarang kita bandingkan langsung)
    // Di aplikasi nyata, gunakan password_verify()
    if ($password == $row['password']) {
        
        // Login berhasil, simpan informasi user ke session
        $_SESSION['username'] = $row['username'];
        $_SESSION['level'] = $row['level'];

        // 7. Arahkan berdasarkan level
        if ($row['level'] == "admin") {
            header("location:dashboard_admin.php");
        } else if ($row['level'] == "user") {
            header("location:dashboard_users.php");
        } else {
            // Jika level tidak dikenali
            header("location:login.html?pesan=gagal");
        }
    } else {
        // Jika password salah
        header("location:login.html?pesan=gagal");
    }
} else {
    // Jika username tidak ditemukan
    header("location:login.html?pesan=gagal");
}

$stmt->close();
$koneksi->close();
?>