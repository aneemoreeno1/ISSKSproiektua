<?php
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
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <title>Erabiltzailearen datuak aldatu</title>
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
            var zenbakiak = parseInt(nanZenbakiak);
            var posizioa = zenbakiak % 23;
            return kate[posizioa];
        }

        function emailEgokia(emaila) {
            return /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/.test(emaila);
        }

        function datuakEgiaztatu() {
            // Izena
            var izena = document.user_modify_form.izena.value;
            if (izena.length < 1 || !bakarrikLetrak(izena)) {
                alert("Izenak ezin du hutsik egon eta soilik letrak izan behar ditu");
                return false;
            }

            // NAN
            var nan = document.user_modify_form.nan.value;
            var nanZatiak = nan.split("-");
            if (nanZatiak.length != 2 || nanZatiak[0].length != 8 || !bakarrikZenbakiak(nanZatiak[0])) {
                alert("NAN formatua okerra. Adibidea: 12345678-Z");
                return false;
            }
            if (kalkulatuNanLetra(nanZatiak[0]).toLowerCase() != nanZatiak[1].toLowerCase()) {
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
            if (eguna < 1 || eguna > egunMaximoak[hilabetea - 1]) {
                alert("Eguna okerra. " + hilabetea + ". hilabeteak " + egunMaximoak[hilabetea-1] + " egun baino ez ditu izan");
                return false;
            }

            var gaur = new Date();
            if (urtea < gaur.getFullYear() - 120) {
                alert("Ezin da 120 urte baino gehiago izan");
                return false;
            }

            // Email
            var emaila = document.user_modify_form.email.value;
            if (!emailEgokia(emaila)) {
                alert("Emaila ez da zuzena");
                return false;
            }

            // Pasahitza
            var pasahitza = document.user_modify_form.pasahitza.value;
            var errep_pasahitza = document.user_modify_form.errep_pasahitza.value;
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
        <h1>Erabiltzailearen datuak aldatu</h1>

        <form id="user_modify_form" name="user_modify_form" method="POST" onsubmit="return datuakEgiaztatu()">
            <label for="izena">Izena:</label>
            <input type="text" name="izena" value="<?= $erabiltzailea['nombre'] ?>" required>

            <label for="nan">NAN:</label>
            <input type="text" name="nan" value="<?= $erabiltzailea['nan'] ?>" required>

            <label for="telefonoa">Telefonoa:</label>
            <input type="text" name="telefonoa" value="<?= $erabiltzailea['telefonoa'] ?>" required>

            <label for="data">Jaiotze data:</label>
            <input type="text" name="data" value="<?= $erabiltzailea['jaiotze_data'] ?>" required>

            <label for="email">Email:</label>
            <input type="text" name="email" value="<?= $erabiltzailea['email'] ?>" required>

            <label for="pasahitza">Pasahitza:</label>
            <input type="password" name="pasahitza" value="<?= $erabiltzailea['pasahitza'] ?>" required>

            <label for="errep_pasahitza">Errepikatu pasahitza:</label>
            <input type="password" name="errep_pasahitza" value="<?= $erabiltzailea['pasahitza'] ?>" required>

            <div class="botoiak">
                <button type="submit" id="user_modify_submit">Datuak gorde</button>
                <button type="button" onclick="window.location.href='index.php'">Atzera</button>
            </div>
        </form>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>
