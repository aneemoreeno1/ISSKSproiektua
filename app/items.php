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

// Session timeout
$timeout = 900;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    session_unset();
    session_destroy();
}
$_SESSION['last_activity'] = time();

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

// Elementuak lortu
$query = mysqli_query($conn, "SELECT * FROM pelikulak") or die(mysqli_error($conn));
?>

<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <title>Pelikulak</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <div class="wrapper">
        <h1>Pelikulak</h1>

        <?php if ($query && mysqli_num_rows($query) > 0): ?>
            <table>
                <tr>
                    <th>Izena</th>
                    <th>Urtea</th>
                    <th>Generoa</th>
                    <th>Ekintzak</th>
                </tr>
                <!-- Pelikula bakoitzeko datuak erakusteko -->
                <?php while ($row = mysqli_fetch_array($query)): ?>
                    <tr>
                        <td><?= safe_output($row['izena']) ?></td>
                        <td><?= safe_output($row['urtea']) ?></td>
                        <td><?= safe_output($row['generoa']) ?></td>
                        <td>
                            <a href="show_item.php?item=<?= urlencode($row['id']) ?>" title="Ikusi" aria-label="Ikusi">Ikusi</a> 
                            <a href="modify_item.php?item=<?= urlencode($row['id']) ?>" title="Editatu" aria-label="Editatu">Editatu</a> 
                            <a href="delete_item.php?item=<?= urlencode($row['id']) ?>" title="Ezabatu" aria-label="Ezabatu" class="delete-link">Ezabatu</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p class="info-message">Ez daude pelikularik.</p>
        <?php endif; ?>

        <div class="botoiak">
            <a href="add_item.php" class="btn-primary">+</a>
            <a href="index.php" class="btn-secondary">Hasierara Bueltatu</a>
        </div>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>
