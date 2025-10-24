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

// URL-tik pelikula ID-a hartu
$item_id = $_GET['item'];

// Pelikularen datuak kargatu
$sql = "SELECT * FROM pelikulak WHERE id = $item_id";
$emaitza = mysqli_query($conn, $sql);

echo "<style>body { background-color: #f3f3f3ff; padding: 20px; }</style>";
if ($emaitza->num_rows > 0) {
	$pelikula = mysqli_fetch_array($emaitza);
	
	// Datuak erakutsi
	echo '<h1>Pelikularen datuak</h1>';
	
	echo '<p>ID: ' . $pelikula['id'] . '</p>';
	echo '<p>Izena: ' . $pelikula['izena'] . '</p>';
	echo '<p>Deskribapena: ' . $pelikula['deskribapena'] . '</p>';
	echo '<p>Urtea: ' . $pelikula['urtea'] . '</p>';
	echo '<p>Egilea: ' . $pelikula['egilea'] . '</p>';
	echo '<p>Generoa: ' . $pelikula['generoa'] . '</p>';
	
	// Estekak
	echo '<p><a href="items.php">Atzera zerrendara</a></p>';
	
} else {
	echo 'Pelikula ez da existitzen';
}

mysqli_close($conn);
?>
