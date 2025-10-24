<?php
session_start(); 


    

$hostname = "db";
$username = "admin";
$password = "test";
$db = "database";

$conn = mysqli_connect($hostname, $username, $password, $db);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$query = mysqli_query($conn, "SELECT * FROM usuarios")
    or die(mysqli_error($conn));

echo"<style>body { background-color: #f3f3f3ff; padding: 20px; } table { border-collapse: collapse; width: 25%; } th, td { padding: 12px; text-align: left; border: 1px solid #ddd; } }</style>";
echo "
        
        <h1>Erabiltzaileak:</h1>";
echo "<table border='1px solid #ddd'>";
echo "<tr><th>ID</th><th>Izena</th><th> </th></tr>";
while ($row = mysqli_fetch_array($query)) {
    echo
        "<tr>
            <td>{$row['id']}</td>
            <td>{$row['nombre']}</td>
            <td>
                <a href='show_user.php?user={$row['id']}'>Ikusi</a>";
    
    // SOILIK saioa duen erabiltzaileak editatu ahal du bere datuak
    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['id']) {
        echo " | <a href='modify_user.php?user={$row['id']}'>Editatu</a>";  
    }
    
    echo "</td>
        </tr>";
}
echo "</table>";

// Estekak
echo '<p>';
echo '<button onclick="window.location.href=\'login.php\'">Saioa Hasi</button> ';
echo '<button onclick="window.location.href=\'register.php\'">Erregistratu</button>  |  ';
echo '<button onclick="window.location.href=\'items.php\'">Pelikulak Ikusi</button>';
echo '</p>';
?>
