<?php
session_start();
include 'koneksi.php';

// Keamanan: Pastikan hanya admin yang bisa menjalankan skrip ini
if (!isset($_SESSION['level']) || $_SESSION['level'] != "admin") {
    die("Akses ditolak. Anda bukan admin.");
}

// Ambil data dari form
$nama = $_POST['nama_layanan'];
$harga_per_kg = $_POST['harga_per_kg'];
$estimasi = $_POST['estimasi_hari'];

// Cek apakah ada ID yang dikirim (menandakan ini proses EDIT)
if (isset($_POST['id']) && !empty($_POST['id'])) {
    // Proses UPDATE (Edit)
    $id = $_POST['id'];
    $sql = "UPDATE layanan SET nama_layanan=?, harga_per_kg=?, estimasi_hari=? WHERE id=?";
    $stmt = $koneksi->prepare($sql);
    // 'siii' = string, integer, integer, integer
    $stmt->bind_param("siii", $nama, $harga_per_kg, $estimasi, $id);
} else {
    // Proses INSERT (Tambah Baru)
    $sql = "INSERT INTO layanan (nama_layanan, harga_per_kg, estimasi_hari) VALUES (?, ?, ?)";
    $stmt = $koneksi->prepare($sql);
    // 'sii' = string, integer, integer
    $stmt->bind_param("sii", $nama, $harga_per_kg, $estimasi);
}

// Jalankan query dan arahkan kembali
if ($stmt->execute()) {
    header("location: layanan.php?status=sukses");
} else {
    echo "Error: Gagal menyimpan data. " . $stmt->error;
}

$stmt->close();
$koneksi->close();
?>