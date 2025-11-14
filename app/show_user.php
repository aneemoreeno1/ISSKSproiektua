<?php
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

if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

// Security functions
function safe_output($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Database connection
$hostname = "db";
$username = "admin";
$password = "test";
$db = "database";

$conn = mysqli_connect($hostname, $username, $password, $db);
if(!$conn){
    http_response_code(500);
    error_log("DB connection error: " .mysqli_connect_error());
    echo "Zerbitzari akatsa";
    exit;
}
mysqli_set_charset($conn, 'utf8');

$user_id = filter_var($_GET['user'] ?? 0, FILTER_VALIDATE_INT);
$erabiltzailea = null;

if ($user_id) {
    if ($stmt = $conn->prepare("SELECT id, nombre, nan, telefonoa, jaiotze_data, email FROM usuarios WHERE id = ? LIMIT 1")) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $erabiltzailea = $result->fetch_assoc();
        $stmt->close();
    } else {
        error_log("DB prepare failed: " . $conn->error);
        http_response_code(500);
        echo "Zerbitzari akatsa.";
        mysqli_close($conn);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="eu">
<head>
<meta charset="UTF-8">
<title>Erabiltzailearen datuak</title>
<link rel="stylesheet" href="style2.css">
</head>
<body>
  <div class="wrapper">
      <?php if ($erabiltzailea): ?>
          <h1>Erabiltzailearen datuak</h1>
          <p><b>ID:</b> <?php echo safe_output($erabiltzailea['id']); ?></p>
          <p><b>Izena:</b> <?php echo safe_output($erabiltzailea['nombre']); ?></p>
          <p><b>NAN:</b> <?php echo safe_output($erabiltzailea['nan']); ?></p>
          <p><b>Telefonoa:</b> <?php echo safe_output($erabiltzailea['telefonoa']); ?></p>
          <p><b>Jaiotze data:</b> <?php echo safe_output($erabiltzailea['jaiotze_data']); ?></p>
          <p><b>Email:</b> <?php echo safe_output($erabiltzailea['email']); ?></p>
          <a href="index.php" class="btn-secondary">Atzera</a>
      <?php else: ?>
          <p>Erabiltzailea ez da existitzen</p>
      <?php endif; ?>
  </div>
<?php mysqli_close($conn); ?>
</body>
</html>
