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

// Basic session start for user authentication check (without complex security settings)
session_start();

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
                <th class="text-center id-column">ID</th> <!--Id textua erdian jartzeko erabilita, zenbakiak direnez eskuinean edo ezkerrean ez zen ongi ikusten -->
                <th>Izena</th>
                <th class="text-right actions-column"> </th>
            </tr>

            <?php while ($row = mysqli_fetch_array($query)) { ?>
                <tr>
                    <td class="text-center id-column"><?= safe_output($row['id']) ?></td>
                    <td><?= safe_output($row['nombre']) ?></td>
                    <td>
                        <a href="show_user.php?user=<?= urlencode($row['id']) ?>" title="Ikusi" aria-label="Ikusi">ikusi</a>
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['id']) { ?> <!-- erabiltzailearen saioa irekita badago orduan erabiltzaileak bere datuak editatu dezake horregatik sesion-->
                        <a href="modify_user.php?user=<?= urlencode($row['id']) ?>" title="Editatu" aria-label="Editatu">Editatu</a>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>

        <div class="botoiak">
                <a href="items.php" class="btn-primary full-width"><b> Pelikulak Ikusi</b></a>     <!-- hauek botoiak dira, primario sekundairio eta linkak, hauekin hierarkia mantenitzen da, erabiltzaileak botoi nagusiena erraz identifikatzen du-->
                <a href="login.php" class="btn-link login-padding"> Saioa Hasi</a> | 
                <a href="register.php" class="btn-link">Erregistratu</a>
            </div>
        </div>
    </div>

<?php mysqli_close($conn); ?>
</body>
</html>
