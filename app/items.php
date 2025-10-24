<?php

// items.php - Elementuen zerrenda

$hostname = "db";
$username = "admin";
$password = "test";
$db = "database";

$conn = mysqli_connect($hostname, $username, $password, $db);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Elementuak lortu
$query = mysqli_query($conn, "SELECT * FROM pelikulak")
    or die(mysqli_error($conn));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pelikulak</title>
</head>
<body>
<?php echo "<style>body { background-color: #f3f3f3ff; padding: 20px; } table { border-collapse: collapse; width: 40%; } th, td { padding: 12px; text-align: left; border: 1px solid #ddd; } }</style>"; ?>
    <h1>Pelikulak</h1>"
    
    <?php
    // Elementuak erakusten
    if (mysqli_num_rows($query) > 0) {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Izena</th><th> </th></tr>";
        
        while ($row = mysqli_fetch_array($query)) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['izena']}</td>";
            echo "<td>
                    <a href='show_item.php?item={$row['id']}'>Ikusi</a> | 
                    <a href='modify_item.php?item={$row['id']}'>Editatu</a> | 
                    <a href='delete_item.php?item={$row['id']}'>Ezabatu</a>
                  </td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Ez daude pelikularik.</p>";
    }
    
    mysqli_close($conn);
    ?>
    
    <p>
        <button onclick="window.location.href='add_item.php'">Pelikula Berria Gehitu</button>
        <button onclick="window.location.href='index.php'">Hasierara Bueltatu</button>
    </p>
</body>
</html>
