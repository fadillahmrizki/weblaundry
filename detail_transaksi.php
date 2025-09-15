<?php
session_start();
include 'koneksi.php';

// Keamanan & ambil data awal
if (!isset($_SESSION['level']) || $_SESSION['level'] != "admin") {
    header("location: login.php?pesan=belum_login");
    exit();
}
if(!isset($_GET['id'])){
    header("location: kelola_transaksi.php");
    exit();
}
$transaksi_id = $_GET['id'];

// Proses UPDATE STATUS jika ada data yang dikirim dari form
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $new_status = $_POST['status'];
    
    $sql_update = "UPDATE transaksi SET status = ? WHERE id = ?";
    $stmt_update = $koneksi->prepare($sql_update);
    $stmt_update->bind_param("si", $new_status, $transaksi_id);
    $stmt_update->execute();
    
    // Jika status diubah menjadi 'Selesai', catat tanggal selesainya
    if ($new_status == 'Selesai') {
        $sql_tgl = "UPDATE transaksi SET tanggal_selesai = NOW() WHERE id = ?";
        $koneksi->query($sql_tgl);
    }
    
    header("Location: detail_transaksi.php?id=" . $transaksi_id . "&status_updated=true");
    exit();
}

// Ambil data transaksi utama (setelah kemungkinan di-update)
$sql_transaksi = "SELECT transaksi.*, users.nama_lengkap AS nama_petugas FROM transaksi LEFT JOIN users ON transaksi.petugas_id = users.id WHERE transaksi.id = ?";
$stmt_transaksi = $koneksi->prepare($sql_transaksi);
$stmt_transaksi->bind_param("i", $transaksi_id);
$stmt_transaksi->execute();
$transaksi = $stmt_transaksi->get_result()->fetch_assoc();

if(!$transaksi){
    header("location: kelola_transaksi.php");
    exit();
}

// Ambil rincian layanan untuk transaksi ini
$sql_detail = "SELECT detail_transaksi.*, layanan.nama_layanan, layanan.harga_per_kg FROM detail_transaksi JOIN layanan ON detail_transaksi.layanan_id = layanan.id WHERE detail_transaksi.transaksi_id = ?";
$stmt_detail = $koneksi->prepare($sql_detail);
$stmt_detail->bind_param("i", $transaksi_id);
$stmt_detail->execute();
$detail_result = $stmt_detail->get_result();

// Cek apakah ini mode cetak
$is_print_mode = isset($_GET['cetak']);
?>

<?php if ($is_print_mode): ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Nota Transaksi - <?php echo htmlspecialchars($transaksi['kode_invoice']); ?></title>
    <link rel="shortcut icon" type="image/png" href="laundry.png"/>
    <style>
        body { font-family: 'Courier New', Courier, monospace; margin: 0; color: #000; background-color: #fff; }
        .receipt-container { width: 302px; /* Lebar umum kertas struk thermal */ margin: auto; padding: 20px; }
        .header { text-align: center; }
        .header h2 { margin: 0; }
        .header p { margin: 2px 0; font-size: 12px; }
        .separator { border-top: 1px dashed #000; margin: 10px 0; }
        .info p { margin: 2px 0; font-size: 12px; }
        .items-table { width: 100%; border-collapse: collapse; font-size: 12px; margin-top: 10px; }
        .items-table td { padding: 3px 0; vertical-align: top;}
        .items-table .price { text-align: right; }
        .total-section { margin-top: 10px; font-size: 12px; }
        .footer { text-align: center; margin-top: 20px; font-size: 11px; }
    </style>
</head>
<body onload="window.print(); setTimeout(window.close, 0);"> <div class="receipt-container">
        <div class="header">
            <h2>LAUNDRY MAMA REY</h2>
            <p>Jl. Tengku Amir Hamzah, Binjai</p>
            <p>Telp: 0812-3456-7890</p>
        </div>
        <div class="separator"></div>
        <div class="info">
            <p>No. Invoice: <?php echo htmlspecialchars($transaksi['kode_invoice']); ?></p>
            <p>Tanggal: <?php echo date('d/m/Y H:i', strtotime($transaksi['tanggal_masuk'])); ?></p>
            <p>Pelanggan: <?php echo htmlspecialchars($transaksi['nama_pelanggan']); ?></p>
            <p>Kasir: <?php echo htmlspecialchars($transaksi['nama_petugas'] ?? 'N/A'); ?></p>
        </div>
        <div class="separator"></div>
        <table class="items-table">
            <tbody>
                <?php mysqli_data_seek($detail_result, 0); while($detail = $detail_result->fetch_assoc()): ?>
                <tr>
                    <td colspan="4"><?php echo htmlspecialchars($detail['nama_layanan']); ?></td>
                </tr>
                <tr>
                    <td>&nbsp;&nbsp;<?php echo htmlspecialchars($detail['kuantitas']); ?> Kg</td>
                    <td class="price">@ <?php echo number_format($detail['harga_per_kg']); ?></td>
                    <td></td>
                    <td class="price"><?php echo number_format($detail['subtotal_harga']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div class="separator"></div>
        <table class="total-section">
            <tr style="font-weight:bold;">
                <td>TOTAL</td>
                <td class="price">Rp <?php echo number_format($transaksi['total_harga']); ?></td>
            </tr>
        </table>
        <div class="separator"></div>
        <div class="footer">
            <p>Terima kasih atas kepercayaan Anda!</p>
            <p>Barang yang tidak diambil dalam 30 hari<br>bukan tanggung jawab kami.</p>
        </div>
    </div>
</body>
</html>

<!-- Kode Web -->

<?php else: ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" type="image/png" href="laundry.png"/>
    <title>Detail Transaksi - <?php echo htmlspecialchars($transaksi['kode_invoice']); ?></title>
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
            <!-- Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center">
                <div class="sidebar-brand-icon">
                    <img src="laundry_02.png" alt="Laundry Mama Rey" style="width: 100%; height: 100%;">
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
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo htmlspecialchars($_SESSION['nama']); ?></span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                        </li>
                    </ul>
                </nav>

                <div class="container-fluid">
                    <h1 class="h3 mb-4 text-gray-800">Detail Transaksi: <?php echo htmlspecialchars($transaksi['kode_invoice']); ?></h1>
                    <?php if(isset($_GET['status_updated'])): ?>
                        <div class="alert alert-success">Status berhasil diperbarui!</div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-lg-7">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Rincian Layanan</h6></div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <thead><tr><th>Layanan</th><th>Harga Satuan</th><th>Kuantitas (Kg)</th><th>Subtotal</th></tr></thead>
                                        <tbody>
                                            <?php mysqli_data_seek($detail_result, 0); while($detail = $detail_result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($detail['nama_layanan']); ?></td>
                                                <td>Rp <?php echo number_format($detail['harga_per_kg']); ?></td>
                                                <td><?php echo htmlspecialchars($detail['kuantitas']); ?></td>
                                                <td>Rp <?php echo number_format($detail['subtotal_harga']); ?></td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr class="font-weight-bold table-active"><td colspan="3" class="text-right">Total Harga</td><td>Rp <?php echo number_format($transaksi['total_harga']); ?></td></tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Informasi & Aksi</h6></div>
                                <div class="card-body">
                                    <p><strong>Nama Pelanggan:</strong><br> <?php echo htmlspecialchars($transaksi['nama_pelanggan']); ?></p>
                                    <p><strong>Telepon:</strong><br> <?php echo htmlspecialchars($transaksi['telepon_pelanggan'] ?? '-'); ?></p>
                                    <p><strong>Tanggal Masuk:</strong><br> <?php echo date('d F Y, H:i', strtotime($transaksi['tanggal_masuk'])); ?></p>
                                    <p><strong>Tanggal Selesai:</strong><br> <?php echo $transaksi['tanggal_selesai'] ? date('d F Y, H:i', strtotime($transaksi['tanggal_selesai'])) : '-'; ?></p>
                                    <p><strong>Dicatat oleh:</strong><br> <?php echo htmlspecialchars($transaksi['nama_petugas'] ?? 'N/A'); ?></p>
                                    <?php if(!empty($transaksi['catatan'])): ?>
                                        <p><strong>Catatan:</strong><br> <?php echo htmlspecialchars($transaksi['catatan']); ?></p>
                                    <?php endif; ?>
                                    <hr>
                                    
                                    <form action="detail_transaksi.php?id=<?php echo $transaksi_id; ?>" method="POST">
                                        <div class="form-group">
                                            <label for="status"><strong>Update Status Cucian:</strong></label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="Baru" <?php if($transaksi['status'] == 'Baru') echo 'selected'; ?>>Baru</option>
                                                <option value="Proses" <?php if($transaksi['status'] == 'Proses') echo 'selected'; ?>>Proses</option>
                                                <option value="Selesai" <?php if($transaksi['status'] == 'Selesai') echo 'selected'; ?>>Selesai</option>
                                                <option value="Diambil" <?php if($transaksi['status'] == 'Diambil') echo 'selected'; ?>>Diambil</option>
                                            </select>
                                        </div>
                                        <button type="submit" name="update_status" class="btn btn-success">Update Status</button>
                                    </form>
                                    <hr>
                                    
                                    <a href="kelola_transaksi.php" class="btn btn-secondary">Kembali</a>
                                    <a href="detail_transaksi.php?id=<?php echo $transaksi_id; ?>&cetak=true" target="_blank" class="btn btn-primary"><i class="fa fa-print"></i> Cetak Nota</a>
                                </div>
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
<?php endif; ?>