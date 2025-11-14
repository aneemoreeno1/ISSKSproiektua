<?php
// register.php - Erabiltzaile berria gehitu

// Datu-basearen konexiorako konfigurazioa
$hostname = "db";
$username = "admin";
$password = "test";
$db = "database";

$mezua = ""; // Erabiltzaileari mezuak erakusteko aldagaia
$mezua_type = ""; // "success" edo "error"

// Datu-basearekin konexioa establetzeko
$conn = mysqli_connect($hostname, $username, $password, $db);

// Handle connection failure gracefully so we can show the message in the page
$db_available = true;
if (!$conn) {
    error_log("Database connection failed (register.php): " . mysqli_connect_error());
    $mezua = "Ezin izan da datu-basearekin konektatu";
    $mezua_type = "error";
    $db_available = false;
}

// Jadanik ondo gorde dela jakinarazten duen GET parametroa badago, erakutsi mezua
if (isset($_GET['created']) && $_GET['created'] == '1') {
    $mezua = "Ondo gorde da!";
    $mezua_type = "success";
}

// Formularioa bidali bada, datuak prozesatzeko
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // If DB isn't available, avoid calling mysqli_* and show an error message
    if (!$db_available) {
        $mezua = "Ezin izan da datu-basearekin konektatu";
        $mezua_type = "error";
    } else {
        // Formularioko datuak eskuratu
        $izena = trim($_POST['izena'] ?? '');
        $nan = trim($_POST['nan'] ?? '');
        $telefonoa = trim($_POST['telefonoa'] ?? '');
        $data = trim($_POST['data'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $pasahitza = trim($_POST['pasahitza'] ?? '');

        // 1) NAN hori duen erabiltzailea jadanik badagoen egiaztatu
        if ($nan !== '') {
            $chk_stmt = mysqli_prepare($conn, "SELECT id FROM usuarios WHERE nan = ? LIMIT 1");
            mysqli_stmt_bind_param($chk_stmt, "s", $nan);
            mysqli_stmt_execute($chk_stmt);
            $chk_res = mysqli_stmt_get_result($chk_stmt);
            if ($chk_res && mysqli_num_rows($chk_res) > 0) {
                $mezua = "Jada badago erabiltzaile bat NAN horrekin (" . htmlspecialchars($nan) . ").";
                $mezua_type = "error";
            }
            mysqli_stmt_close($chk_stmt);
        }

        // 2) NAN bikoizturik ez badago, erabiltzailea datu-basean gorde
        if ($mezua === "") {
            // Hash password before storing
            $hashed_password = password_hash($pasahitza, PASSWORD_DEFAULT);
            
            $stmt = mysqli_prepare($conn, "INSERT INTO usuarios (nombre, nan, telefonoa, jaiotze_data, email, pasahitza) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssssss", $izena, $nan, $telefonoa, $data, $email, $hashed_password);
            
            // Datuak ondo gorde badira, berbideraketa egin
            if (mysqli_stmt_execute($stmt)) {
                // Use PRG pattern: redirect to self with created flag
                mysqli_stmt_close($stmt);
                header('Location: register.php?created=1');
                exit();
            } else {
                $mezua = "Arazo bat egon da datu-basean.";
                error_log("Register insert error: " . mysqli_stmt_error($stmt));
                $mezua_type = "error";
            }
            mysqli_stmt_close($stmt);
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
    <script src="js/common.js" defer></script>
    <script src="js/register.js" defer></script>
</head>
<body>
    <div class="wrapper">
        <h1>Erregistratu</h1>

        <!-- Mezuak erakusteko toki berezia: formaren barruan botoien gainean agertuko da -->

        <!-- Erregistro formularioa -->
        <form id="register_form" name="register_form" method="POST">
            <input type="text" id="izena" name="izena" placeholder="Izena" required>

            <input type="text" id="nan" name="nan" placeholder="NAN" required><br>

            <input type="tel" id="telefonoa" name="telefonoa" placeholder="Telefonoa" required>

            <input type="text" id="data" name="data" placeholder="Jaiotza data: YYYY-MM-DD" title="Format: YYYY-MM-DD" required><br>

            <input type="text" id="email" name="email" placeholder="Email" style="width:100%" required><br>

            <input type="password" id="pasahitza" name="pasahitza" placeholder="Pasahitza" required>

            <input type="password" id="errep_pasahitza" name="errep_pasahitza" placeholder="Errepikatu Pasahitza" required><br>
            
            <!-- Formularioaren botoiak -->
            <?php if ($mezua !== ""): ?>
                <p style="text-align:center; font-size: 0.7em; margin-bottom:10px; <?php echo ($mezua_type === 'success') ? 'color: #1a6f1a;' : 'color: #7f0000ff;'; ?>">
                    <?php echo htmlspecialchars($mezua); ?>
                </p>
            <?php endif; ?>

            <div class="botoiak">
                <button type="submit" class="btn-primary" id="register_submit" style="width:100%">Erregistratu</button>
                <button type="button" class="btn-secondary" data-navigate="index.php" style="width:100%">Atzera</button> <br>
                <button type="button" class="btn-link" data-navigate="login.php" style="">Jada baduzu kontua? Hasi Saioa</button>
            </div>
        </form>
    </div>
</body>
</html>
