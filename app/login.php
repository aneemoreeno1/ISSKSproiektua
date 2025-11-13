<?php
session_set_cookie_params( [
   'lifetime' => 0,        
   'path' => '/',
   'secure' => true,       
   'httponly' => true,     
   'samesite' => 'Strict'
]);

session_start();

if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

$timeout = 60;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=1");
    exit;
}

$_SESSION['last_activity'] = time();

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

    // Parametroak prestatutako adierazpen batean erabiliz erabiltzailearen datuak lortu, pasahitz hash-a barne
    $stmt = $conn->prepare("SELECT id, nombre, pasahitza FROM usuarios WHERE nombre = ?");
    $stmt->bind_param("s", $erabiltzailea);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows == 1) {
        $row = $result->fetch_assoc();
        // Pasahitza berrikusi -> password_verify()
        if (password_verify($pasahitza, $row['pasahitza'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['nombre'];
            $stmt->close();
            header("Location: index.php");
            exit;
        } else {
            echo "<p style='color:#ff6666; text-align:center; margin-bottom:15px;'>Sartutako erabiltzailea edo pasahitza ez da zuzena</p>";
        }
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
