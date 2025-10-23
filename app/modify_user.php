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

	// Saioa egiaztatu
	if (!isset($_SESSION['user_id'])) {
		header("Location: login.php");
		exit();
	}

	// ID lortu
	$user_id = $_SESSION['user_id'];

	// Erabiltzailearen datuak kargatu
	$sql = "SELECT * FROM usuarios WHERE id = $user_id";
	$emaitza = mysqli_query($conn, $sql);

	if ($emaitza->num_rows > 0) {
		$erabiltzailea = mysqli_fetch_array($emaitza);
	} else {
		die("Erabiltzailea ez da existitzen. Lehenengo erabiltzailea sortu.");
	}

	// Formularioa bidali bada
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		$izena = $_POST['izena'];
		$nan = $_POST['nan'];
		$telefonoa = $_POST['telefonoa'];
		$data = $_POST['data'];
		$email = $_POST['email'];
		$pasahitza = $_POST['pasahitza']; 
		
		// Datuak eguneratu
		$sql = "UPDATE usuarios 
				SET nombre = '$izena', 
					nan = '$nan', 
					telefonoa = '$telefonoa', 
					jaiotze_data = '$data', 
					email = '$email', 
					pasahitza = '$pasahitza'
				WHERE id = $user_id";
		
		$emaitza = mysqli_query($conn, $sql);
		
		if ($emaitza) {
			echo "<script>alert('Datuak eguneratuak!');</script>";
			// Datuak berriro kargatu
			$sql = "SELECT * FROM usuarios WHERE id = $user_id";
			$emaitza = mysqli_query($conn, $sql);
			$erabiltzailea = mysqli_fetch_array($emaitza);
		} else {
			echo "Errorea: " . mysqli_error($conn);
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Erabiltzailearen datuak aldatu</title>

	<script>
		function bakarrikLetrak(testua) {
			var patroia = /^[A-Za-zÑñ\s]+$/;
			return patroia.test(testua);
		}

		function bakarrikZenbakiak(testua) {
			var patroia = /^[0-9]+$/;
			return patroia.test(testua);
		}

		function kalkulatuNanLetra(nanZenbakiak) {
			var kate = "TRWAGMYFPDXBNJZSQVHLCKET";
			var zenbakiak = parseInt(nanZenbakiak);
			var posizioa = zenbakiak % 23;
			return kate[posizioa];
		}

		function emailEgokia(emaila) {
			var patroia = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
			return patroia.test(emaila);
		}

		function datuakEgiaztatu() {
			// Izena
			var izena = document.user_modify_form.izena.value;
			if (izena.length < 1) {
				alert("Izenak ezin du hutsik egon");
				return false;
			} else if (!bakarrikLetrak(izena)) {
				alert("Izenak soilik letrak izan behar ditu");
				return false;
			}

			// NAN
			var nan = document.user_modify_form.nan.value;
			var nanZatiak = nan.split("-");
			if (nanZatiak.length != 2) {
				alert("NAN formatua okerra. Adibidea: 12345678-Z");
				return false;
			}
			var nanZenbakiak = nanZatiak[0];
			var nanLetraIdatzita = nanZatiak[1];
			if (nanZenbakiak.length != 8 || !bakarrikZenbakiak(nanZenbakiak)) {
				alert("NAN-ak 8 zenbaki izan behar ditu");
				return false;
			}
			var nanLetraKalkulatua = kalkulatuNanLetra(nanZenbakiak);
			if (nanLetraKalkulatua.toLowerCase() != nanLetraIdatzita.toLowerCase()) {
				alert("NAN ez da zuzena");
				return false;
			}

			// Telefonoa
			var telefonoa = document.user_modify_form.telefonoa.value;
			if (telefonoa.length != 9 || !bakarrikZenbakiak(telefonoa)) {
				alert("Telefonoak 9 zenbaki izan behar ditu");
				return false;
			}

			// Data
			var data = document.user_modify_form.data.value;
			var patroia = /^\d{4}-\d{2}-\d{2}$/;
			if (!patroia.test(data)) {
				alert("Data formatua okerra. Adibidea: 2024-12-20");
				return false;
			}

			// Emaila
			var emaila = document.user_modify_form.email.value;
			if (!emailEgokia(emaila)) {
				alert("Emaila ez da zuzena");
				return false;
			}

			// Pasahitza (SOILIK OHARRA)
			var pasahitza = document.user_modify_form.pasahitza.value;
			
			if (pasahitza.length < 8) {
				window.alert("Pasahitza ez segurua. Gutxienez 8 karaktere gomendatzen dira.");
			}else if (!/[0-9]/.test(pasahitza)) {
				window.alert("Pasahitza ez segurua. Gutxienez zenbaki bat gomendatzen da.");
			}else if (!/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(pasahitza)) {
				window.alert("Pasahitza ez segurua. Gutxienez karaktere berezi bat gomendatzen da.");
			}

			// Errepikatu passahitza (biak berdinak izan behar dira)
			var errep_pasahitza = document.user_modify_form.errep_pasahitza.value;
			if (pasahitza != errep_pasahitza) {
				window.alert("Pasahitzak ez dira berdinak.");
				return false;
			}
	

			return true;
		}
	</script>
</head>

<body>
	<h1>Erabiltzailearen datuak aldatu</h1>

	<form id="user_modify_form" name="user_modify_form" method="POST" onsubmit="return datuakEgiaztatu()">
		Izena: <input type="text" name="izena" value="<?php echo $erabiltzailea['nombre']; ?>" required><br><br>
		NAN: <input type="text" name="nan" value="<?php echo $erabiltzailea['nan']; ?>" required><br><br>
		Telefonoa: <input type="text" name="telefonoa" value="<?php echo $erabiltzailea['telefonoa']; ?>" required><br><br>
		Jaiotze data: <input type="text" name="data" value="<?php echo $erabiltzailea['jaiotze_data']; ?>" required><br><br>
		Email: <input type="text" name="email" value="<?php echo $erabiltzailea['email']; ?>" required><br><br>
		Pasahitza: <input type="password" name="pasahitza" value="<?php echo $erabiltzailea['pasahitza']; ?>" required><br><br>
		Errepikatu pasahitza: <input type="password" name="errep_pasahitza" value="<?php echo $erabiltzailea['pasahitza']; ?>" required><br><br>

		
		<!-- Bidaltzeko botoia -->
		<input type="submit" id="user_modify_submit" value="Datuak gorde">
	</form>

	<p><a href="index.php">Atzera zerrendara</a></p>
</body>
</html>
