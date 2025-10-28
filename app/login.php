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
    <title>Sartu</title>
    <link rel="stylesheet" href="style2.css">
    <script>
        function datuakEgiaztatu() {
            var erabiltzailea = document.login_form.erabiltzailea.value;
            var pasahitza = document.login_form.pasahitza.value;

            if (erabiltzailea.length < 1) {
                alert("Sartu erabiltzaile izena");
                return false;
            }
            if (pasahitza.length < 1) {
                alert("Sartu pasahitza");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="wrapper">
        <h1>Sartu</h1>

        <form id="login_form" name="login_form" method="POST" onsubmit="return datuakEgiaztatu()">
            <label for="erabiltzailea">Erabiltzailea:</label><br>
            <input type="text" id="erabiltzailea" name="erabiltzailea" required> <br><br>

            <label for="pasahitza">Pasahitza:</label><br>
            <input type="password" id="pasahitza" name="pasahitza" required>

            <div class="botoiak">
                <button type="button" class="btn-primary" id="login_submit">Sartu</button>
                <button type="button" class="btn-secondary" onclick="window.location.href='index.php'">Atzera</button>
            </div>
        </form>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>
