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

// Formularioa bidali bada (ezabaketa baieztatu da)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['item_id'])) {
    $item_id = $_POST['item_id'];

    // DELETE kontsulta prestatu eta exekutatu
    $sql = "DELETE FROM pelikulak WHERE id = $item_id";
    mysqli_query($conn, $sql);
    
    // Items orrira bueltatu automatikoki
    header("Location: items.php");
    exit;
}

// GET bidez item ID-a jaso bada (ezabaketa orria erakusteko)
if (isset($_GET['item'])) {
    $item_id = $_GET['item'];
    
    // Pelikularen datuak kargatu    
    $sql = "SELECT * FROM pelikulak WHERE id = $item_id";
    $result = mysqli_query($conn, $sql);
    $pelikula = mysqli_fetch_assoc($result);
    
    if ($pelikula) {
        // Pelikularen informazioa erakutsi
        echo "<h3>Ezabatu pelikula:</h3>";
        echo "<p><strong>ID:</strong> " . $pelikula['id'] . "</p>";
        echo "<p><strong>Izena:</strong> " . $pelikula['izena'] . "</p>";
        echo "<p><strong>Urtea:</strong> " . $pelikula['urtea'] . "</p>";

        // Ezabaketa baieztatzeko formularioa
        echo "<form method='post'>";
        echo "<input type='hidden' name='item_id' value='" . $pelikula['id'] . "'>";
        echo "<button type='submit' id='item_delete_submit'>Ezabatu</button>";
        echo "</form>";
    } else {
        echo "<p>Ez da pelikularik aurkitu.</p>";
    }
}

echo "<br><a href='items.php'>Bueltatu</a>";

mysqli_close($conn);
?>
