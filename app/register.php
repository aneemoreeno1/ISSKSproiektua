<?php
// Datu-basearekin konexioa
$hostname = "db";
$username = "admin";
$password = "test";
$db = "database";

$conn = mysqli_connect($hostname,$username,$password,$db);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Formularioa bidali bada
$mezua = "";
if ($_POST) {
    $izena = $_POST['izena'];
    $nan = $_POST['nan'];
    $telefonoa = $_POST['telefonoa'];
    $data = $_POST['data'];
    $email = $_POST['email'];
    $pasahitza = password_hash($_POST['pasahitza'], PASSWORD_DEFAULT);
    
    // Datuak datu-basean gorde
    $sql = "INSERT INTO usuarios (nombre, nan, telefonoa, jaiotze_data, email, pasahitza) 
            VALUES ('$izena', '$nan', '$telefonoa', '$data', '$email', '$pasahitza')";
    
    if (mysqli_query($conn, $sql)) {
        $mezua = "Erregistroa eginda!";
    } else {
        $mezua = "Errorea: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Erregistratu</title>
</head>
<body>
    <h1>Erregistratu</h1>
    
    <?php if ($mezua): ?>
        <p><?php echo $mezua; ?></p>
    <?php endif; ?>
    
    <!-- Erregistro formularioa -->
    <form id="register_form" method="POST">
        Izena: <input type="text" name="izena" required><br><br>
        NAN: <input type="text" name="nan" required><br><br>
        Telefonoa: <input type="text" name="telefonoa" required><br><br>
        Jaiotze data: <input type="text" name="data" required><br><br>
        Email: <input type="text" name="email" required><br><br>
        Pasahitza: <input type="password" name="pasahitza" required><br><br>
        
        <!-- Bidaltzeko botoia -->
        <input type="submit" id="register_submit" value="Erregistratu">
    </form>

    <p><a href="/">Atzera hasierako orrira</a></p>
</body>
</html>
