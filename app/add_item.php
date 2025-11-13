<?php


header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self'; font-src 'self'; connect-src 'self'; frame-ancestors 'self';");

if (!isset($_SERVER['HTTPS']) && $_SERVER['SERVER_PORT'] == '81') {
    $_SERVER['HTTPS'] = 'on'; 
}

session_start([
    'lifetime' => 0,
    'path' => '/',
    'secure' => true,      
    'httponly' => true,   
    'samesite' => 'Strict'
]);

if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php");
    exit;
}

//____________________________Pelikula berria gehitu_________________________________________________

$hostname = "db";
$username = "admin";
$password = "test";
$db = "database";

$conn = mysqli_connect($hostname, $username, $password, $db);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$mezua = ""; // Mezua erakusteko textu hutsa (aldagaia)

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $izena = $_POST['izena'];
    $deskribapena = $_POST['deskribapena'];
    $urtea = $_POST['urtea'];
    $egilea = $_POST['egilea'];
    $generoa = $_POST['generoa'];

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO pelikulak (izena, deskribapena, urtea, egilea, generoa) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiss", $izena, $deskribapena, $urtea, $egilea, $generoa);
    
    if ($stmt->execute()) {
        $mezua = "Pelikula ondo gehitu da!";
    } else {
        $mezua = "Arazo bat egon da: " . $stmt->error;
    }
    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <title>Pelikula berria gehitu</title>
    <link rel="stylesheet" href="style2.css">
    <script>

        function bakarrikLetrak(testua) {
            return /^[A-Za-zÑñ\s]+$/.test(testua); 
        }
        function bakarrikZenbakiak(testua) {
            return /^[0-9]+$/.test(testua);
        }
        function bakarrikLetrakEtaZenbakiak(testua) {
            return /^[A-Za-zÑñ0-9\s.,!?¡¿()-]+$/.test(testua);
        }
        function gutxienezLetraBat(testua) {
            return /[A-Za-zÑñ]/.test(testua);
        }

        function datuakEgiaztatu() {
            var izena = document.item_add_form.izena.value;
            if (izena.length < 1) {
                alert("Izenak ezin du hutsik egon");
                return false;
            } else if (!bakarrikLetrakEtaZenbakiak(izena)) {
                alert("Izenak soilik letrak, zenbakiak eta karaktere arruntak izan behar ditu");
                return false;
            }

            var deskribapena = document.item_add_form.deskribapena.value;
            if (deskribapena.length > 500) {
                alert("Deskribapenak ezin du 500 karaktere baino gehiago izan");
                return false;
            }

            var urtea = document.item_add_form.urtea.value;
            if (urtea !== "") {
                if (!bakarrikZenbakiak(urtea)) {
                    alert("Urteak zenbaki osoa izan behar du");
                    return false;
                }
                var urteZenbakia = parseInt(urtea);
                var gaurkoUrtea = new Date().getFullYear();
                if (urteZenbakia < 1888) {
                    alert("Urtea ez da egokia. 1888 baino handiagoa izan behar da");
                    return false;
                }
                if (urteZenbakia > gaurkoUrtea + 5) {
                    alert("Urtea ez da egokia. Ezin da etorkizuneko 5 urte baino gehiago izan");
                    return false;
                }
            }

            var egilea = document.item_add_form.egilea.value;
            if (egilea !== "" && !bakarrikLetrakEtaZenbakiak(egilea)) {
                alert("Egileak soilik letrak, zenbakiak eta karaktere arruntak izan behar ditu");
                return false;
            } else if (egilea !== "" && !gutxienezLetraBat(egilea)) {
                alert("Egileak gutxienez letra bat izan behar du");
                return false;
            }

            var generoa = document.item_add_form.generoa.value;
            if (generoa !== "" && !bakarrikLetrak(generoa)) {
                alert("Generoak soilik letrak izan behar ditu");
                return false;
            } else if (generoa !== "" && !gutxienezLetraBat(generoa)) {
                alert("Generoak gutxienez letra bat izan behar du");
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
    <div class="wrapper">
        <h1>Pelikula berria gehitu</h1>

        <?php if ($mezua !== ""): ?>
            <p style="text-align:center; font-weight:bold; font-size:1.2em; color:#66ff66;">
                <?php echo $mezua; ?>
            </p>
        <?php endif; ?>

        <form id="item_add_form" name="item_add_form" method="POST" onsubmit="return datuakEgiaztatu()">
            <div class="form-grid">
                <div>
                    <label for="izena">Izena:</label><br>
                    <input type="text" name="izena" style="width: 100%;" required>
                </div>             

                <div>
                    <label for="urtea">Urtea:</label><br>
                    <input type="number" name="urtea" style="width: 100%" value="<?php echo date('Y'); ?>">
                </div>

                <div>
                    <label for="egilea">Egilea:</label><br>
                    <input type="text" name="egilea" style="width: 100%;">
                </div>

                <div>
                    <label for="generoa">Generoa:</label><br>
                    <input type="text" name="generoa" style="width: 100%;">
                </div>
            </div>

            <div class="full-width">
                    <label for="deskribapena">Deskribapena:</label><br>
                    <textarea name="deskribapena" rows="4" style="width: 100%;"></textarea>
                </div>

            <div class="botoiak" style="margin-top:18px;">
                <button id="item_add_submit" type="submit" class="btn-primary">Gehitu</button>
                <button type="button" class="btn-secondary" onclick="window.location.href='items.php'">Atzera</button>
            </div>
        </form>
    </div>
</body>
</html>
