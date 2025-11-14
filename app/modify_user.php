<?php
// modify_user.php - Erabiltzailearen datuak aldatzeko orria

//Saioa hasi
require_once 'includes/session.php';

// Datu-basearekin konexioa egiteko konfigurazioa
$hostname = "db";
$username = "admin";
$password = "test";
$db = "database";

// Datu-basearekin konexioa establetzeko
$conn = mysqli_connect($hostname, $username, $password, $db);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Erabiltzailea saioa hasita dagoen egiaztatu
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Erabiltzailearen IDa lortu
$user_id = $_SESSION['user_id'];

// Datu-baseatik erabiltzailearen datuak kargatu 
$stmt = mysqli_prepare($conn, "SELECT * FROM usuarios WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$emaitza = mysqli_stmt_get_result($stmt);

// Erabiltzailea existitzen dela egiaztatu
if ($emaitza->num_rows > 0) {
    $erabiltzailea = mysqli_fetch_array($emaitza);
    mysqli_stmt_close($stmt);
} else {
    mysqli_stmt_close($stmt);
    die("Erabiltzailea ez da existitzen. Lehenengo erabiltzailea sortu.");
}

// Formularioa bidali bada, datuak prozesatu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Formulariotik datuak eskuratu
    $izena = $_POST['izena'];
    $nan = $_POST['nan'];
    $telefonoa = $_POST['telefonoa'];
    $data = $_POST['data'];
    $email = $_POST['email'];
    $pasahitza = $_POST['pasahitza']; 
    
    // NAN ezin da aldatu, beraz datu-basean eguneraketa egiteko kontsulta
    $stmt = mysqli_prepare($conn, "UPDATE usuarios SET nombre = ?, telefonoa = ?, jaiotze_data = ?, email = ?, pasahitza = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "sssssi", $izena, $telefonoa, $data, $email, $pasahitza, $user_id);
    
    // Kontsulta exekutatu eta emaitza egiaztatu
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        // Datuak berriro kargatu formularioan erakusteko
        $stmt2 = mysqli_prepare($conn, "SELECT * FROM usuarios WHERE id = ?");
        mysqli_stmt_bind_param($stmt2, "i", $user_id);
        mysqli_stmt_execute($stmt2);
        $emaitza = mysqli_stmt_get_result($stmt2);
        $erabiltzailea = mysqli_fetch_array($emaitza);
        mysqli_stmt_close($stmt2);
    } else {
        echo "Errorea: " . mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <title>Erabiltzailearen datuak aldatu</title>
    <link rel="stylesheet" href="style2.css">
    <script src="js/common.js" defer></script>
    <script src="js/user.js" defer></script>
</head>
<body>
    <div class="wrapper">
        <h1>Erabiltzailearen datuak aldatu</h1>
        
        <!-- Erabiltzailearen datuak aldatzeko formularioa -->
        <form id="user_modify_form" name="user_modify_form" method="POST">
            <label for="izena">Izena:</label><br>
            <input type="text" name="izena" style="width: 100%;" value="<?= $erabiltzailea['nombre'] ?>" required>

            <label for="nan">NAN:</label><br>
            <!-- Mostrar el NAN como texto (no editable) y añadir un input hidden para preservar el valor en el POST -->
            <div style="width: 100%; padding:8px; background:#f5f5f5; border-radius:8px; box-sizing:border-box;"><?= htmlspecialchars($erabiltzailea['nan']) ?></div>
            <input type="hidden" name="nan" value="<?= htmlspecialchars($erabiltzailea['nan']) ?>">

            <label for="telefonoa">Telefonoa:</label><br>
            <input type="text" name="telefonoa" style="width: 100%;" value="<?= $erabiltzailea['telefonoa'] ?>" required>

            <label for="data">Jaiotze data:</label><br>
            <input type="text" name="data" style="width: 100%;" value="<?= $erabiltzailea['jaiotze_data'] ?>" required>

            <label for="email">Email:</label><br>
            <input type="text" name="email" style="width: 100%;" value="<?= $erabiltzailea['email'] ?>" required>

            <label for="pasahitza">Pasahitza:</label><br>
            <input type="password" name="pasahitza" style="width: 100%;" value="<?= $erabiltzailea['pasahitza'] ?>" required>

            <label for="errep_pasahitza">Errepikatu pasahitza:</label><br>
            <input type="password" name="errep_pasahitza" style="width: 100%;" value="<?= $erabiltzailea['pasahitza'] ?>" required>
            
            <div class="botoiak">
                <button type="submit" class="btn-primary" id="user_modify_submit">Datuak gorde</button>
                <button type="button" class="btn-secondary" data-navigate="index.php">Atzera</button>
            </div>
        </form>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>
