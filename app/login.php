<?php
// Datu-basearekin konexioa
$hostname = "db";
$username = "admin";
$password = "test";
$db = "database";

$conn = mysqli_connect($hostname,$username,$password,$db);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Saioa Hasi</title>
</head>
<body>
    <h1>Saioa Hasi</h1>
    
    <!-- Saio hasierako formularioa -->
    <form id="login_form" method="POST">
        Erabiltzailea: <input type="text" name="erabiltzailea" required><br><br>
        Pasahitza: <input type="password" name="pasahitza" required><br><br>
        
        <!-- Sartzeko botoia -->
        <input type="submit" id="login_submit" value="Sartu">
    </form>

    <p><a href="/">Atzera hasierako orrira</a></p>
</body>
</html>
