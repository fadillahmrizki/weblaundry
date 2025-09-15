<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    $sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $token = bin2hex(random_bytes(50));
        $expires = date("Y-m-d H:i:s", time() + 3600); 

        $sql_update = "UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE email = ?";
        $stmt_update = $koneksi->prepare($sql_update);
        $stmt_update->bind_param("sss", $token, $expires, $email);
        $stmt_update->execute();

        $reset_link = "http://localhost/web_sdri/uas_mabd/reset_password.php?token=" . $token;

        echo "<h3>Link Reset Dibuat (Simulasi Email)</h3>";
        echo "<p>Silakan klik link di bawah ini untuk melanjutkan:</p>";
        echo "<a href='" . $reset_link . "'>" . $reset_link . "</a>";
        echo "<br><br><a href='forgot-password.php'>Kembali</a>";

    } else {
        header("location: forgot-password.php?pesan=emailtidakditemukan");
    }
}
?>