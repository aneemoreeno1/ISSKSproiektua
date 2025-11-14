<?php
//____________________________Pelikula berria gehitu_________________________________________________
// Remove server information
header("Server: ");
header_remove("X-Powered-By");

// Simple session start
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

$mezua = ""; // Mezua erakusteko textu hutsa (aldagaia)
$mezua_type = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verify CSRF token
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $mezua = "Segurtasun errorea. Mesedez, saiatu berriro.";
        $mezua_type = "error";
    } else {
        $izena = trim(htmlspecialchars($_POST['izena'] ?? '', ENT_QUOTES, 'UTF-8'));
        $deskribapena = trim(htmlspecialchars($_POST['deskribapena'] ?? '', ENT_QUOTES, 'UTF-8'));
        $urtea = filter_var($_POST['urtea'] ?? '', FILTER_VALIDATE_INT);
        $egilea = trim(htmlspecialchars($_POST['egilea'] ?? '', ENT_QUOTES, 'UTF-8'));
        $generoa = trim(htmlspecialchars($_POST['generoa'] ?? '', ENT_QUOTES, 'UTF-8'));

        // Server-side validation
        if (empty($izena) || strlen($izena) > 100) {
            $mezua = "Izen baliogabea.";
            $mezua_type = "error";
        } elseif (strlen($deskribapena) > 500) {
            $mezua = "Deskribapena luzeegia da.";
            $mezua_type = "error";
        } elseif ($urtea && !validate_year($urtea)) {
            $mezua = "Urte baliogabea.";
            $mezua_type = "error";
        } else {
            // Use prepared statement to prevent SQL injection
            $stmt = $conn->prepare("INSERT INTO pelikulak (izena, deskribapena, urtea, egilea, generoa) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssiss", $izena, $deskribapena, $urtea, $egilea, $generoa);
            
            if ($stmt->execute()) {
                $mezua = "Pelikula ondo gehitu da!";
                $mezua_type = "success";
            } else {
                $mezua = "Arazo bat egon da datuak gordetzean.";
                $mezua_type = "error";
                error_log("Add item error: " . $stmt->error);
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
    <title>Pelikula berria gehitu</title>
    <link rel="stylesheet" href="style2.css">

</head>
<body>
    <div class="wrapper">
        <h1>Pelikula berria gehitu</h1>

        <?php if ($mezua !== ""): ?>
            <p class="<?php echo ($mezua_type === 'success') ? 'user-success' : 'user-error'; ?> message-large">
                <?php echo safe_output($mezua); ?>
            </p>
        <?php endif; ?>

        <form id="item_add_form" name="item_add_form" method="POST">
            <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
            <div class="form-grid">
                <div>
                    <label for="izena">Izena:</label><br>
                    <input type="text" name="izena" class="full-width-input" required maxlength="100">
                </div>             

                <div>
                    <label for="urtea">Urtea:</label><br>
                    <input type="number" name="urtea" class="full-width-input" min="1888" max="<?php echo date('Y') + 5; ?>" value="<?php echo date('Y'); ?>">
                </div>

                <div>
                    <label for="egilea">Egilea:</label><br>
                    <input type="text" name="egilea" class="full-width-input" maxlength="100">
                </div>

                <div>
                    <label for="generoa">Generoa:</label><br>
                    <input type="text" name="generoa" class="full-width-input" maxlength="50">
                </div>
            </div>

            <div class="full-width">
                    <label for="deskribapena">Deskribapena:</label><br>
                    <textarea name="deskribapena" rows="4" class="full-width-input" maxlength="500"></textarea>
                </div>

            <div class="botoiak button-margin-top">
                <button id="item_add_submit" type="submit" class="btn-primary">Gehitu</button>
                <a href="items.php" class="btn-secondary">Atzera</a>
            </div>
        </form>
    </div>
</body>
</html>
