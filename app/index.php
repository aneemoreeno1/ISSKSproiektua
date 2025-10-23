<?php
echo '<h1>Yeah, it works!</h1>';

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

echo "<h2>Erabiltzaileak:</h2>";
echo "<table border='1'>";
echo "<tr><th>ID</th><th>Izena</th><th> </th></tr>";
while ($row = mysqli_fetch_array($query)) {
    echo "<tr>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['nombre']}</td>";
    echo "<td>
            <a href='show_user.php?user={$row['id']}'>Ikusi</a> | 
            <a href='modify_user.php?user={$row['id']}'>Editatu</a>
          </td>";
    echo "</tr>";
}
echo "</table>";

// Estekak
echo '<p>';
echo '<a href="login.php">Saioa Hasi</a> | ';
echo '<a href="register.php">Erregistratu</a> | ';
echo '<a href="items.php">Pelikulak Ikusi</a>';
echo '</p>';
?>
