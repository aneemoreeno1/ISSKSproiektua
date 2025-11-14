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
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("Database connection error");
}
mysqli_set_charset($conn, 'utf8');

$item_id = filter_var($_GET['item'] ?? 0, FILTER_VALIDATE_INT);
$pelikula = null;

if ($item_id) {
    $stmt = $conn->prepare("SELECT * FROM pelikulak WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $pelikula = $result->fetch_array();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="eu">
<head>
<meta charset="UTF-8">
<title>Pelikularen datuak</title>
<link rel="stylesheet" href="style2.css">
</head>
<body>
  <div class="wrapper">
      <?php if ($pelikula): ?>
          <h1>Pelikularen datuak</h1>
          <p><b>ID:</b> <?php echo safe_output($pelikula['id']); ?></p>
          <p><b>Izena:</b> <?php echo safe_output($pelikula['izena']); ?></p>
          <p><b>Deskribapena:</b> <?php echo safe_output($pelikula['deskribapena']); ?></p>
          <p><b>Urtea:</b> <?php echo safe_output($pelikula['urtea']); ?></p>
          <p><b>Egilea:</b> <?php echo safe_output($pelikula['egilea']); ?></p>
          <p><b>Generoa:</b> <?php echo safe_output($pelikula['generoa']); ?></p>
          <a href=\"items.php\" class=\"btn-secondary\">Atzera</a>
      <?php else: ?>
          <p>Pelikula ez da existitzen</p>
      <?php endif; ?>
  </div>
<?php mysqli_close($conn); ?>
</body>
</html>
