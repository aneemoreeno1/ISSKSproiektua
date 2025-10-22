<?php
// Datu-basearekin konexioa
$hostname = "db";
$username = "admin";
$password = "test";
$db = "database";

$conn = mysqli_connect($hostname, $username, $password, $db);
if ($conn->connect_error) {
	die("Database connection failed: " . $conn->connect_error);
}

// URL-tik erabiltzaile ID-a hartu
$user_id = $_GET['user'];

// Erabiltzailearen datuak kargatu
$sql = "SELECT * FROM usuarios WHERE id = $user_id";
$emaitza = mysqli_query($conn, $sql);

if ($emaitza->num_rows > 0) {
	$erabiltzailea = mysqli_fetch_array($emaitza);
	
	// Datuak erakutsi
	echo '<h1>Erabiltzailearen datuak</h1>';
	
	echo '<p>ID: ' . $erabiltzailea['id'] . '</p>';
	echo '<p>Izena: ' . $erabiltzailea['nombre'] . '</p>';
	echo '<p>NAN: ' . $erabiltzailea['nan'] . '</p>';
	echo '<p>Telefonoa: ' . $erabiltzailea['telefonoa'] . '</p>';
	echo '<p>Jaiotze data: ' . $erabiltzailea['jaiotze_data'] . '</p>';
	echo '<p>Email: ' . $erabiltzailea['email'] . '</p>';
	
	// Estekak
	echo '<p><a href="index.php">Atzera zerrendara</a></p>';
	
} else {
	echo 'Erabiltzailea ez da existitzen';
}
?>

