<?php
session_start();
include 'koneksi.php';

// Keamanan: Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['level']) || $_SESSION['level'] != "admin") {
    die("Akses ditolak.");
}

// Pastikan ada ID yang dikirim
if (!isset($_GET['id'])) {
    header("location: kelola_transaksi.php");
    exit();
}

$transaksi_id_to_delete = $_GET['id'];

// Siapkan dan jalankan perintah DELETE
$sql = "DELETE FROM transaksi WHERE id = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $transaksi_id_to_delete);

if ($stmt->execute()) {
    // Jika berhasil, kembali ke halaman kelola transaksi dengan pesan sukses
    header("location: kelola_transaksi.php?status=hapussukses");
} else {
    die("Gagal menghapus transaksi.");
}

$stmt->close();
$koneksi->close();
?>