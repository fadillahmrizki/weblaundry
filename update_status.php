<?php
session_start();
include 'koneksi.php';

// Keamanan: Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['level']) || $_SESSION['level'] != "admin") {
    die("Akses ditolak.");
}

// Pastikan ada ID dan status yang dikirim
if (!isset($_GET['id']) || !isset($_GET['status'])) {
    header("location: kelola_transaksi.php");
    exit();
}

$transaksi_id = $_GET['id'];
$new_status = $_GET['status'];

// Jika status baru adalah 'Selesai', kita juga update tanggal_selesai
if ($new_status == 'Selesai') {
    $sql = "UPDATE transaksi SET status = ?, tanggal_selesai = NOW() WHERE id = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("si", $new_status, $transaksi_id);
} else {
    // Untuk status lain (misal: 'Proses' atau 'Diambil' nanti), kita hanya update statusnya
    $sql = "UPDATE transaksi SET status = ? WHERE id = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("si", $new_status, $transaksi_id);
}


if ($stmt->execute()) {
    // Jika berhasil, kembali ke halaman kelola transaksi
    header("location: kelola_transaksi.php?update=sukses");
} else {
    die("Gagal mengupdate status.");
}

$stmt->close();
$koneksi->close();
?>