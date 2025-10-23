<?php
echo '<h1>Yeah, it works!</h1>';
// phpinfo();

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

echo '<h2>Erabiltzaileak:</h2>';
while ($row = mysqli_fetch_array($query)) {
	echo
		"<tr>
			<td>{$row['id']}</td>
			<td>{$row['nombre']}</td>
			<td>
				<a href='show_user.php?user={$row['id']}'>Ikusi</a> | 
				<a href='modify_user.php?user={$row['id']}'>Editatu</a>
			</td>
		</tr>";
}

// Estekak
echo '<p><a href="login.php">SaioaHasi</a> | <a href="register.php">Erregistratu</a></p> | <a href="items.php">Pelikulak ikusi</a></p>';
?>

