<?php
session_start();
include 'koneksi.php';

// Keamanan: Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['level']) || $_SESSION['level'] != "admin") {
    header("location: login.php?pesan=belum_login");
    exit();
}

// 1. Ambil data untuk kartu statistik
$query_baru = $koneksi->query("SELECT COUNT(id) as jumlah FROM transaksi WHERE status = 'Baru'");
$jumlah_baru = $query_baru->fetch_assoc()['jumlah'];

$query_proses = $koneksi->query("SELECT COUNT(id) as jumlah FROM transaksi WHERE status = 'Proses'");
$jumlah_proses = $query_proses->fetch_assoc()['jumlah'];

$query_selesai = $koneksi->query("SELECT COUNT(id) as jumlah FROM transaksi WHERE status = 'Selesai'");
$jumlah_selesai = $query_selesai->fetch_assoc()['jumlah'];

$query_pendapatan = $koneksi->query("SELECT SUM(total_harga) as total FROM transaksi WHERE DATE(tanggal_masuk) = CURDATE()");
$pendapatan_hari_ini = $query_pendapatan->fetch_assoc()['total'] ?? 0;

// 2. Ambil data untuk tabel transaksi terbaru
$sql_transaksi_terbaru = "SELECT * FROM transaksi ORDER BY tanggal_masuk DESC LIMIT 5";
$result_transaksi_terbaru = $koneksi->query($sql_transaksi_terbaru);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin Page - Dashboard</title>
    <link rel="shortcut icon" type="image/png" href="laundry.png"/>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center">
                <div class="sidebar-brand-icon">
                    <img src="laundry_02.png" alt="Laundry Mama Rey" style="width: 50px; height: 50px;">
                </div>
                <div class="sidebar-brand-text mx-1">Laundry Mama Rey</div>
            </a>
            <hr class="sidebar-divider my-0">

            <li class="nav-item active">
                <a class="nav-link" href="#">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <hr class="sidebar-divider">

            <li class="nav-item">
                <a class="nav-link" href="pengguna.php">
                    <i class="fas fa-fw fa-users-cog"></i>
                    <span>Pengguna</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="layanan.php">
                    <i class="fas fa-fw fa-tshirt"></i>
                    <span>Layanan</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="kelola_transaksi.php">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Manajemen Transaksi</span>
                </a>
            </li>
            <hr class="sidebar-divider d-none d-md-block">

            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- End of Sidebar -->

        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <div class="container-fluid">
                        <div class="d-sm-flex align-items-center justify-content-between mb-2">
                            <h1 class="h3 mb-0 text-gray-800">Selamat Datang, <?php echo $_SESSION['nama']; ?>!</h1>
                        </div>
                    </div>
                    
                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <div class="topbar-divider d-none d-sm-block"></div>
                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                                <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2"></i>
                                    Keluar Akun
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                    </div>
                    <!-- Content Row -->
                    <div class="row">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Cucian Baru (Belum Diproses)</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $jumlah_baru; ?> Transaksi</div>
                                        </div>
                                        <div class="col-auto"><i class="fas fa-inbox fa-2x text-gray-300"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Sedang Diproses</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $jumlah_proses; ?> Transaksi</div>
                                        </div>
                                        <div class="col-auto"><i class="fas fa-sync-alt fa-2x text-gray-300"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Siap Diambil</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $jumlah_selesai; ?> Transaksi</div>
                                        </div>
                                        <div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pendapatan Hari Ini</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?php echo number_format($pendapatan_hari_ini, 0, ',', '.'); ?></div>
                                        </div>
                                        <div class="col-auto"><i class="fas fa-dollar-sign fa-2x text-gray-300"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.container-fluid -->

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">5 Transaksi Terbaru</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Invoice</th>
                                            <th>Pelanggan</th>
                                            <th>Tgl Masuk</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($result_transaksi_terbaru->num_rows > 0): ?>
                                            <?php while($row = $result_transaksi_terbaru->fetch_assoc()): ?>
                                            <tr>
                                                <td><a href="detail_transaksi.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['kode_invoice']); ?></a></td>
                                                <td><?php echo htmlspecialchars($row['nama_pelanggan']); ?></td>
                                                <td><?php echo date('d M Y, H:i', strtotime($row['tanggal_masuk'])); ?></td>
                                                <td>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                                                <td><span class="badge badge-info"><?php echo htmlspecialchars($row['status']); ?></span></td>
                                            </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr><td colspan="5" class="text-center">Belum ada transaksi hari ini.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                </div>
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Projek MABD 2025</span>
                    </div>
                </div>
            </footer>
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