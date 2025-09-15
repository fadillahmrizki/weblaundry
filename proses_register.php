<?php
// Hubungkan ke database
include 'koneksi.php';

// Ambil data dari form sesuai dengan input yang ada
$nama_lengkap = $_POST['nama_lengkap']; // <-- INI BARIS KUNCINYA, mengambil langsung 'nama_lengkap'
$email = $_POST['email'];
$password = $_POST['password'];
$konfirmasi_password = $_POST['konfirmasi_password'];

// Validasi apakah password dan konfirmasi password sama
if ($password != $konfirmasi_password) {
    header("location:register.php?pesan=passwordtidaksama");
    exit();
}

// Cek apakah email sudah ada di database
$check_email_sql = "SELECT email FROM users WHERE email = ?";
$stmt_check = $koneksi->prepare($check_email_sql);
$stmt_check->bind_param("s", $email);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    header("location:register.php?pesan=emailexists");
    exit();
}
$stmt_check->close();

// HASH PASSWORD SEBELUM DISIMPAN
$password_hashed = password_hash($password, PASSWORD_DEFAULT);

// Siapkan query untuk menyimpan data user baru
$level = 'user'; 
$sql = "INSERT INTO users (nama_lengkap, email, password, level) VALUES (?, ?, ?, ?)";
$stmt = $koneksi->prepare($sql);

// Bind parameter ke query
$stmt->bind_param("ssss", $nama_lengkap, $email, $password_hashed, $level);

// Eksekusi query
if ($stmt->execute()) {
    // Jika pendaftaran berhasil, arahkan ke halaman login dengan pesan sukses
    header("location:login.php?pesan=daftarsukses");
} else {
    // Jika pendaftaran gagal karena alasan lain
    header("location:register.php?pesan=gagal");
}

$stmt->close();
$koneksi->close();
?>