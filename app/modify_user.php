<?php
// modify_user.php - Erabiltzailearen datuak aldatzeko orria

// Security headers
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self'; connect-src 'self'; media-src 'self'; object-src 'none'; child-src 'none'; frame-src 'none'; worker-src 'none'; manifest-src 'self'; base-uri 'self'; form-action 'self';");
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

// Explicitly set session cookie with all security attributes
if (session_id()) {
    setcookie(session_name(), session_id(), [
        'expires' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
}

// Session timeout
$timeout = 900;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=1");
    exit;
}
$_SESSION['last_activity'] = time();

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

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validate_phone($phone) {
    return preg_match('/^[0-9]{9}$/', $phone);
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

// Erabiltzailea saioa hasita dagoen egiaztatu
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Erabiltzailearen IDa lortu
$user_id = intval($_SESSION['user_id']);

// Datu-baseatik erabiltzailearen datuak kargatu using prepared statement
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Erabiltzailea existitzen dela egiaztatu
if ($result->num_rows > 0) {
    $erabiltzailea = $result->fetch_array();
} else {
    die("Erabiltzailea ez da existitzen. Lehenengo erabiltzailea sortu.");
}
$stmt->close();

$success_message = "";
$error_message = "";

// Formularioa bidali bada, datuak prozesatu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verify CSRF token
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error_message = "Segurtasun errorea. Mesedez, saiatu berriro.";
    } else {
        // Formulariotik datuak eskuratu
        $izena = trim(htmlspecialchars($_POST['izena'] ?? '', ENT_QUOTES, 'UTF-8'));
        $nan = trim(htmlspecialchars($_POST['nan'] ?? '', ENT_QUOTES, 'UTF-8'));
        $telefonoa = trim(htmlspecialchars($_POST['telefonoa'] ?? '', ENT_QUOTES, 'UTF-8'));
        $data = trim(htmlspecialchars($_POST['data'] ?? '', ENT_QUOTES, 'UTF-8'));
        $email = trim(htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'));
        $pasahitza = $_POST['pasahitza'] ?? '';
        
        // Server-side validation
        if (empty($izena) || !preg_match('/^[A-Za-zÑñ\s]{1,50}$/', $izena)) {
            $error_message = "Izen baliogabea.";
        } elseif (!validate_phone($telefonoa)) {
            $error_message = "Telefono zenbaki baliogabea.";
        } elseif (!validate_email($email)) {
            $error_message = "Email helbide baliogabea.";
        } elseif (strlen($pasahitza) < 8 || !preg_match('/[0-9]/', $pasahitza) || !preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $pasahitza)) {
            $error_message = "Pasahitza ez da segurua. Gutxienez 8 karaktere, zenbaki bat eta karaktere berezi bat izan behar ditu.";
        } else {
            // Hash the password before storing
            $hashed_password = password_hash($pasahitza, PASSWORD_DEFAULT);
            
            // NAN ezin da aldatu, beraz datu-basean eguneraketa egiteko kontsulta using prepared statement
            $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, telefonoa = ?, jaiotze_data = ?, email = ?, pasahitza = ? WHERE id = ?");
            $stmt->bind_param("sssssi", $izena, $telefonoa, $data, $email, $hashed_password, $user_id);
            
            if ($stmt->execute() && $stmt->affected_rows > 0) {
                $success_message = "Datuak eguneratuak!";
                // Update session data
                $_SESSION['user_name'] = $izena;
                // Datuak berriro kargatu formularioan erakusteko
                $stmt_reload = $conn->prepare("SELECT * FROM usuarios WHERE id = ?");
                $stmt_reload->bind_param("i", $user_id);
                $stmt_reload->execute();
                $result = $stmt_reload->get_result();
                $erabiltzailea = $result->fetch_array();
                $stmt_reload->close();
            } else {
                $error_message = "Arazo bat egon da datuak eguneratzean.";
                error_log("Modify user error: " . $stmt->error);
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
    <title>Erabiltzailearen datuak aldatu</title>
    <link rel="stylesheet" href="style2.css">

    <script>
        // Izena letrak soilik direla egiaztatzeko funtzioa
        function bakarrikLetrak(testua) {
            return /^[A-Za-zÑñ\s]+$/.test(testua);
        }
        
        // Zenbakiak soilik direla egiaztatzeko funtzioa
        function bakarrikZenbakiak(testua) {
            return /^[0-9]+$/.test(testua);
        }
        
        // NAN-aren letra kalkulatzeko funtzioa
        function kalkulatuNanLetra(nanZenbakiak) {
            var kate = "TRWAGMYFPDXBNJZSQVHLCKET";
            var zenbakiak = parseInt(nanZenbakiak);
            var posizioa = zenbakiak % 23;
            return kate[posizioa];
        }
        
        // Emailaren formatua zuzena den egiaztatzeko funtzioa
        function emailEgokia(emaila) {
            return /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/.test(emaila);
        }
        
        // Formularioaren datu guztiak egiaztatzeko funtzio nagusia
        function datuakEgiaztatu() {
            // Izena lortu eta egiaztatu
            var izena = document.user_modify_form.izena.value;
            if (izena.length < 1 || !bakarrikLetrak(izena)) {
                alert("Izenak ezin du hutsik egon eta soilik letrak izan behar ditu");
                return false;
            }

            // NAN lortu eta egiaztatu
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

            // Telefonoa lortu eta egiaztatu
            var telefonoa = document.user_modify_form.telefonoa.value;
            if (telefonoa.length != 9 || !bakarrikZenbakiak(telefonoa)) {
                alert("Telefonoak 9 zenbaki izan behar ditu");
                return false;
            }

            // Data egiaztatu
            var data = document.user_modify_form.data.value;
            var dataZatiak = data.split("-");
            if (data.length != 10 || dataZatiak.length != 3) {
                alert("Data formatua okerra. Adibidea: 2024-12-20");
                return false;
            }
            
            // Data zatiak analizatu
            var urtea = parseInt(dataZatiak[0]);
            var hilabetea = parseInt(dataZatiak[1]);
            var eguna = parseInt(dataZatiak[2]);
            
            // Hilabetea baliozkoa den egiaztatu
            if (hilabetea < 1 || hilabetea > 12) {
                alert("Hilabetea 1 eta 12 artean egon behar da");
                return false;
            }

            // Hilabete bakoitzaren egun kopurua zehaztu (bisustua kontuan hartuz)            var egunMaximoak = [31,28,31,30,31,30,31,31,30,31,30,31];
            if ((urtea % 4 === 0 && urtea % 100 !== 0) || (urtea % 400 === 0)) {
                egunMaximoak[1] = 29;
            }
            if (eguna < 1 || eguna > egunMaximoak[hilabetea - 1]) {
                alert("Eguna okerra. " + hilabetea + ". hilabeteak " + egunMaximoak[hilabetea-1] + " egun baino ez ditu izan");
                return false;
            }
            
            // Data ez dela 120 urte baino zaharragoa egiaztatu
            var gaur = new Date();
            if (urtea < gaur.getFullYear() - 120) {
                alert("Ezin da 120 urte baino gehiago izan");
                return false;
            }

            // Email lortu eta egiaztatu
            var emaila = document.user_modify_form.email.value;
            if (!emailEgokia(emaila)) {
                alert("Emaila ez da zuzena");
                return false;
            }

            // Pasahitza lortu eta egiaztatu
            var pasahitza = document.user_modify_form.pasahitza.value;
            var errep_pasahitza = document.user_modify_form.errep_pasahitza.value;
            if (pasahitza != errep_pasahitza) {
                alert("Pasahitzak ez dira berdinak.");
                return false;
            }
            
            // Pasahitzaren segurtasuna egiaztatu
            if (pasahitza.length < 8 || !/[0-9]/.test(pasahitza) || !/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(pasahitza)) {
                alert("Pasahitza ez segurua. Gutxienez 8 karaktere, zenbaki bat eta karaktere berezi bat izan behar ditu.");
                return false;
            }

            // Datu guztiak zuzenak badira, formularioa bidali
            return true;
        }
    </script>
</head>
<body>
    <div class="wrapper">
        <h1>Erabiltzailearen datuak aldatu</h1>
        
        <?php if ($success_message): ?>
            <p style="color:#66ff66; text-align:center; margin-bottom:15px;"><?= safe_output($success_message) ?></p>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <p style="color:#ff6666; text-align:center; margin-bottom:15px;"><?= safe_output($error_message) ?></p>
        <?php endif; ?>
        
        <!-- Erabiltzailearen datuak aldatzeko formularioa -->
        <form id="user_modify_form" name="user_modify_form" method="POST" onsubmit="return datuakEgiaztatu()">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
            <label for="izena">Izena:</label><br>
            <input type="text" name="izena" style="width: 100%;" value="<?= safe_output($erabiltzailea['nombre']) ?>" required maxlength="50">

            <label for="nan">NAN:</label><br>
            <!-- Mostrar el NAN como texto (no editable) y añadir un input hidden para preservar el valor en el POST -->
            <div style="width: 100%; padding:8px; background:#f5f5f5; border-radius:8px; box-sizing:border-box;"><?= safe_output($erabiltzailea['nan']) ?></div>
            <input type="hidden" name="nan" value="<?= safe_output($erabiltzailea['nan']) ?>">

            <label for="telefonoa">Telefonoa:</label><br>
            <input type="tel" name="telefonoa" style="width: 100%;" value="<?= safe_output($erabiltzailea['telefonoa']) ?>" required maxlength="9" pattern="[0-9]{9}">

            <label for="data">Jaiotze data:</label><br>
            <input type="text" name="data" style="width: 100%;" value="<?= safe_output($erabiltzailea['jaiotze_data']) ?>" required maxlength="10" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}">

            <label for="email">Email:</label><br>
            <input type="email" name="email" style="width: 100%;" value="<?= safe_output($erabiltzailea['email']) ?>" required maxlength="100">

            <label for="pasahitza">Pasahitza berria:</label><br>
            <input type="password" name="pasahitza" style="width: 100%;" placeholder="Sartu pasahitza berria" required maxlength="255">

            <label for="errep_pasahitza">Errepikatu pasahitza berria:</label><br>
            <input type="password" name="errep_pasahitza" style="width: 100%;" placeholder="Errepikatu pasahitza berria" required maxlength="255">
            
            <div class="botoiak">
                <button type="submit" class="btn-primary" id="user_modify_submit">Datuak gorde</button>
                <button type="button" class="btn-secondary" onclick="window.location.href='index.php'">Atzera</button>
            </div>
        </form>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>
