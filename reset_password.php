<?php
include 'koneksi.php';
$token_valid = false;
$error_message = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // PERBAIKAN: Gunakan waktu dari PHP, bukan dari MySQL (NOW())
    $now = date("Y-m-d H:i:s");

    $sql = "SELECT id FROM users WHERE reset_token = ? AND reset_token_expires > ?";
    $stmt = $koneksi->prepare($sql);
    // PERBAIKAN: Bind 2 parameter sekarang (token dan waktu saat ini)
    $stmt->bind_param("ss", $token, $now);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $token_valid = true;
    } else {
        $error_message = "Link reset tidak valid atau sudah kedaluwarsa.";
    }
} else {
    $error_message = "Token tidak ditemukan.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset Password Baru</title>
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-primary">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-5">
                    <div class="text-center">
                        <h1 class="h4 text-gray-900 mb-4">Buat Password Baru Anda</h1>
                    </div>
                    <?php if(isset($_GET['pesan']) && $_GET['pesan'] == 'tidaksama'): ?>
                        <div class="alert alert-danger">Password dan konfirmasi tidak sama!</div>
                    <?php endif; ?>

                    <?php if ($token_valid): ?>
                        <form method="POST" action="update_password.php">
                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                            <div class="form-group">
                                <input type="password" name="password" class="form-control form-control-user" placeholder="Password Baru" required>
                            </div>
                            <div class="form-group">
                                <input type="password" name="konfirmasi_password" class="form-control form-control-user" placeholder="Ulangi Password Baru" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-user btn-block">Update Password</button>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                        <div class="text-center">
                             <a class="small" href="forgot-password.php">Minta link baru</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>