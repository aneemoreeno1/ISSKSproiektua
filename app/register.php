<?php
// register.php - Erabiltzaile berria gehitu

// Security headers
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; font-src 'self'; connect-src 'self'; media-src 'self'; object-src 'none'; child-src 'none'; frame-src 'none'; worker-src 'none'; manifest-src 'self'; base-uri 'self'; form-action 'self'; frame-ancestors 'none';");
header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
header("Permissions-Policy: geolocation=(), microphone=(), camera=()");
// Remove server information
header("Server: ");
header_remove("X-Powered-By");

// Set session cookie parameters BEFORE starting session - this is critical for SameSite
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.name', 'SECURE_SESSID');

// Set session parameters before session_start
session_set_cookie_params([
   'lifetime' => 0,
   'path' => '/',
   'domain' => '',
   'secure' => true,
   'httponly' => true,
   'samesite' => 'Strict'
]);

// Now start session with proper parameters already set
session_start();

if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

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

function check_rate_limit($action, $max_attempts = 3, $time_window = 300) {
    $client_ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $key = $action . '_' . $client_ip;
    
    if (!isset($_SESSION['rate_limit'])) {
        $_SESSION['rate_limit'] = [];
    }
    
    $now = time();
    
    foreach ($_SESSION['rate_limit'] as $k => $v) {
        if ($v['time'] < ($now - $time_window)) {
            unset($_SESSION['rate_limit'][$k]);
        }
    }
    
    if (!isset($_SESSION['rate_limit'][$key])) {
        $_SESSION['rate_limit'][$key] = ['count' => 0, 'time' => $now];
    }
    
    if ($_SESSION['rate_limit'][$key]['count'] >= $max_attempts) {
        return false;
    }
    
    $_SESSION['rate_limit'][$key]['count']++;
    $_SESSION['rate_limit'][$key]['time'] = $now;
        
    return true;
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validate_nan($nan) {
    if (!preg_match('/^[0-9]{8}-[A-Za-z]$/', $nan)) {
        return false;
    }
    
    $numbers = substr($nan, 0, 8);
    $letter = strtoupper(substr($nan, 9, 1));
    $valid_letters = "TRWAGMYFPDXBNJZSQVHLCKET";
    $expected_letter = $valid_letters[intval($numbers) % 23];
    
    return $letter === $expected_letter;
}

function validate_phone($phone) {
    return preg_match('/^[0-9]{9}$/', $phone);
}

$mezua = ""; // Erabiltzaileari mezuak erakusteko aldagaia
$mezua_type = ""; // "success" edo "error"

// Database connection
$hostname = "db";
$username = "admin";
$password = "test";
$db = "database";

$conn = mysqli_connect($hostname, $username, $password, $db);
$db_available = true;
if (!$conn) {
    error_log("Database connection failed (register.php): " . mysqli_connect_error());
    $mezua = "Ezin izan da datu-basearekin konektatu";
    $mezua_type = "error";
    $db_available = false;
} else {
    mysqli_set_charset($conn, 'utf8');
}

// Jadanik ondo gorde dela jakinarazten duen GET parametroa badago, erakutsi mezua
if (isset($_GET['created']) && $_GET['created'] == '1') {
    $mezua = "Ondo gorde da!";
    $mezua_type = "success";
}

// Formularioa bidali bada, datuak prozesatzeko
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check rate limiting for registration
    if (!check_rate_limit('register', 3, 300)) {
        $mezua = "Gehiegitan saiatu zara. Mesedez, itxaron 5 minutu.";
        $mezua_type = "error";
    } elseif (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $mezua = "Segurtasun errorea. Mesedez, saiatu berriro.";
        $mezua_type = "error";
    } elseif (!$db_available) {
        $mezua = "Ezin izan da datu-basearekin konektatu";
        $mezua_type = "error";
    } else {
        // Formularioko datuak garbitu eta eskuratu
        $izena = trim(htmlspecialchars($_POST['izena'] ?? '', ENT_QUOTES, 'UTF-8'));
        $nan = trim(htmlspecialchars($_POST['nan'] ?? '', ENT_QUOTES, 'UTF-8'));
        $telefonoa = trim(htmlspecialchars($_POST['telefonoa'] ?? '', ENT_QUOTES, 'UTF-8'));
        $data = trim(htmlspecialchars($_POST['data'] ?? '', ENT_QUOTES, 'UTF-8'));
        $email = trim(htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'));
        $pasahitza = $_POST['pasahitza'] ?? '';

        // Server-side validation
        if (empty($izena) || !preg_match('/^[A-Za-zÑñ\s]{1,50}$/', $izena)) {
            $mezua = "Izen baliogabea.";
            $mezua_type = "error";
        } elseif (!validate_nan($nan)) {
            $mezua = "NAN formatua okerra.";
            $mezua_type = "error";
        } elseif (!validate_phone($telefonoa)) {
            $mezua = "Telefono zenbaki baliogabea.";
            $mezua_type = "error";
        } elseif (!validate_email($email)) {
            $mezua = "Email helbide baliogabea.";
            $mezua_type = "error";
        } elseif (strlen($pasahitza) < 8 || !preg_match('/[0-9]/', $pasahitza) || !preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $pasahitza)) {
            $mezua = "Pasahitza ez da segurua. Gutxienez 8 karaktere, zenbaki bat eta karaktere berezi bat izan behar ditu.";
            $mezua_type = "error";
        } else {
            // 1) NAN hori duen erabiltzailea jadanik badagoen egiaztatu
            $stmt = $conn->prepare("SELECT id FROM usuarios WHERE nan = ? OR email = ? LIMIT 1");
            $stmt->bind_param("ss", $nan, $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
                $mezua = "Jada badago erabiltzaile bat NAN edo email horrekin.";
                $mezua_type = "error";
            } else {
                // 2) Erabiltzailea datu-basean gorde
                $hashed_password = password_hash($pasahitza, PASSWORD_DEFAULT);
                
                $stmt_insert = $conn->prepare("INSERT INTO usuarios (nombre, nan, telefonoa, jaiotze_data, email, pasahitza) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt_insert->bind_param("ssssss", $izena, $nan, $telefonoa, $data, $email, $hashed_password);
                
                if ($stmt_insert->execute()) {
                    $stmt_insert->close();
                    header('Location: register.php?created=1');
                    exit();
                } else {
                    $mezua = "Arazo bat egon da datu-basean.";
                    error_log("Register insert error: " . $stmt_insert->error);
                    $mezua_type = "error";
                }
                $stmt_insert->close();
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
    <title>Erregistratu</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <div class="wrapper">
        <h1>Erregistratu</h1>

        <!-- Mezuak erakusteko toki berezia: formaren barruan botoien gainean agertuko da -->

        <!-- Erregistro formularioa -->
        <form id="register_form" name="register_form" method="POST">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
            <input type="text" id="izena" name="izena" placeholder="Izena" required maxlength="50">

            <input type="text" id="erabiltzaileIzena" name="nan" placeholder="NAN: 12345678-Z" required maxlength="10" pattern="[0-9]{8}-[A-Za-z]"><br>

            <input type="tel" id="telefonoa" name="telefonoa" placeholder="Telefonoa" required maxlength="9" pattern="[0-9]{9}">

            <input type="text" id="data" name="data" placeholder="Jaiotza data: YYYY-MM-DD" title="Format: YYYY-MM-DD" required maxlength="10" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}"><br>

            <input type="email" id="email" name="email" placeholder="Email" class="full-width-input" required maxlength="100"><br>

            <input type="password" id="pasahitza" name="pasahitza" placeholder="Pasahitza" required maxlength="255">

            <input type="password" id="errep_pasahitza" name="errep_pasahitza" placeholder="Errepikatu Pasahitza" required maxlength="255"><br>
            
            <!-- Formularioaren botoiak -->
            <?php if ($mezua !== ""): ?>
                <p class="<?php echo ($mezua_type === 'success') ? 'success-message' : 'error-message'; ?>">
                    <?php echo safe_output($mezua); ?>
                </p>
            <?php endif; ?>

            <div class="botoiak">
                <button type="submit" class="btn-primary full-width" id="register_submit">Erregistratu</button>
                <a href="index.php" class="btn-secondary full-width">Atzera</a> <br>
                <a href="login.php" class="btn-link">Jada baduzu kontua? Hasi Saioa</a>
            </div>
        </form>
    </div>
</body>
</html>
