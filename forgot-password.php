<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Lupa Password</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-primary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-6 col-lg-7 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-2">Lupa Password Anda?</h1>
                                        <p class="mb-4">Masukkan alamat email Anda, dan kami akan membuatkan link untuk mereset password Anda.</p>
                                    </div>
                                    <?php 
                                    if(isset($_GET['pesan']) && $_GET['pesan'] == "emailtidakditemukan"){
                                        echo "<div class='alert alert-danger'>Email tidak terdaftar.</div>";
                                    }
                                    ?>
                                    <form class="user" method="POST" action="proses_lupa_password.php">
                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user" name="email" placeholder="Masukkan Alamat Email..." required>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Kirim Link Reset
                                        </button>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="login.php">Kembali ke Login</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>