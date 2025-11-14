<?php
//Datu basearekin konexioa ezartzeko
$hostname = "db";
$username = "admin";
$password = "test";
$db = "database";

$conn = mysqli_connect($hostname, $username, $password, $db);
//Konekzioa ez bada ezarri, errorea bistaratu
if ($conn->connect_error) { die("Database connection failed: " . $conn->connect_error); }

//user ID lortu GET bidez
$user_id = $_GET['user'];

// Erabiltzailearen datuak kargatu
$stmt = mysqli_prepare($conn, "SELECT * FROM usuarios WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$emaitza = mysqli_stmt_get_result($stmt);
$erabiltzailea = mysqli_fetch_array($emaitza);
mysqli_stmt_close($stmt);
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
