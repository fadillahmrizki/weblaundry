<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Buat Akun</title>
    <link rel="shortcut icon" type="image/png" href="laundry.png"/>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>


<body class="bg-gradient-primary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-12"> <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Buat Akun Baru!</h1>
                                        <?php 
                                        if(isset($_GET['pesan'])){
                                            if($_GET['pesan'] == "gagal"){
                                                echo "<div class='alert alert-danger'>Pendaftaran gagal! Terjadi kesalahan.</div>";
                                            } else if($_GET['pesan'] == "passwordtidaksama"){
                                                echo "<div class='alert alert-warning'>Password dan konfirmasi password tidak sama!</div>";
                                            } else if($_GET['pesan'] == "emailexists"){
                                                echo "<div class='alert alert-danger'>Email sudah terdaftar, silakan gunakan email lain.</div>";
                                            }
                                        }
                                        ?>
                                    </div>
                                    <form class="user" method="POST" action="proses_register.php">
                                        <div class="form-group">
                                            <input type="text" class="form-control form-control-user" name="nama_lengkap" placeholder="Nama Lengkap" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user" name="email" placeholder="Alamat Email" required>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-sm-6 mb-3 mb-sm-0">
                                                <input type="password" class="form-control form-control-user" name="password" placeholder="Password" required>
                                            </div>
                                            <div class="col-sm-6">
                                                <input type="password" class="form-control form-control-user" name="konfirmasi_password" placeholder="Ulangi Password" required>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Daftarkan Akun
                                        </button>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="forgot-password.php">Lupa Password?</a>
                                    </div>
                                    <div class="text-center">
                                        <a class="small" href="login.php">Sudah punya akun? Login!</a>
                                    </div>
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
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>
</html>