<?php
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

if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
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

function check_rate_limit($action, $max_attempts = 5, $time_window = 300) {
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

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check rate limiting
    if (!check_rate_limit('login', 5, 300)) {
        $error_message = 'Gehiegitan saiatu zara. Mesedez, itxaron 5 minutu.';
    } elseif (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error_message = 'Segurtasun errorea. Mesedez, saiatu berriro.';
    } else {
        $erabiltzailea = trim(htmlspecialchars($_POST['erabiltzailea'] ?? '', ENT_QUOTES, 'UTF-8'));
        $pasahitza = $_POST['pasahitza'] ?? '';

        if (empty($erabiltzailea) || empty($pasahitza) || strlen($erabiltzailea) > 50 || strlen($pasahitza) > 255) {
            $error_message = 'Datu baliogabeak.';
        } else {
            // Parametroak prestatutako adierazpen batean erabiliz erabiltzailearen datuak lortu, pasahitz hash-a barne
            $stmt = $conn->prepare("SELECT id, nombre, pasahitza FROM usuarios WHERE nombre = ? LIMIT 1");
            $stmt->bind_param("s", $erabiltzailea);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows == 1) {
                $row = $result->fetch_assoc();
                // Pasahitza berrikusi -> password_verify()
                if (password_verify($pasahitza, $row['pasahitza'])) {
                    // Regenerate session ID on successful login
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['user_name'] = $row['nombre'];
                    $_SESSION['login_time'] = time();
                    $stmt->close();
                    header("Location: index.php");
                    exit;
                } else {
                    $error_message = 'Sartutako erabiltzailea edo pasahitza ez da zuzena';
                }
            } else {
                $error_message = 'Sartutako erabiltzailea edo pasahitza ez da zuzena';
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
    <title>Sartu</title>
    <link rel="stylesheet" href="style2.css">
    <script>
        function datuakEgiaztatu() {
            var erabiltzailea = document.login_form.erabiltzailea.value;
            var pasahitza = document.login_form.pasahitza.value;

            if (erabiltzailea.length < 1) {
                alert("Sartu erabiltzaile izena");
                return false;
            }
            if (pasahitza.length < 1) {
                alert("Sartu pasahitza");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="wrapper" style="width: 20%">
        <h1>Sartu</h1><br>
        <?php if ($error_message): ?>
            <p style="color:#ff6666; text-align:center; margin-bottom:15px;"><?= safe_output($error_message) ?></p>
        <?php endif; ?>
        
        <form id="login_form" name="login_form" method="POST" onsubmit="return datuakEgiaztatu()">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
             <!-- erabiltzaile eta pasahitz kutxak biak batera "sentitzeko" baten bottom border radius eta bestearen top border radius 0ra jarri dira eta margin top 0 ra ere bai, biak pegatuta egoteko. Bati azkenean bottom border ere kendu zaio bikoitza izan ez izatearren  -->
            <input type="text" id="erabiltzailea" name="erabiltzailea" style="width:100%; border-bottom-left-radius: 0px; border-bottom-right-radius: 0px; margin-bottom:0; box-sizing: border-box; border-bottom: 0px;" placeholder="Erabiltzailea" required maxlength="50"> <br>
            <input type="password" id="pasahitza" name="pasahitza" style="width:100%; border-top-left-radius: 0px; border-top-right-radius: 0px; margin-top: 0; box-sizing: border-box;"  placeholder="Pasahitza" required maxlength="255"><br>

            <div class="botoiak">
                <button type="submit" class="btn-primary" style="width:100%" id="login_submit">Sartu</button> <br>
                <button type="button" class="btn-secondary" style="width:100%" onclick="window.location.href='index.php'">Atzera</button> <br>
                <button type="button" class="btn-link" onclick="window.location.href='register.php'">Ez duzu konturik? Erregistratu</button>
            </div>
        </form>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>
