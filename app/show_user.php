<?php
// Security function for XSS protection
function safe_output($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Database connection
$hostname = "db";
$username = "admin";
$password = "test";
$db = "database";

$conn = mysqli_connect($hostname, $username, $password, $db);
if(!$conn){
    http_response_code(500);
    error_log("DB connection error: " .mysqli_connect_error());
    echo "Zerbitzari akatsa";
    exit;
}

$user_id = $_GET['user'];

if ($stmt = $conn->prepare("SELECT id, nombre, nan, telefonoa, jaiotze_data, email FROM usuarios WHERE id = ? LIMIT 1")) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $erabiltzailea = $result->fetch_assoc();
    $stmt->close();
} else {
    error_log("DB prepare failed: " . $conn->error);
    http_response_code(500);
    echo "Zerbitzari akatsa.";
    mysqli_close($conn);
    exit;
}
?>

<!DOCTYPE html>
<html lang="eu">
<head>
<meta charset="UTF-8">
<title>Erabiltzailearen datuak</title>
<link rel="stylesheet" href="style2.css">
</head>
<body>
  <div class="wrapper">
      <?php if ($erabiltzailea): ?>
          <h1>Erabiltzailearen datuak</h1>
          <p><b>ID:</b> <?php echo safe_output($erabiltzailea['id']); ?></p>
          <p><b>Izena:</b> <?php echo safe_output($erabiltzailea['nombre']); ?></p>
          <p><b>NAN:</b> <?php echo safe_output($erabiltzailea['nan']); ?></p>
          <p><b>Telefonoa:</b> <?php echo safe_output($erabiltzailea['telefonoa']); ?></p>
          <p><b>Jaiotze data:</b> <?php echo safe_output($erabiltzailea['jaiotze_data']); ?></p>
          <p><b>Email:</b> <?php echo safe_output($erabiltzailea['email']); ?></p>
          <a href="index.php" class="btn-secondary">Atzera</a>
      <?php else: ?>
          <p>Erabiltzailea ez da existitzen</p>
      <?php endif; ?>
  </div>
<?php mysqli_close($conn); ?>
</body>
</html>
