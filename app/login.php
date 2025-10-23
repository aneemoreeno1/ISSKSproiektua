<?php
// PHP Kodea

session_start();

// Datu-basearekin konexioa
$hostname = "db";
$username = "admin";
$password = "test";
$db = "database";

$conn = mysqli_connect($hostname, $username, $password, $db);
if ($conn->connect_error) {
	die("Database connection failed: " . $conn->connect_error);
}

// Formularioa bidali bada
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$erabiltzailea = $_POST['erabiltzailea'];
	$pasahitza = $_POST['pasahitza'];
	
	// Erabiltzailea eta pasahitza egiaztatu
	$sql = "SELECT * FROM usuarios WHERE nombre = '$erabiltzailea' AND pasahitza = '$pasahitza'";
	$emaitza = mysqli_query($conn, $sql);
	
	if (mysqli_num_rows($emaitza) > 0) {
		// Datuak lortu
		$user = mysqli_fetch_assoc($emaitza);
		//Saioa hasi
	        $_SESSION['user_id'] = $user['id'];
        	$_SESSION['username'] = $user['nombre'];

		echo "Ongi etorri " . $erabiltzailea . "!";
		echo "<p><a href='modify_user.php?user={$user['id']}'>Editatu</a></p>";
		
	} else {
		echo "Erabiltzailea edo pasahitza okerrak";
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Sartu</title>

	<script>
	function datuakEgiaztatu() {
		var erabiltzailea = document.login_form.erabiltzailea.value;
		var pasahitza = document.login_form.pasahitza.value;
		
		// Erabiltzailea hutsik dagoen egiaztatu
		if (erabiltzailea.length < 1) {
			window.alert("Sartu erabiltzaile izena");
			return false;
		}
		
		// Pasahitza hutsik dagoen egiaztatu
		if (pasahitza.length < 1) {
			window.alert("Sartu pasahitza");
			return false;
		}
		
		return true;
	}
	</script>
</head>
<body>
	<!-- HTML Kodea -->
	
	<h1>Sartu</h1>
	
	<!-- Login formularioa -->
	<form id="login_form" name="login_form" method="POST" onsubmit="return datuakEgiaztatu()">
		Erabiltzailea: <input type="text" name="erabiltzailea" required><br><br>
		Pasahitza: <input type="password" name="pasahitza" required><br><br>
		
		<!-- Bidaltzeko botoia -->
		<input type="submit" id="login_submit" value="Sartu">
	</form>

	<p>
		<a href="index.php">Atzera hasierako orrira</a> 
	</p>
</body>
</html>

