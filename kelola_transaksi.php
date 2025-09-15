<?php
session_start();
include 'koneksi.php';

// Keamanan: Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['level']) || $_SESSION['level'] != "admin") {
    header("location: login.php?pesan=belum_login");
    exit();
}

// Mengambil data transaksi untuk ditampilkan di tabel
$sql = "SELECT transaksi.*, users.nama_lengkap AS nama_petugas 
        FROM transaksi 
        LEFT JOIN users ON transaksi.petugas_id = users.id 
        ORDER BY tanggal_masuk DESC";
$result = $koneksi->query($sql);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" type="image/png" href="laundry.png"/>
    <title>Admin Page - Dashboard</title>
    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <!-- Custom styles for this template-->
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

            <!-- Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="dashboard_admin.php">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <hr class="sidebar-divider">

            <!-- Pengguna -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="pengguna.php" aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-users-cog"></i>
                    <span>Pengguna</span>
                </a>
            </li>

            <!-- Layanan -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="layanan.php" aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fa-tshirt"></i>
                    <span>Layanan</span>
                </a>
            </li>

            <!-- Transaksi -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="kelola_transaksi.php" aria-expanded="true">
                    <i class="fas fa-fw fa-folder-open"></i>
                    <span>Manajemen Transaksi</span>
                </a>
            </li>
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- End of Sidebar -->
        
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <h1 class="h3 mb-2 text-gray-800">Data Transaksi</h1>
                </nav>

                <div class="container-fluid">
                    <div class="card shadow mb-4">
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
                                            <th>Petugas</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['kode_invoice']); ?></td>
            <td><?php echo $row['nama_pelanggan']; ?></td>
            <td><?php echo date('d M Y, H:i', strtotime($row['tanggal_masuk'])); ?></td>
            <td>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
            <td>
                <?php
                // Beri warna berbeda untuk setiap status
                $status = htmlspecialchars($row['status']);
                $badge_class = 'badge-info'; // Default untuk 'Baru' & 'Proses'
                if ($status == 'Selesai') {
                    $badge_class = 'badge-success';
                } elseif ($status == 'Diambil') {
                    $badge_class = 'badge-secondary';
                }
                echo "<span class='badge $badge_class'>$status</span>";
                ?>
            </td>
            <td><?php echo htmlspecialchars($row['nama_petugas'] ?? 'N/A'); ?></td>
            <td>
                <a href="detail_transaksi.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm">Detail</a>
                
                <?php if ($row['status'] == 'Baru' || $row['status'] == 'Proses'): ?>
                    <a href="update_status.php?id=<?php echo $row['id']; ?>&status=Selesai" class="btn btn-success btn-sm" onclick="return confirm('Apakah Anda yakin pesanan ini sudah selesai?');">Selesaikan</a>
                <?php endif; ?>

                <a href="hapus_transaksi.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus transaksi ini?');">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="7" class="text-center">Belum ada data transaksi.</td></tr>
    <?php endif; ?>
</tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>
</html>