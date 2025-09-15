<?php
session_start();
include 'koneksi.php';

// Keamanan: Pastikan hanya admin yang bisa mengakses
if (!isset($_SESSION['level']) || $_SESSION['level'] != "user") {
    header("location: login.php?pesan=belum_login");
    exit();
}

// Ambil data layanan untuk dropdown
$layanan_sql = "SELECT * FROM layanan";
$layanan_result = $koneksi->query($layanan_sql);
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
    <title>User Page - Dashboard</title>
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
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard_users.php">
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
        <!-- End of Sidebar -->
        
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <h6 class="m-0 font-weight-bold text-primary">Buat Pesanan Laundry Baru untuk : <?php echo htmlspecialchars($_SESSION['nama']); ?></h6>
                </nav>

                <div class="container-fluid">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Transaksi Baru</h6>
                        </div>
                        <div class="card-body">
                            <form action="proses_transaksi.php" method="POST">
                                <div class="form-group row">
                                    <div class="col-sm-6 mb-3 mb-sm-0">
                                        <label>Nama Pelanggan</label>
                                        <input type="text" name="nama_pelanggan" class="form-control" required>
                                    </div>
                                    <div class="col-sm-6">
                                        <label>Telepon Pelanggan</label>
                                        <input type="text" name="telepon_pelanggan" class="form-control">
                                    </div>
                                </div>
                                <hr>
                                
                                <h4>Detail Layanan</h4>
                                <div id="layanan-wrapper">
                                    <div class="form-group row layanan-item">
                                        <div class="col-sm-5">
                                            <select name="layanan_id[]" class="form-control layanan-select" required>
                                                <option value="">-- Pilih Layanan --</option>
                                                <?php while($row = $layanan_result->fetch_assoc()){
                                                    echo "<option value='{$row['id']}' data-harga='{$row['harga_per_kg']}'>{$row['nama_layanan']} (Rp ".number_format($row['harga_per_kg']).")</option>";
                                                }?>
                                            </select>
                                        </div>
                                        <div class="col-sm-3">
                                            <input type="number" name="kuantitas[]" class="form-control kuantitas-input" placeholder="Kuantitas (Kg)" step="0.1" required>
                                        </div>
                                        <div class="col-sm-4">
                                             <input type="text" class="form-control subtotal" placeholder="Subtotal" readonly>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" id="tambah-layanan-btn" class="btn btn-info btn-sm mb-3">Tambah Layanan Lain</button>
                                <hr>

                                <div class="form-group">
                                    <label>Catatan (Opsional)</label>
                                    <textarea name="catatan" class="form-control"></textarea>
                                </div>
                                
                                <div class="text-right">
                                    <h3>Total Harga: <span id="total-harga">Rp 0</span></h3>
                                </div>

                                <button type="submit" class="btn btn-primary btn-user btn-block mt-4">Simpan Transaksi</button>
                                <a href="kelola_transaksi.php" class="btn btn-secondary btn-user btn-block">Batal</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
    $(document).ready(function() {
        function calculateTotal() {
            var total = 0;
            $('.layanan-item').each(function() {
                var harga = parseFloat($(this).find('.layanan-select option:selected').data('harga')) || 0;
                var kuantitas = parseFloat($(this).find('.kuantitas-input').val()) || 0;
                var subtotal = harga * kuantitas;
                $(this).find('.subtotal').val('Rp ' + subtotal.toLocaleString('id-ID'));
                total += subtotal;
            });
            $('#total-harga').text('Rp ' + total.toLocaleString('id-ID'));
        }

        $(document).on('change keyup', '.layanan-select, .kuantitas-input', function() {
            calculateTotal();
        });

        $("#tambah-layanan-btn").click(function() {
            var layananRow = $(".layanan-item").first().clone();
            layananRow.find('select, input').val('');
            layananRow.find('.subtotal').val('Subtotal');
            $("#layanan-wrapper").append(layananRow);
            calculateTotal();
        });
    });
    </script>
</body>
</html>