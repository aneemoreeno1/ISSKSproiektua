<?php
$hostname = "db";
$username = "admin";
$password = "test";
$db = "database";

$conn = mysqli_connect($hostname, $username, $password, $db);
if ($conn->connect_error) { die("Database connection failed: " . $conn->connect_error); }

$user_id = $_GET['user'];
$sql = "SELECT * FROM usuarios WHERE id = $user_id";
$emaitza = mysqli_query($conn, $sql);
$erabiltzailea = mysqli_fetch_array($emaitza);
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
