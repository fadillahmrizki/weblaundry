<?php
session_start();
include 'koneksi.php';

// Keamanan: Pastikan hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['level']) || $_SESSION['level'] != "admin") {
    header("location: login.php?pesan=belum_login");
    exit();
}

// Ambil ID admin yang sedang login dari session
$admin_id = $_SESSION['id'];

// Ambil semua data pengguna dari database, KECUALI admin yang sedang login
$sql = "SELECT id, nama_lengkap, email, level FROM users WHERE id != ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin - Kelola Pengguna</title>
    <link rel="shortcut icon" type="image/png" href="laundry.png"/>
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
                    <i class="fas fa-fw fa-folder"></i>
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
                <!-- Navbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <h1 class="h3 mb-2 text-gray-800">Kelola Pengguna</h1>
                </nav>
                <!-- Main Content -->
                <div class="container-fluid">
                    <?php if(isset($_GET['status']) && $_GET['status'] == 'hapussukses'): ?>
                        <div class="alert alert-success">Pengguna telah berhasil dihapus.</div>
                    <?php endif; ?>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Daftar Pengguna Terdaftar</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Lengkap</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no = 1; if ($result->num_rows > 0): ?>
                                            <?php while($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $no++; ?></td>
                                                <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                                <td><?php echo htmlspecialchars($row['level']); ?></td>
                                                <td>
                                                    <a href="hapus_pengguna.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">Hapus</a>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr><td colspan="5" class="text-center">Tidak ada pengguna lain.</td></tr>
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
<?php $koneksi->close(); ?>