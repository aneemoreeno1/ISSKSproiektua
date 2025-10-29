<?php
session_start();
$hostname = "db";
$username = "admin";
$password = "test";
$db = "database";

$conn = mysqli_connect($hostname, $username, $password, $db);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $erabiltzailea = $_POST['erabiltzailea'];
    $pasahitza = $_POST['pasahitza'];

    $sql = "SELECT * FROM usuarios WHERE nombre='$erabiltzailea' AND pasahitza='$pasahitza'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['user_name'] = $row['nombre'];
        header("Location: index.php");
        exit;
    } else {
        echo "<p style='color:#ff6666; text-align:center; margin-bottom:15px;'>Usuario o contraseña incorrectos</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <title>Saioa hasi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="wrapper" style="width: 20%">
        <h1>Sartu</h1><br>

        <?php
        if (isset($mezua) && $mezua !== "") {
            echo "<p style='text-align:center; font-weight:bold; font-size:1.1em; color:#66ff66;'>$mezua</p>";
        }
        ?>

        <form id="login_form" name="login_form" method="POST" onsubmit="return datuakEgiaztatu()">
            <input type="text" id="erabiltzailea" name="erabiltzailea"
                style="width:100%; border-bottom-left-radius: 0px; border-bottom-right-radius: 0px;
                margin-bottom:0; box-sizing: border-box; border-bottom: 0px;"
                placeholder="Erabiltzailea" required><br>

            <input type="password" id="pasahitza" name="pasahitza"
                style="width:100%; border-top-left-radius: 0px; border-top-right-radius: 0px;
                margin-top: 0; box-sizing: border-box;"
                placeholder="Pasahitza" required><br>

            <div class="botoiak">
                <button type="submit" class="btn-primary" style="width:100%" id="login_submit">Sartu</button><br>
                <button type="button" class="btn-secondary" style="width:100%" onclick="window.location.href='index.php'">Atzera</button><br>
                <button type="button" class="btn-link" onclick="window.location.href='register.php'">Ez duzu konturik? Erregistratu</button>
            </div>
        </form>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>
