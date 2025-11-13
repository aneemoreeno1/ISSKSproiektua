<?php
// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\'; style-src \'self\' \'unsafe-inline\'; img-src \'self\'; font-src \'self\'; connect-src \'self\'; frame-ancestors \'self\';');

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'httponly' => true, 
    'samesite' => 'Strict' // 'Strict' edo 'Lax' izan daiteke
]);

session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

if(!isset($_SESSION['user_id'])){
    http_response_code(401);
    echo "Errorea: saioa hasi behar duzu";
    exit;
}

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
$user_id=(int) $_SESSION['user_id'];

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
          <p><b>ID:</b> <?php echo $erabiltzailea['id']; ?></p>
          <p><b>Izena:</b> <?php echo $erabiltzailea['nombre']; ?></p>
          <p><b>NAN:</b> <?php echo $erabiltzailea['nan']; ?></p>
          <p><b>Telefonoa:</b> <?php echo $erabiltzailea['telefonoa']; ?></p>
          <p><b>Jaiotze data:</b> <?php echo $erabiltzailea['jaiotze_data']; ?></p>
          <p><b>Email:</b> <?php echo $erabiltzailea['email']; ?></p>
          <button class="btn-secondary" onclick="window.location.href='index.php'">Atzera</button>
      <?php else: ?>
          <p>Erabiltzailea ez da existitzen</p>
      <?php endif; ?>
  </div>
<?php mysqli_close($conn); ?>
</body>
</html>
