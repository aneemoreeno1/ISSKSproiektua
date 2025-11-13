<?php
//____________________________Pelikula berria gehitu_________________________________________________

// Security headers
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; object-src 'none'; base-uri 'self'; form-action 'self';");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
header("Permissions-Policy: geolocation=(), microphone=(), camera=()");
// Remove server information
header("Server: ");
header_remove("X-Powered-By");

// Secure session configuration with all security flags
session_set_cookie_params([
   'lifetime' => 0,
   'path' => '/',
   'domain' => '',
   'secure' => true,
   'httponly' => true,
   'samesite' => 'Strict'
]);

// Set additional cookie security
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Strict');
session_start();

if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

// Security functions
function safe_output($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function validate_year($year) {
    $current_year = date('Y');
    return is_numeric($year) && $year >= 1888 && $year <= ($current_year + 5);
}

// Database connection
$hostname = "db";
$username = "admin";
$password = "test";
$db = "database";

$conn = mysqli_connect($hostname, $username, $password, $db);
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("Database connection error");
}
mysqli_set_charset($conn, 'utf8');

$mezua = ""; // Mezua erakusteko textu hutsa (aldagaia)
$mezua_type = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verify CSRF token
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $mezua = "Segurtasun errorea. Mesedez, saiatu berriro.";
        $mezua_type = "error";
    } else {
        $izena = trim(htmlspecialchars($_POST['izena'] ?? '', ENT_QUOTES, 'UTF-8'));
        $deskribapena = trim(htmlspecialchars($_POST['deskribapena'] ?? '', ENT_QUOTES, 'UTF-8'));
        $urtea = filter_var($_POST['urtea'] ?? '', FILTER_VALIDATE_INT);
        $egilea = trim(htmlspecialchars($_POST['egilea'] ?? '', ENT_QUOTES, 'UTF-8'));
        $generoa = trim(htmlspecialchars($_POST['generoa'] ?? '', ENT_QUOTES, 'UTF-8'));

        // Server-side validation
        if (empty($izena) || strlen($izena) > 100) {
            $mezua = "Izen baliogabea.";
            $mezua_type = "error";
        } elseif (strlen($deskribapena) > 500) {
            $mezua = "Deskribapena luzeegia da.";
            $mezua_type = "error";
        } elseif ($urtea && !validate_year($urtea)) {
            $mezua = "Urte baliogabea.";
            $mezua_type = "error";
        } else {
            // Use prepared statement to prevent SQL injection
            $stmt = $conn->prepare("INSERT INTO pelikulak (izena, deskribapena, urtea, egilea, generoa) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiss", $izena, $deskribapena, $urtea, $egilea, $generoa);
            
            if ($stmt->execute()) {
                $mezua = "Pelikula ondo gehitu da!";
                $mezua_type = "success";
            } else {
                $mezua = "Arazo bat egon da datuak gordetzean.";
                $mezua_type = "error";
                error_log("Add item error: " . $stmt->error);
            }
            $stmt->close();
        }
    }
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
            <p style="text-align:center; font-weight:bold; font-size:1.2em; <?php echo ($mezua_type === 'success') ? 'color:#66ff66;' : 'color:#ff6666;'; ?>">
                <?php echo safe_output($mezua); ?>
            </p>
        <?php endif; ?>

        <form id="item_add_form" name="item_add_form" method="POST" onsubmit="return datuakEgiaztatu()">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
            <div class="form-grid">
                <div>
                    <label for="izena">Izena:</label><br>
                    <input type="text" name="izena" style="width: 100%;" required maxlength="100">
                </div>             

                <div>
                    <label for="urtea">Urtea:</label><br>
                    <input type="number" name="urtea" style="width: 100%" min="1888" max="<?php echo date('Y') + 5; ?>" value="<?php echo date('Y'); ?>">
                </div>

                <div>
                    <label for="egilea">Egilea:</label><br>
                    <input type="text" name="egilea" style="width: 100%;" maxlength="100">
                </div>

                <div>
                    <label for="generoa">Generoa:</label><br>
                    <input type="text" name="generoa" style="width: 100%;" maxlength="50">
                </div>
            </div>

            <div class="full-width">
                    <label for="deskribapena">Deskribapena:</label><br>
                    <textarea name="deskribapena" rows="4" style="width: 100%;" maxlength="500"></textarea>
                </div>

            <div class="botoiak" style="margin-top:18px;">
                <button id="item_add_submit" type="submit" class="btn-primary">Gehitu</button>
                <button type="button" class="btn-secondary" onclick="window.location.href='items.php'">Atzera</button>
            </div>
        </form>
    </div>
</body>
</html>
