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
$query = mysqli_query($conn, "SELECT * FROM pelikulak") or die(mysqli_error($conn));
?>

<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <title>Pelikulak</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Pelikulak</h1>
    
    <?php
    // Elementuak erakusten
    if (mysqli_num_rows($query) > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Izena</th><th>Ekintzak</th></tr>";
        
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
        <button id="item_add_submit" type="button" onclick="window.location.href='add_item.php'">Pelikula Berria Gehitu</button>
        <button id="home" type="button" onclick="window.location.href='index.php'">Hasierara Bueltatu</button>
    </p>
</body>
</html>
