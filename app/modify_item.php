<?php
// modify_item.php - Pelikularen datuak aldatu

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

function validate_year($year) {
    $current_year = date('Y');
    return is_numeric($year) && $year >= 1888 && $year <= ($current_year + 5);
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

// ID lortu GET bidez with validation
$item_id = filter_var($_GET['item'] ?? 0, FILTER_VALIDATE_INT);
if (!$item_id) {
    die("ID baliogabea.");
}

// Pelikularen datuak kargatu using prepared statement
$stmt = $conn->prepare("SELECT * FROM pelikulak WHERE id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $pelikula = $result->fetch_array();
} else {
    die("Pelikula ez da existitzen.");
}
$stmt->close();

$success_message = "";
$error_message = "";

// Formularioa bidali bada
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verify CSRF token
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error_message = "Segurtasun errorea. Mesedez, saiatu berriro.";
    } else {
        $izena = trim(htmlspecialchars($_POST['izena'] ?? '', ENT_QUOTES, 'UTF-8'));
        $deskribapena = trim(htmlspecialchars($_POST['deskribapena'] ?? '', ENT_QUOTES, 'UTF-8'));
        $urtea = filter_var($_POST['urtea'] ?? '', FILTER_VALIDATE_INT);
        $egilea = trim(htmlspecialchars($_POST['egilea'] ?? '', ENT_QUOTES, 'UTF-8'));
        $generoa = trim(htmlspecialchars($_POST['generoa'] ?? '', ENT_QUOTES, 'UTF-8'));
        
        // Server-side validation
        if (empty($izena) || strlen($izena) > 100) {
            $error_message = "Izen baliogabea.";
        } elseif (strlen($deskribapena) > 500) {
            $error_message = "Deskribapena luzeegia da.";
        } elseif ($urtea && !validate_year($urtea)) {
            $error_message = "Urte baliogabea.";
        } else {
            // Datuak eguneratu using prepared statement
            $stmt = $conn->prepare("UPDATE pelikulak SET izena = ?, deskribapena = ?, urtea = ?, egilea = ?, generoa = ? WHERE id = ?");
            $stmt->bind_param("ssissi", $izena, $deskribapena, $urtea, $egilea, $generoa, $item_id);
            
            if ($stmt->execute() && $stmt->affected_rows > 0) {
                $success_message = "Datuak eguneratuak!";
                // Datuak berriro kargatu
                $stmt_reload = $conn->prepare("SELECT * FROM pelikulak WHERE id = ?");
                $stmt_reload->bind_param("i", $item_id);
                $stmt_reload->execute();
                $result = $stmt_reload->get_result();
                $pelikula = $result->fetch_array();
                $stmt_reload->close();
            } else {
                $error_message = "Arazo bat egon da datuak eguneratzean.";
                error_log("Modify item error: " . $stmt->error);
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
    <title>Pelikularen datuak aldatu</title>
    <link rel="stylesheet" href="style2.css">
    <script>
        function bakarrikLetrak(testua) {
            var patroia = /^[A-Za-zÑñ\s]+$/;
            return patroia.test(testua);
        }

        function bakarrikZenbakiak(testua) {
            var patroia = /^[0-9]+$/;
            return patroia.test(testua);
        }

        function gutxienezLetraBat(testua) {
            var patroia = /[A-Za-zÑñ]/;
            return patroia.test(testua);
        }

        function karaktereArruntaK(testua) {
            var patroia = /^[A-Za-zÑñ0-9\s.,!?¡¿()-]+$/;
            return patroia.test(testua);
        }

        function datuakEgiaztatu() {
            // Izena
            var izena = document.item_modify_form.izena.value;
            if (izena.length < 1) {
                alert("Izenak ezin du hutsik egon");
                return false;
            } else if (!karaktereArruntaK(izena)) {
                alert("Izenak soilik letrak, zenbakiak eta karaktere arruntak izan behar ditu");
                return false;
            }

            // Deskribapena
            var deskribapena = document.item_modify_form.deskribapena.value;
            if (deskribapena.length > 500) {
                alert("Deskribapenak ezin du 500 karaktere baino gehiago izan");
                return false;
            }

            // Urtea
            var urtea = document.item_modify_form.urtea.value;
            if (urtea !== "") {
                if (!bakarrikZenbakiak(urtea)) {
                    alert("Urteak zenbaki osoa izan behar du");
                    return false;
                }
                var urteZenbakia = parseInt(urtea);
                var gaurkoUrtea = new Date().getFullYear();
                if (urteZenbakia < 1888) {
                    alert("Urtea ez da egokia. 1888 baino handiagoa izan behar da");
                    return false;
                }
                if (urteZenbakia > gaurkoUrtea + 5) {
                    alert("Urtea ez da egokia. Ezin da etorkizuneko 5 urte baino gehiago izan");
                    return false;
                }
            }

            // Egilea
            var egilea = document.item_modify_form.egilea.value;
            if (egilea !== "") {
                if (!karaktereArruntaK(egilea)) {
                    alert("Egileak soilik letrak, zenbakiak eta karaktere arruntak izan behar ditu");
                    return false;
                } else if (!gutxienezLetraBat(egilea)) {
                    alert("Egileak gutxienez letra bat izan behar du");
                    return false;
                }
            }

            // Generoa
            var generoa = document.item_modify_form.generoa.value;
            if (generoa !== "") {
                if (!bakarrikLetrak(generoa)) {
                    alert("Generoak soilik letrak izan behar ditu");
                    return false;
                } else if (!gutxienezLetraBat(generoa)) {
                    alert("Generoak gutxienez letra bat izan behar du");
                    return false;
                }
            }

            return true;
        }
    </script>
</head>
<body>
    <div class="wrapper">
        <h1>Pelikularen datuak aldatu</h1>
        
        <?php if ($success_message): ?>
            <p style="color:#66ff66; text-align:center; margin-bottom:15px;"><?= safe_output($success_message) ?></p>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <p style="color:#ff6666; text-align:center; margin-bottom:15px;"><?= safe_output($error_message) ?></p>
        <?php endif; ?>

        <form id="item_modify_form" name="item_modify_form" method="POST" onsubmit="return datuakEgiaztatu()">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
            <div class="form-grid">
                <div>
                    <label for="izena">Izena:</label><br>
                    <input type="text" id="izena" name="izena" value="<?php echo safe_output($pelikula['izena']); ?>" required maxlength="100">
                </div>

                <div>
                    <label for="urtea">Urtea:</label><br>
                    <input type="number" id="urtea" name="urtea" value="<?php echo safe_output($pelikula['urtea']); ?>" min="1888" max="<?php echo date('Y') + 5; ?>">
                </div>

                <div>
                    <label for="egilea">Egilea:</label><br>
                    <input type="text" id="egilea" name="egilea" value="<?php echo safe_output($pelikula['egilea']); ?>" maxlength="100">
                </div>

                <div>
                    <label for="generoa">Generoa:</label><br>
                    <input type="text" id="generoa" name="generoa" value="<?php echo safe_output($pelikula['generoa']); ?>" maxlength="50">
                </div>
            </div>

            <div class="full-width" style="margin-top:12px;">
                <label for="deskribapena">Deskribapena:</label><br>
                <textarea id="deskribapena" name="deskribapena" rows="4" style="width: 100%; font-family: 'Segoe UI';" maxlength="500"><?php echo safe_output($pelikula['deskribapena']); ?></textarea>
            </div>

            <div class="botoiak" style="margin-top:18px;">
                <button type="submit" id="item_modify_submit" class="btn-primary">Datuak gorde</button>
                <button type="button" id="items_back" class="btn-secondary" onclick="window.location.href='items.php'">Atzera</button>
            </div>
        </form>
    </div>
</body>
</html>
