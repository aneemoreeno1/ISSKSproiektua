<?php
//Datu-basearekin konekxioa ezartzeko
require_once 'includes/session.php';
$hostname = "db";
$username = "admin";
$password = "test";
$db = "database";

$conn = mysqli_connect($hostname, $username, $password, $db);
//Konekzioa ez bada ezarri, errorea bistaratu
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //Erabiltzailearen sarrrera-datuak eskuratu   
    $erabiltzailea = $_POST['erabiltzailea'];
    $pasahitza = $_POST['pasahitza'];
    //Erabiltzailea eta pasahitza datu-basean bilatu
    $stmt = mysqli_prepare($conn, "SELECT * FROM usuarios WHERE nombre=? AND pasahitza=?");
    mysqli_stmt_bind_param($stmt, "ss", $erabiltzailea, $pasahitza);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
//Egiaztatu erabiltzailea existitzen den eta saioa hasi
    if ($result && mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['user_name'] = $row['nombre'];
        header("Location: index.php");
        exit;
    } else {
        //errore kasuan, mezua erakutsi
        echo "<p style='color:#ff6666; text-align:center; margin-bottom:15px;'>Sartutako erabiltzailea edo pasahitza ez da zuzena</p>";
    }
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <title>Sartu</title>
    <link rel="stylesheet" href="style2.css">
    <script src="js/common.js" defer></script>
    <script src="js/login.js" defer></script>
</head>
<body>
    <div class="wrapper" style="width: 20%">
        <h1>Sartu</h1><br>
        <form id="login_form" name="login_form" method="POST">
            <input type="text" id="erabiltzailea" name="erabiltzailea" style="width:100%; border-bottom-left-radius: 0px; border-bottom-right-radius: 0px; margin-bottom:0; box-sizing: border-box; border-bottom: 0px;" placeholder="Erabiltzailea" required> <br>
            <input type="password" id="pasahitza" name="pasahitza" style="width:100%; border-top-left-radius: 0px; border-top-right-radius: 0px; margin-top: 0; box-sizing: border-box;"  placeholder="Pasahitza" required><br>

            <div class="botoiak">
                <button type="submit" class="btn-primary" style="width:100%" id="login_submit">Sartu</button> <br>
                <button type="button" class="btn-secondary" style="width:100%" data-navigate="index.php">Atzera</button> <br>
                <button type="button" class="btn-link" data-navigate="register.php">Ez duzu konturik? Erregistratu</button>
            </div>
        </form>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>
