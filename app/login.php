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

    $sql = "SELECT * FROM usuarios WHERE nombre = '$erabiltzailea' AND pasahitza = '$pasahitza'";
    $emaitza = mysqli_query($conn, $sql);

    if (mysqli_num_rows($emaitza) > 0) {
        $user = mysqli_fetch_assoc($emaitza);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['nombre'];

        echo "Ongi etorri " . $erabiltzailea . "!";
        echo "<p><a href='modify_user.php?user={$user['id']}'>Editatu</a></p>";
    } else {
        echo "Erabiltzailea edo pasahitza okerrak";
    }
}
?>

<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <title>Sartu</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function datuakEgiaztatu() {
            var erabiltzailea = document.login_form.erabiltzailea.value;
            var pasahitza = document.login_form.pasahitza.value;

            if (erabiltzailea.length < 1) {
                window.alert("Sartu erabiltzaile izena");
                return false;
            }

            if (pasahitza.length < 1) {
                window.alert("Sartu pasahitza");
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
            <input type="text" id="erabiltzailea" name="erabiltzailea" required><br><br>

            <label for="pasahitza">Pasahitza:</label><br>
            <input type="password" id="pasahitza" name="pasahitza" required><br><br>

            <div class="botoiak">
                <button type="submit" id="login_submit">Sartu</button>
                <button type="button" id="login_back" onclick="window.location.href='index.php'">Atzera</button>
            </div>
        </form>
    </div>
</body>
</html>
