<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    include 'koneksi.php';

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password'])) {
            
            // PENTING: Ganti Session ID lama dengan yang baru untuk keamanan
            session_regenerate_id(true);

            // PASTIKAN BARIS INI ADA DAN BENAR
            $_SESSION['id'] = $row['id'];
            $_SESSION['nama'] = $row['nama_lengkap']; 
            $_SESSION['level'] = $row['level'];      

            if ($row['level'] == "admin") {
                header("location:dashboard_admin.php");
            } else if ($row['level'] == "user") {
                header("location:dashboard_users.php");
            }
            exit(); 

        } else {
            header("location:login.php?pesan=gagal");
            exit();
        }
    } else {
        header("location:login.php?pesan=gagal");
        exit();
    }
    
    $stmt->close();
    $koneksi->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="shortcut icon" type="image/png" href="laundry.png"/>
    <title>Halaman Login</title>
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
                                        <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                                        
                                        <?php 
                                        if(isset($_GET['pesan'])){
                                            if($_GET['pesan'] == "gagal"){
                                                echo "<div class='alert alert-danger'>Login gagal! Username atau password salah.</div>";
                                            } else if($_GET['pesan'] == "logout"){
                                                echo "<div class='alert alert-success'>Anda telah berhasil logout.</div>";
                                            } else if($_GET['pesan'] == "belum_login"){
                                                echo "<div class='alert alert-danger'>Anda harus login untuk mengakses halaman.</div>";
                                            }
                                        }
                                        ?>
                                    </div>
                                    
                                    <form class="user" method="POST" action="login.php">
                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user" id="exampleInputEmail" placeholder="Masukkan Alamat Email..." name="email" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user"
                                                id="exampleInputPassword" placeholder="Password" name="password" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block">
                                            Login
                                        </button>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="forgot-password.php">Lupa Password?</a>
                                    </div>
                                    <div class="text-center">
                                        <a class="small" href="register.php">Buat Akun!</a>
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