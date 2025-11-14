<?php
// delete_item.php - Pelikula ezabatu

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

// Formularioa bidali bada (ezabaketa baieztatu)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['item_id'])) {
    // Verify CSRF token
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("Segurtasun errorea.");
    }
    
    $item_id = filter_var($_POST['item_id'], FILTER_VALIDATE_INT);
    
    if ($item_id) {
        $stmt = $conn->prepare("DELETE FROM pelikulak WHERE id = ?");
        $stmt->bind_param("i", $item_id);
        if ($stmt->execute()) {
            $stmt->close();
            header("Location: items.php");
            exit;
        } else {
            error_log("Delete item error: " . $stmt->error);
            die("Errorea pelikula ezabatzean.");
        }
    } else {
        die("ID baliogabea.");
    }
}

// GET bidez item ID-a jaso bada (ezabaketa orria erakusteko)
$pelikula = null;
if (isset($_GET['item'])) {
    $item_id = filter_var($_GET['item'], FILTER_VALIDATE_INT);
    
    if ($item_id) {
        $stmt = $conn->prepare("SELECT * FROM pelikulak WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $pelikula = $result->fetch_assoc();
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <title>Ezabatu Pelikula</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <div class="wrapper">
        <h1>Ezabatu Pelikula</h1>
    
        <?php if ($pelikula): ?>
            <p><strong>ID:</strong> <?php echo safe_output($pelikula['id']); ?></p>
            <p><strong>Izena:</strong> <?php echo safe_output($pelikula['izena']); ?></p>
            <p><strong>Urtea:</strong> <?php echo safe_output($pelikula['urtea']); ?></p>
    
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                <input type="hidden" name="item_id" value="<?php echo safe_output($pelikula['id']); ?>">
                <button id="item_delete_submit" type="submit" class="btn-primary">Ezabatu</button>
                <a href="items.php" class="btn-secondary">Bueltatu</a>
            </form>
               
        <?php else: ?>
            <p>Ez da pelikularik aurkitu.</p>
        <?php endif; ?>

<?php mysqli_close($conn); ?>
</body>
</html>
