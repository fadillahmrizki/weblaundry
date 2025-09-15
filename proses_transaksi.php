<?php
session_start();
include 'koneksi.php';

// Keamanan: Pastikan pengguna sudah login (Admin atau User)
if (!isset($_SESSION['id'])) {
    die("Akses ditolak. Silakan login terlebih dahulu.");
}

// Cek apakah form disubmit
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die("Akses ditolak.");
}

// Ambil data dari session, bukan dari form lagi
$nama_pelanggan = $_SESSION['nama']; 
$petugas_id = $_SESSION['id']; // Pengguna yang login dicatat sebagai petugas

// Ambil data lain dari form
$telepon_pelanggan = $_POST['telepon_pelanggan'] ?? ''; // Ambil telepon jika ada
$catatan = $_POST['catatan'];
$layanan_ids = $_POST['layanan_id'];
$kuantitas_arr = $_POST['kuantitas'];

$kode_invoice = "INV-" . date("Ymd") . "-" . strtoupper(substr(uniqid(), -4));

$koneksi->begin_transaction();

try {
    // 1. Simpan data utama ke tabel 'transaksi'
    $sql_transaksi = "INSERT INTO transaksi (kode_invoice, nama_pelanggan, telepon_pelanggan, petugas_id, catatan) VALUES (?, ?, ?, ?, ?)";
    $stmt_transaksi = $koneksi->prepare($sql_transaksi);
    $stmt_transaksi->bind_param("sssis", $kode_invoice, $nama_pelanggan, $telepon_pelanggan, $petugas_id, $catatan);
    $stmt_transaksi->execute();
    $transaksi_id = $koneksi->insert_id;

    // 2. Simpan setiap item layanan ke tabel 'detail_transaksi'
    $grand_total = 0;
    $sql_detail = "INSERT INTO detail_transaksi (transaksi_id, layanan_id, kuantitas, subtotal_harga) VALUES (?, ?, ?, ?)";
    $stmt_detail = $koneksi->prepare($sql_detail);

    $sql_get_harga = "SELECT harga_per_kg FROM layanan WHERE id = ?";
    $stmt_get_harga = $koneksi->prepare($sql_get_harga);

    foreach ($layanan_ids as $key => $layanan_id) {
        $kuantitas = $kuantitas_arr[$key];
        $stmt_get_harga->bind_param("i", $layanan_id);
        $stmt_get_harga->execute();
        $harga_result = $stmt_get_harga->get_result()->fetch_assoc();
        $harga_satuan = $harga_result['harga_per_kg'];
        
        $subtotal = $harga_satuan * $kuantitas;
        $grand_total += $subtotal;
        
        $stmt_detail->bind_param("iidi", $transaksi_id, $layanan_id, $kuantitas, $subtotal);
        $stmt_detail->execute();
    }

    // 3. Update total_harga di tabel 'transaksi'
    $sql_update_total = "UPDATE transaksi SET total_harga = ? WHERE id = ?";
    $stmt_update_total = $koneksi->prepare($sql_update_total);
    $stmt_update_total->bind_param("ii", $grand_total, $transaksi_id);
    $stmt_update_total->execute();

    $koneksi->commit();
    // Arahkan kembali ke dashboard yang sesuai
    if ($_SESSION['level'] == 'admin') {
        header("location: kelola_transaksi.php?status=sukses");
    } else {
        header("location: dashboard_users.php?status=pesanansukses");
    }

} catch (mysqli_sql_exception $exception) {
    $koneksi->rollback();
    die("Error: Gagal menyimpan transaksi. " . $exception->getMessage());
}
?>