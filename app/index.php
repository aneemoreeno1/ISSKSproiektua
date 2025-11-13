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

// Session timeout - 15 minutes
$timeout = 900;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    session_unset();
    session_destroy();
}
$_SESSION['last_activity'] = time();

// Safe output function
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

// Erabiltzaile guztien datuak datu-baseatik eskuratzeko  
$query = mysqli_query($conn, "SELECT * FROM usuarios") or die(mysqli_error($conn));
?>

<!DOCTYPE html>
<html lang="eu">
<head>
    <link rel="stylesheet" href="style2.css"> <!-- lerro hau style2 fitxategiarekin konektatzeko da, eta guztietan kopiatu eta itatsi behar da -->
    <meta charset="UTF-8">
    <title>Erabiltzaileak</title>
</head>
<body>
    <div class="wrapper" > <!-- hau orri guztietan errepikatuko da, erdiko karratu txuria da, style2 orrialdean dago zehaztuta nolakoa izango den-->
        <h1>Erabiltzaileak</h1>
        <table>
            <tr>
                <th style="text-align:center; width: 20px;">ID</th> <!--Id textua erdian jartzeko erabilita, zenbakiak direnez eskuinean edo ezkerrean ez zen ongi ikusten -->
                <th>Izena</th>
                <th style="text-align:right; width: 90px;"> </th>
            </tr>

            <?php while ($row = mysqli_fetch_array($query)) { ?>
                <tr>
                    <td style="text-align:center; width: 20px;"><?= safe_output($row['id']) ?></td>
                    <td><?= safe_output($row['nombre']) ?></td>
                    <td>
                        <a href="show_user.php?user=<?= urlencode($row['id']) ?>" title="Ikusi" aria-label="Ikusi"><img src="irudiak/view.svg" alt="Ikusi" style="width:18px;height:18px;vertical-align:middle; color: #7F0001;"></a>
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['id']) { ?> <!-- erabiltzailearen saioa irekita badago orduan erabiltzaileak bere datuak editatu dezake horregatik sesion-->
                        <a href="modify_user.php?user=<?= urlencode($row['id']) ?>" title="Editatu" aria-label="Editatu"><img src="irudiak/edit.svg" alt="Editatu" style="width:18px;height:18px;vertical-align:middle; color: #7F0001;"></a>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>

        <div class="botoiak">
                <button type="button" class="btn-primary" style="width:100%" onclick="window.location.href='items.php'"><b> Pelikulak Ikusi</b></button>     <!-- hauek botoiak dira, primario sekundairio eta linkak, hauekin hierarkia mantenitzen da, erabiltzaileak botoi nagusiena erraz identifikatzen du-->
                <button type="button" class="btn-link" style="padding: 5px 2px 5px 140px;" onclick="window.location.href='login.php'"> Saioa Hasi</button> | 
                <button type="button" class="btn-link" onclick="window.location.href='register.php'">Erregistratu</button>
            </div>
        </div>
    </div>

<?php mysqli_close($conn); ?>
</body>
</html>
