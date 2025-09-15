<?php
session_start();

echo "ID Sesi Anda saat ini adalah: <br>";
echo "<h3>" . session_id() . "</h3>";

echo '<br><a href="login.php">Ke Halaman Login</a>';
?>