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

echo "<style>body { background-color: #f3f3f3ff; padding: 20px; }</style>";

if ($emaitza->num_rows > 0) {
	$erabiltzailea = mysqli_fetch_array($emaitza);
	
	// Datuak erakutsi
	echo '<h1>Erabiltzailearen datuak</h1>';
	echo '<p><b>ID:</b> ' . $erabiltzailea['id'] . '</p>';
	echo '<p><b>Izena:</b> ' . $erabiltzailea['nombre'] . '</p>';
	echo '<p><b>NAN:</b> ' . $erabiltzailea['nan'] . '</p>';
	echo '<p><b>Telefonoa:</b> ' . $erabiltzailea['telefonoa'] . '</p>';
	echo '<p><b>Jaiotze data:</b> ' . $erabiltzailea['jaiotze_data'] . '</p>';
	echo '<p><b>Email:</b> ' . $erabiltzailea['email'] . '</p>';
	
	// Estekak
	echo '<button onclick="window.location.href=\'index.php\'">Atzera</button>';
	
} else {
	echo 'Erabiltzailea ez da existitzen';
}

mysqli_close($conn);
?>
