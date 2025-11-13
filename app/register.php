<?php
// register.php - Erabiltzaile berria gehitu

// Security headers
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; object-src 'none'; base-uri 'self'; form-action 'self';");
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
    <script>
        
        // Izena letrak soilik direla egiaztatzeko funtzioa
        function bakarrikLetrak(testua) {
            return /^[A-Za-zÑñ\s]+$/.test(testua);
        }
        
        // Zenbakiak soilik direla egiaztatzeko funtzioa
        function bakarrikZenbakiak(testua) {
            return /^[0-9]+$/.test(testua);
        }
        
        // NAN-aren letra kalkulatzeko funtzioa
        function kalkulatuNanLetra(nanZenbakiak) {
            var kate = "TRWAGMYFPDXBNJZSQVHLCKET";
            var zenbakiak = parseInt(nanZenbakiak);
            var posizioa = zenbakiak % 23;
            return kate[posizioa];
        }
        
        // Erabiltzaile balidazioa bakarrik textua
        function erabiltzaileIzenaBaliozkoa(testua) {
            return /^[A-Za-z0-9_-]{3,20}$/.test(testua);
        }

        // Emailaren formatua zuzena den egiaztatzeko funtzioa
        function emailEgokia(emaila) {
            return /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/.test(emaila);
        }

        // Formularioaren datu guztiak egiaztatzeko funtzio nagusia
        function datuakEgiaztatu() {
            // Izena lortu eta egiaztatu
            var izena = document.register_form.izena.value;
            if (izena.length < 1 || !bakarrikLetrak(izena)) {
                alert("Izenak ezin du hutsik egon eta soilik letrak izan behar ditu");
                return false;
            }

            // NAN lortu eta egiaztatu
            var erabiltzaileIzena = document.register_form.erabiltzaileIzena.value;
            var nanZatiak = erabiltzaileIzena.split("-");
            if (nanZatiak.length != 2 || nanZatiak[0].length != 8 || !bakarrikZenbakiak(nanZatiak[0])) {
                alert("NAN formatua okerra. Adibidea: 12345678-Z");
                return false;
            }
            if (kalkulatuNanLetra(nanZatiak[0]).toLowerCase() != nanZatiak[1].toLowerCase()) {
                alert("NAN ez da zuzena");
                return false;
            }

            // Telefonoa lortu eta egiaztatu
            var telefonoa = document.register_form.telefonoa.value;
            if (telefonoa.length != 9 || !bakarrikZenbakiak(telefonoa)) {
                alert("Telefonoak 9 zenbaki izan behar ditu");
                return false;
            }

            // Data lortu, egiaztatu eta formatua normalizatu
            var dataField = document.register_form.data;
            var data = dataField.value;

            
            var dataZatiak = data.split("-");
            if (data.length != 10 || dataZatiak.length != 3) {
                alert("Data formatua okerra. Adibidea: 2024-12-20");
                return false;
            }
            
            // Data baliozko den egiaztatu
            var urtea = parseInt(dataZatiak[0]);
            var hilabetea = parseInt(dataZatiak[1]);
            var eguna = parseInt(dataZatiak[2]);

            if (hilabetea < 1 || hilabetea > 12) {
                alert("Hilabetea 1 eta 12 artean egon behar da");
                return false;
            }

            // Hilabete bakoitzaren egun kopurua zehaztu (bisustua kontuan hartuz)
            var egunMaximoak = [31,28,31,30,31,30,31,31,30,31,30,31];
            if ((urtea % 4 === 0 && urtea % 100 !== 0) || (urtea % 400 === 0)) {
                egunMaximoak[1] = 29;
            }
            if (eguna < 1 || eguna > egunMaximoak[hilabetea-1]) {
                alert("Eguna okerra. " + hilabetea + ". hilabeteak " + egunMaximoak[hilabetea-1] + " egun baino ez ditu izan");
                return false;
            }
            
            // Data ez dela 120 urte baino zaharragoa egiaztatu
            var gaur = new Date();
            if (urtea < gaur.getFullYear() - 120 || urtea > gaur.getFullYear()) {
                alert("Urte okerra. Ez da 120 urte baino gehiago edo etorkizuneko data izan");
                return false;
            }

            // Emaila egiaztatu
            if (!emailEgokia(document.register_form.email.value)) {
                alert("Emaila ez da zuzena");
                return false;
            }

            // Pasahitza egiaztatu
            var pasahitza = document.register_form.pasahitza.value;
            var errep_pasahitza = document.register_form.errep_pasahitza.value;
            if (pasahitza != errep_pasahitza) {
                alert("Pasahitzak ez dira berdinak.");
                return false;
            }
            
            // Pasahitzaren segurtasuna egiaztatu
            if (pasahitza.length < 8 || !/[0-9]/.test(pasahitza) || !/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(pasahitza)) {
                alert("Pasahitza ez segurua. Gutxienez 8 karaktere, zenbaki bat eta karaktere berezi bat izan behar ditu.");
                return false;
            }

            // Datu guztiak zuzenak badira, formularioa bidali
            return true;
        }
    </script>
</head>
<body>
    <div class="wrapper">
        <h1>Erregistratu</h1>

        <!-- Mezuak erakusteko toki berezia: formaren barruan botoien gainean agertuko da -->

        <!-- Erregistro formularioa -->
        <form id="register_form" name="register_form" method="POST" onsubmit="return datuakEgiaztatu()">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
            <input type="text" id="izena" name="izena" placeholder="Izena" required maxlength="50">

            <input type="text" id="erabiltzaileIzena" name="nan" placeholder="NAN: 12345678-Z" required maxlength="10" pattern="[0-9]{8}-[A-Za-z]"><br>

            <input type="tel" id="telefonoa" name="telefonoa" placeholder="Telefonoa" required maxlength="9" pattern="[0-9]{9}">

            <input type="text" id="data" name="data" placeholder="Jaiotza data: YYYY-MM-DD" title="Format: YYYY-MM-DD" required maxlength="10" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}"><br>

            <input type="email" id="email" name="email" placeholder="Email" style="width:100%" required maxlength="100"><br>

            <input type="password" id="pasahitza" name="pasahitza" placeholder="Pasahitza" required maxlength="255">

            <input type="password" id="errep_pasahitza" name="errep_pasahitza" placeholder="Errepikatu Pasahitza" required maxlength="255"><br>
            
            <!-- Formularioaren botoiak -->
            <?php if ($mezua !== ""): ?>
                <p style="text-align:center; font-size: 0.7em; margin-bottom:10px; <?php echo ($mezua_type === 'success') ? 'color: #1a6f1a;' : 'color: #7f0000ff;'; ?>">
                    <?php echo safe_output($mezua); ?>
                </p>
            <?php endif; ?>

            <div class="botoiak">
                <button type="submit" class="btn-primary" id="register_submit" style="width:100%">Erregistratu</button>
                <button type="button" class="btn-secondary" onclick="window.location.href='index.php'" style="width:100%">Atzera</button> <br>
                <button type="button" class="btn-link" onclick="window.location.href='login.php'" style="">Jada baduzu kontua? Hasi Saioa</button>
            </div>
        </form>
    </div>
</body>
</html>
