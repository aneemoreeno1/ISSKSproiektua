<?php
// register.php - Erabiltzaile berria gehitu

$hostname = "db";
$username = "admin";
$password = "test";
$db = "database";

$conn = mysqli_connect($hostname, $username, $password, $db);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$mezua = ""; // Mezua erakusteko

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $izena = $_POST['izena'];
    $nan = $_POST['nan'];
    $telefonoa = $_POST['telefonoa'];
    $data = $_POST['data'];
    $email = $_POST['email'];
    $pasahitza = $_POST['pasahitza']; // Pasahitza zifratu gabe

    $sql = "INSERT INTO usuarios (nombre, nan, telefonoa, jaiotze_data, email, pasahitza) 
            VALUES ('$izena', '$nan', '$telefonoa', '$data', '$email', '$pasahitza')";

    if (mysqli_query($conn, $sql)) {
        $mezua = "Ondo gorde da!";
    } else {
        $mezua = "Arazo bat egon da: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <title>Erregistratu</title>
    <link rel="stylesheet" href="style2.css">
    <script>
        
        function bakarrikLetrak(testua) {
            return /^[A-Za-zÑñ\s]+$/.test(testua);
        }

        function bakarrikZenbakiak(testua) {
            return /^[0-9]+$/.test(testua);
        }

        function kalkulatuNanLetra(nanZenbakiak) {
            var kate = "TRWAGMYFPDXBNJZSQVHLCKET";
            return kate[parseInt(nanZenbakiak) % 23];
        }

        function emailEgokia(emaila) {
            return /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/.test(emaila);
        }

        function datuakEgiaztatu() {
            var izena = document.register_form.izena.value;
            if (izena.length < 1 || !bakarrikLetrak(izena)) {
                alert("Izenak ezin du hutsik egon eta soilik letrak izan behar ditu");
                return false;
            }

            var nan = document.register_form.nan.value;
            var nanZatiak = nan.split("-");
            if (nanZatiak.length != 2 || nanZatiak[0].length != 8 || !bakarrikZenbakiak(nanZatiak[0]) || nanZatiak[1].length != 1) {
                alert("NAN formatua okerra. Adibidea: 12345678-Z");
                return false;
            }
            if (kalkulatuNanLetra(nanZatiak[0]).toLowerCase() != nanZatiak[1].toLowerCase()) {
                alert("NAN idatzita dagoena ez da zuzena");
                return false;
            }

            var telefonoa = document.register_form.telefonoa.value;
            if (telefonoa.length != 9 || !bakarrikZenbakiak(telefonoa)) {
                alert("Telefonoak 9 zenbaki izan behar ditu");
                return false;
            }

            var data = document.register_form.data.value;
            var dataZatiak = data.split("-");
            if (data.length != 10 || dataZatiak.length != 3) {
                alert("Data formatua okerra. Adibidea: 2024-12-20");
                return false;
            }

            var urtea = parseInt(dataZatiak[0]);
            var hilabetea = parseInt(dataZatiak[1]);
            var eguna = parseInt(dataZatiak[2]);

            if (hilabetea < 1 || hilabetea > 12) {
                alert("Hilabetea 1 eta 12 artean egon behar da");
                return false;
            }

            var egunMaximoak = [31,28,31,30,31,30,31,31,30,31,30,31];
            if ((urtea % 4 === 0 && urtea % 100 !== 0) || (urtea % 400 === 0)) {
                egunMaximoak[1] = 29;
            }
            if (eguna < 1 || eguna > egunMaximoak[hilabetea-1]) {
                alert("Eguna okerra. " + hilabetea + ". hilabeteak " + egunMaximoak[hilabetea-1] + " egun baino ez ditu izan");
                return false;
            }

            var gaur = new Date();
            if (urtea < gaur.getFullYear() - 120 || urtea > gaur.getFullYear()) {
                alert("Urte okerra. Ez da 120 urte baino gehiago edo etorkizuneko data izan");
                return false;
            }

            if (!emailEgokia(document.register_form.email.value)) {
                alert("Emaila ez da zuzena");
                return false;
            }

            var pasahitza = document.register_form.pasahitza.value;
            var errep_pasahitza = document.register_form.errep_pasahitza.value;
            if (pasahitza != errep_pasahitza) {
                alert("Pasahitzak ez dira berdinak.");
                return false;
            }

            if (pasahitza.length < 8 || !/[0-9]/.test(pasahitza) || !/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(pasahitza)) {
                alert("Pasahitza ez segurua. Gutxienez 8 karaktere, zenbaki bat eta karaktere berezi bat izan behar ditu.");
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
    <div class="wrapper">
        <h1>Erregistratu</h1>

        <?php if ($mezua !== ""): ?>
            <p style="text-align:center; font-weight:bold; font-size:1.2em; color:#66ff66;">
                <?php echo $mezua; ?>
            </p>
        <?php endif; ?>

        <form id="register_form" name="register_form" method="POST" onsubmit="return datuakEgiaztatu()">
            <label for="izena">Izena:</label>
            <input type="text" id="izena" name="izena" required>

            <label for="nan">NAN:</label>
            <input type="text" id="nan" name="nan" required>

            <label for="telefonoa">Telefonoa:</label>
            <input type="text" id="telefonoa" name="telefonoa" required>

            <label for="data">Jaiotze data:</label>
            <input type="text" id="data" name="data" required>

            <label for="email">Email:</label>
            <input type="text" id="email" name="email" required>

            <label for="pasahitza">Pasahitza:</label>
            <input type="password" id="pasahitza" name="pasahitza" required>

            <label for="errep_pasahitza">Errepikatu pasahitza:</label>
            <input type="password" id="errep_pasahitza" name="errep_pasahitza" required>

            <div class="botoiak">
                <button type="submit" class="btn-primary" id="register_submit">Erregistratu</button>
                <button type="button" class="btn-secondary" onclick="window.location.href='index.php'">Atzera</button>
            </div>
        </form>
    </div>
</body>
</html>
