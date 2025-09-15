<?php
session_start();
include 'koneksi.php';

// Keamanan: Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['level']) || $_SESSION['level'] != "admin") {
    die("Akses ditolak. Anda bukan admin.");
}

// Pastikan ada ID yang dikirim melalui URL
if (!isset($_GET['id'])) {
    header("location: layanan.php");
    exit();
}

$id = $_GET['id'];
$sql = "DELETE FROM layanan WHERE id = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    // Jika berhasil, kembali ke halaman kelola layanan
    header("location: layanan.php?status=hapussukses");
} else {
    echo "Error: Gagal menghapus layanan. " . $stmt->error;
}

$stmt->close();
$koneksi->close();
?>