<?php
session_start();
include 'koneksi.php';

// Keamanan: Pastikan hanya user yang bisa mengakses
if (!isset($_SESSION['level']) || $_SESSION['level'] != "user") {
    header("location: login.php?pesan=belum_login");
    exit();
}

// Ambil ID pengguna dari session
$user_id = $_SESSION['id'];

// 1. Ambil data untuk kartu statistik (HANYA UNTUK USER YANG LOGIN)
$query_proses = $koneksi->prepare("SELECT COUNT(id) as jumlah FROM transaksi WHERE petugas_id = ? AND (status = 'Baru' OR status = 'Proses')");
$query_proses->bind_param("i", $user_id);
$query_proses->execute();
$jumlah_proses = $query_proses->get_result()->fetch_assoc()['jumlah'];

$query_selesai = $koneksi->prepare("SELECT COUNT(id) as jumlah FROM transaksi WHERE petugas_id = ? AND (status = 'Selesai' OR status = 'Diambil')");
$query_selesai->bind_param("i", $user_id);
$query_selesai->execute();
$jumlah_selesai = $query_selesai->get_result()->fetch_assoc()['jumlah'];

$query_total_transaksi = $koneksi->prepare("SELECT COUNT(id) as jumlah FROM transaksi WHERE petugas_id = ?");
$query_total_transaksi->bind_param("i", $user_id);
$query_total_transaksi->execute();
$total_transaksi = $query_total_transaksi->get_result()->fetch_assoc()['jumlah'];

$query_pengeluaran = $koneksi->prepare("SELECT SUM(total_harga) as total FROM transaksi WHERE petugas_id = ?");
$query_pengeluaran->bind_param("i", $user_id);
$query_pengeluaran->execute();
$total_pengeluaran = $query_pengeluaran->get_result()->fetch_assoc()['total'] ?? 0;

// 2. Ambil data untuk tabel riwayat transaksi (HANYA UNTUK USER YANG LOGIN)
$sql_riwayat = $koneksi->prepare("SELECT * FROM transaksi WHERE petugas_id = ? ORDER BY tanggal_masuk DESC");
$sql_riwayat->bind_param("i", $user_id);
$sql_riwayat->execute();
$result_riwayat = $sql_riwayat->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>User Page - Dashboard</title>
    <link rel="shortcut icon" type="image/png" href="laundry.png"/>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>


<body id="page-top">
    <div id="wrapper">
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center">
                <div class="sidebar-brand-icon"><img src="laundry_02.png" alt="Laundry Mama Rey" style="width: 50px; height: 50px;"></div>
                <div class="sidebar-brand-text mx-1">Laundry Mama Rey</div>
            </a>
            <hr class="sidebar-divider my-0">
            <li class="nav-item active">
                <a class="nav-link" href="dashboard_users.php"><i class="fas fa-fw fa-tachometer-alt"></i><span>Dashboard</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="tambah_transaksi.php"><i class="fas fa-fw fa-plus-circle"></i><span>Buat Pesanan Baru</span></a>
            </li>
            <hr class="sidebar-divider d-none d-md-block">
            <div class="text-center d-none d-md-inline"><button class="rounded-circle border-0" id="sidebarToggle"></button></div>
        </ul>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <h1 class="h3 mb-2 text-gray-800">Selamat Datang, <?php echo htmlspecialchars($_SESSION['nama']); ?>!</h1>
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3"><i class="fa fa-bars"></i></button>
                    
                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo htmlspecialchars($_SESSION['nama']); ?></span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profil
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Log Aktivitas
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger" href="logout.php" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2"></i> Keluar Akun
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>

                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xl-3 col-md-6 mb-4"><div class="card border-left-info shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="text-xs font-weight-bold text-info text-uppercase mb-1">Pesanan Diproses</div><div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $jumlah_proses; ?> Pesanan</div></div><div class="col-auto"><i class="fas fa-sync-alt fa-2x text-gray-300"></i></div></div></div></div></div>
                        <div class="col-xl-3 col-md-6 mb-4"><div class="card border-left-success shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="text-xs font-weight-bold text-success text-uppercase mb-1">Pesanan Selesai</div><div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $jumlah_selesai; ?> Pesanan</div></div><div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300"></i></div></div></div></div></div>
                        <div class="col-xl-3 col-md-6 mb-4"><div class="card border-left-primary shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Transaksi</div><div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_transaksi; ?> Kali</div></div><div class="col-auto"><i class="fas fa-receipt fa-2x text-gray-300"></i></div></div></div></div></div>
                        <div class="col-xl-3 col-md-6 mb-4"><div class="card border-left-warning shadow h-100 py-2"><div class="card-body"><div class="row no-gutters align-items-center"><div class="col mr-2"><div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Pengeluaran</div><div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?php echo number_format($total_pengeluaran, 0, ',', '.'); ?></div></div><div class="col-auto"><i class="fas fa-dollar-sign fa-2x text-gray-300"></i></div></div></div></div></div>
                    </div>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Riwayat Pesanan Anda</h6></div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead><tr><th>Invoice</th><th>Tgl Masuk</th><th>Tgl Selesai</th><th>Total</th><th>Status</th></tr></thead>
                                    <tbody>
                                        <?php if ($result_riwayat->num_rows > 0): ?>
                                            <?php while($row = $result_riwayat->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($row['kode_invoice']); ?></td>
                                                <td><?php echo date('d M Y, H:i', strtotime($row['tanggal_masuk'])); ?></td>
                                                <td><?php echo $row['tanggal_selesai'] ? date('d M Y, H:i', strtotime($row['tanggal_selesai'])) : '-'; ?></td>
                                                <td>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                                                <td><span class="badge badge-info"><?php echo htmlspecialchars($row['status']); ?></span></td>
                                            </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr><td colspan="5" class="text-center">Anda belum memiliki riwayat pesanan.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            <footer class="sticky-footer bg-white"><div class="container my-auto"><div class="copyright text-center my-auto"><span>Copyright &copy; Laundry Mama Rey 2025</span></div></div></footer>
        </div>
    </div>
    <a class="scroll-to-top rounded" href="#page-top"><i class="fas fa-angle-up"></i></a>

    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-exclamation-triangle" viewBox="0 0 16 16" style="color: red;">
                            <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z"/>
                            <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                        </svg> Yakin Mau Keluar?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Klik "Keluar" jika memang ingin keluar.</div>
                <div class="modal-footer">
                    <button class="btn btn-info" type="button" data-dismiss="modal">Batal</button>
                    <a class="btn btn-danger" href="login.php">Keluar</a> </div>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>
</html>