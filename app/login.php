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

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, nombre FROM usuarios WHERE nombre = ? AND pasahitza = ?");
    $stmt->bind_param("ss", $erabiltzailea, $pasahitza);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['user_name'] = $row['nombre'];
        $stmt->close();
        header("Location: index.php");
        exit;
    } else {
        echo "<p style='color:#ff6666; text-align:center; margin-bottom:15px;'>Sartutako erabiltzailea edo pasahitza ez da zuzena</p>";
    }
    $stmt->close();
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
    <div class="wrapper" style="width: 20%">
        <h1>Sartu</h1><br>
        <form id="login_form" name="login_form" method="POST" onsubmit="return datuakEgiaztatu()">
             <!-- erabiltzaile eta pasahitz kutxak biak batera "sentitzeko" baten bottom border radius eta bestearen top border radius 0ra jarri dira eta margin top 0 ra ere bai, biak pegatuta egoteko. Bati azkenean bottom border ere kendu zaio bikoitza izan ez izatearren  -->
            <input type="text" id="erabiltzailea" name="erabiltzailea" style="width:100%; border-bottom-left-radius: 0px; border-bottom-right-radius: 0px; margin-bottom:0; box-sizing: border-box; border-bottom: 0px;" placeholder="Erabiltzailea" required> <br>
            <input type="password" id="pasahitza" name="pasahitza" style="width:100%; border-top-left-radius: 0px; border-top-right-radius: 0px; margin-top: 0; box-sizing: border-box;"  placeholder="Pasahitza" required><br>

            <div class="botoiak">
                <button type="submit" class="btn-primary" style="width:100%" id="login_submit">Sartu</button> <br>
                <button type="button" class="btn-secondary" style="width:100%" onclick="window.location.href='index.php'">Atzera</button> <br>
                <button type="button" class="btn-link" onclick="window.location.href='register.php'">Ez duzu konturik? Erregistratu</button>
            </div>
        </form>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>
