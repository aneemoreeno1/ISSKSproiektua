<?php
// PHP Kodea
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
	$izena = $_POST['izena'];
	$nan = $_POST['nan'];
	$telefonoa = $_POST['telefonoa'];
	$data = $_POST['data'];
	$email = $_POST['email'];
	$pasahitza = $_POST['pasahitza']; // Pasahitza zifratu gabe

	// Datuak datu-basean gorde
	$sql = "INSERT INTO usuarios (nombre, nan, telefonoa, jaiotze_data, email, pasahitza) 
			VALUES ('$izena', '$nan', '$telefonoa', '$data', '$email', '$pasahitza')";

	// Exekutatu
	$emaitza = mysqli_query($conn, $sql);

	// Emaitza egiaztatu
	if ($emaitza) {
		echo "Ondo gorde da!";
	} else {
		echo "Arazo bat egon da: " . mysqli_error($conn);
	}
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Erregistratu</title>
	
	<script>
		function bakarrikLetrak(testua) {
			var patroia = /^[A-Za-zÑñ\s]+$/;
			return patroia.test(testua);
		}

		function bakarrikZenbakiak(testua) {
			var patroia = /^[0-9]+$/;
			return patroia.test(testua);
		}

		// NAN letra kalkulatzeko
		function kalkulatuNanLetra(nanZenbakiak) {
			var kate = "TRWAGMYFPDXBNJZSQVHLCKET";
			var zenbakiak = parseInt(nanZenbakiak);
			var posizioa = zenbakiak % 23;
			return kate[posizioa];
		}

		// Emaila egokia den ala ez egiaztatzeko
		function emailEgokia(emaila) {
			var patroia = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
			return patroia.test(emaila);
		}

		function datuakEgiaztatu() {
			// Izena: soilik testua
			var izena = document.register_form.izena.value;
			if (izena.length < 1) {
				window.alert("Izenak ezin du hutsik egon");
				return false;
			} else if (!bakarrikLetrak(izena)) {
				window.alert("Izenak soilik letrak izan behar ditu");
				return false;
			}

			// NAN: 12345678-Z
			var nan = document.register_form.nan.value;
			var nanZatiak = nan.split("-");
			var nanZenbakiak = nanZatiak[0];
			var nanLetraIdatzita = nanZatiak[1];
			if (nanZatiak.length != 2) {
				window.alert("NAN formatua okerra. Adibidea: 12345678-Z");
				return false;
			} else if (nanZenbakiak.length != 8 || !bakarrikZenbakiak(nanZenbakiak)) {
				window.alert("NAN-ak 8 zenbaki izan behar ditu");
				return false;
			} else if (nanLetraIdatzita.length != 1 || !bakarrikLetrak(nanLetraIdatzita)) {
				window.alert("NAN-aren letra okerra da");
				return false;
			} else {
				var nanLetraKalkulatua = kalkulatuNanLetra(nanZenbakiak);
				if (nanLetraKalkulatua.toLowerCase() != nanLetraIdatzita.toLowerCase()) {
					window.alert("NAN idatzita dagoena ez da zuzena");
					return false;
				}
			}

			// Telefonoa: 9 zenbaki
			var telefonoa = document.register_form.telefonoa.value;
			if (telefonoa.length != 9 || !bakarrikZenbakiak(telefonoa)) {
				window.alert("Telefonoak 9 zenbaki izan behar ditu");
				return false;
			}

			// Data: uuuu-hh-ee
			var data = document.register_form.data.value;
			var dataZatiak = data.split("-");
			if (data.length != 10 || dataZatiak.length != 3) {
				window.alert("Data formatua okerra. Adibidea: 2024-12-20");
				return false;
			} else if (
				dataZatiak[0].length != 4 || !bakarrikZenbakiak(dataZatiak[0]) ||
				dataZatiak[1].length != 2 || !bakarrikZenbakiak(dataZatiak[1]) ||
				dataZatiak[2].length != 2 || !bakarrikZenbakiak(dataZatiak[2])
			) {
				window.alert("Data formatua okerra. Urteak 4 zenbaki, hilabeteak 2, egunak 2");
				return false;
			}

			// Emaila: egokia izan behar da
			var emaila = document.register_form.email.value;
			if (!emailEgokia(emaila)) {
				window.alert("Emaila ez da zuzena. Adibidea: adibidea@zerbitzaria.extentsioa");
				return false;
			}

			// Datuak guztiz ongi badaude
			return true;
		}
	</script>
</head>
<body>
	<!-- HTML Kodea -->
	
	<h1>Erregistratu</h1>

	<!-- Erregistro formularioa -->
	<form id="register_form" name="register_form" method="POST" onsubmit="return datuakEgiaztatu()">
		Izena: <input type="text" name="izena" required><br><br>
		NAN: <input type="text" name="nan" required><br><br>
		Telefonoa: <input type="text" name="telefonoa" required><br><br>
		Jaiotze data: <input type="text" name="data" required><br><br>
		Email: <input type="text" name="email" required><br><br>
		Pasahitza: <input type="password" name="pasahitza" required><br><br>

		<!-- Bidaltzeko botoia -->
		<input type="submit" id="register_submit" value="Erregistratu">
	</form>

	<p><a href="index.php">Atzera hasierako orrira</a></p>
</body>
</html>

