<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['level']) || $_SESSION['level'] != "admin") {
    header("location: login.php?pesan=belum_login");
    exit();
}

$sql = "SELECT * FROM layanan ORDER BY id DESC";
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
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <h1 class="h3 mb-2 text-gray-800">Kelola Layanan Laundry</h1>
                </nav>

                <div class="container-fluid">
                    <p class="mb-4">Anda bisa menambah, mengedit, atau menghapus layanan yang anda miliki.</p>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tambahLayananModal">
                                + Layanan Baru
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Nama Layanan</th>
                                            <th>Harga per Kg/Pcs</th>
                                            <th>Estimasi</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($result->num_rows > 0): ?>
                                            <?php while($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($row['nama_layanan']); ?></td>
                                                <td>Rp <?php echo number_format($row['harga_per_kg'], 0, ',', '.'); ?></td>
                                                <td><?php echo htmlspecialchars($row['estimasi_hari']); ?> hari</td>
                                                <td>
                                                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editLayananModal" data-id="<?php echo $row['id']; ?>" data-nama="<?php echo htmlspecialchars($row['nama_layanan']); ?>" data-harga="<?php echo $row['harga_per_kg']; ?>" data-estimasi="<?php echo $row['estimasi_hari']; ?>">
                                                        Edit
                                                    </button>
                                                    <a href="hapus_layanan.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus layanan ini?');">Hapus</a>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
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

    <div class="modal fade" id="tambahLayananModal" tabindex="-1" role="dialog" aria-labelledby="tambahLayananModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahLayananModalLabel">Tambah Layanan Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="proses_layanan.php" method="POST">
                        <div class="form-group">
                            <label>Nama Layanan</label>
                            <input type="text" name="nama_layanan" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Harga per Kg/Pcs</label>
                            <input type="number" name="harga_per_kg" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Estimasi Hari</label>
                            <input type="number" name="estimasi_hari" class="form-control" required>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Layanan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editLayananModal" tabindex="-1" role="dialog" aria-labelledby="editLayananModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editLayananModalLabel">Edit Layanan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <form action="proses_layanan.php" method="POST">
                        <input type="hidden" name="id" id="edit-id">
                        <div class="form-group">
                            <label>Nama Layanan</label>
                            <input type="text" name="nama_layanan" id="edit-nama" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Harga per Kg</label>
                            <input type="number" name="harga_per_kg" id="edit-harga" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Estimasi Hari</label>
                            <input type="number" name="estimasi_hari" id="edit-estimasi" class="form-control" required>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script>
    $('#editLayananModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var nama = button.data('nama');
        var harga = button.data('harga');
        var estimasi = button.data('estimasi');
        var modal = $(this);
        modal.find('.modal-body #edit-id').val(id);
        modal.find('.modal-body #edit-nama').val(nama);
        modal.find('.modal-body #edit-harga').val(harga);
        modal.find('.modal-body #edit-estimasi').val(estimasi);
    });
    </script>
</body>
</html>