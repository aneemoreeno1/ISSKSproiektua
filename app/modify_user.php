<?php
// modify_user.php - Erabiltzailearen datuak aldatzeko orria

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

// Force session cookie regeneration with all security attributes
if (session_id()) {
    // Delete any existing session cookie first
    setcookie(session_name(), '', time() - 3600, '/', '', true, true);
    
    // Set new session cookie with all security attributes
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
</head>
<body>
    <div class="wrapper">
        <h1>Erabiltzailearen datuak aldatu</h1>
        
        <?php if ($success_message): ?>
            <p class="user-success"><?= safe_output($success_message) ?></p>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <p class="user-error"><?= safe_output($error_message) ?></p>
        <?php endif; ?>
        
        <!-- Erabiltzailearen datuak aldatzeko formularioa -->
        <form id="user_modify_form" name="user_modify_form" method="POST">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
            <label for="izena">Izena:</label><br>
            <input type="text" name="izena" class="full-width-input" value="<?= safe_output($erabiltzailea['nombre']) ?>" required maxlength="50">

            <label for="nan">NAN:</label><br>
            <!-- Mostrar el NAN como texto (no editable) y añadir un input hidden para preservar el valor en el POST -->
            <div class="readonly-field"><?= safe_output($erabiltzailea['nan']) ?></div>
            <input type="hidden" name="nan" value="<?= safe_output($erabiltzailea['nan']) ?>">

            <label for="telefonoa">Telefonoa:</label><br>
            <input type="tel" name="telefonoa" class="full-width-input" value="<?= safe_output($erabiltzailea['telefonoa']) ?>" required maxlength="9" pattern="[0-9]{9}">

            <label for="data">Jaiotze data:</label><br>
            <input type="text" name="data" class="full-width-input" value="<?= safe_output($erabiltzailea['jaiotze_data']) ?>" required maxlength="10" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}">

            <label for="email">Email:</label><br>
            <input type="email" name="email" class="full-width-input" value="<?= safe_output($erabiltzailea['email']) ?>" required maxlength="100">

            <label for="pasahitza">Pasahitza berria:</label><br>
            <input type="password" name="pasahitza" class="full-width-input" placeholder="Sartu pasahitza berria" required maxlength="255">

            <label for="errep_pasahitza">Errepikatu pasahitza berria:</label><br>
            <input type="password" name="errep_pasahitza" class="full-width-input" placeholder="Errepikatu pasahitza berria" required maxlength="255">
            
            <div class="botoiak">
                <button type="submit" class="btn-primary" id="user_modify_submit">Datuak gorde</button>
                <a href="index.php" class="btn-secondary">Atzera</a>
            </div>
        </form>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>
