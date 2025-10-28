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

// URL-tik pelikula ID-a hartu eta integer bihurtu (segurtasuna)
$item_id = isset($_GET['item']) ? intval($_GET['item']) : 0;

// Pelikularen datuak kargatu
$sql = "SELECT * FROM pelikulak WHERE id = $item_id";
$emaitza = mysqli_query($conn, $sql);

echo "<style>body { background-color: #f3f3f3; padding: 20px; font-family: sans-serif; }</style>";

if ($emaitza->num_rows > 0) {
    $pelikula = mysqli_fetch_array($emaitza);
    
    echo '<h1>Pelikularen datuak</h1>';
    echo '<p><b>ID:</b> ' . htmlspecialchars($pelikula['id']) . '</p>';
    echo '<p><b>Izena:</b> ' . htmlspecialchars($pelikula['izena']) . '</p>';
    echo '<p><b>Deskribapena:</b> ' . htmlspecialchars($pelikula['deskribapena']) . '</p>';
    echo '<p><b>Urtea:</b> ' . htmlspecialchars($pelikula['urtea']) . '</p>';
    echo '<p><b>Egilea:</b> ' . htmlspecialchars($pelikula['egilea']) . '</p>';
    echo '<p><b>Generoa:</b> ' . htmlspecialchars($pelikula['generoa']) . '</p>';
    
    echo '<button onclick="history.back()">Atzera</button>';
} else {
    echo '<p>Pelikula ez da existitzen</p>';
}

mysqli_close($conn);
?>
