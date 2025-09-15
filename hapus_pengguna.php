<?php
session_start();
include 'koneksi.php';

// Keamanan: Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['level']) || $_SESSION['level'] != "admin") {
    die("Akses ditolak. Anda bukan admin.");
}

// Pastikan ada ID yang dikirim
if (!isset($_GET['id'])) {
    header("location: kelola_pengguna.php");
    exit();
}

$user_id_to_delete = $_GET['id'];
$admin_id = $_SESSION['id'];

// Keamanan tambahan: Pastikan admin tidak menghapus dirinya sendiri
if ($user_id_to_delete == $admin_id) {
    die("Error: Anda tidak bisa menghapus akun Anda sendiri.");
}

// Siapkan dan jalankan perintah DELETE
$sql = "DELETE FROM users WHERE id = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $user_id_to_delete);

if ($stmt->execute()) {
    // Jika berhasil, kembali ke halaman kelola pengguna dengan pesan sukses
    header("location: kelola_pengguna.php?status=hapussukses");
} else {
    // Jika gagal
    die("Gagal menghapus pengguna.");
}

$stmt->close();
$koneksi->close();
?>