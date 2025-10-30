<?php
//Datu basearekin konexioa ezartzeko
$hostname = "db";
$username = "admin";
$password = "test";
$db = "database";

$conn = mysqli_connect($hostname, $username, $password, $db);
//Konekzioa ez bada ezarri, errorea bistaratu

if ($conn->connect_error) { die("Database connection failed: " . $conn->connect_error); }
//GET parametro bidez, jasotako pelikularen IDa hartu
$item_id = $_GET['item'];
//Pelikula horren datuak datu-basean bilatu
$sql = "SELECT * FROM pelikulak WHERE id = $item_id";
$emaitza = mysqli_query($conn, $sql);
$pelikula = mysqli_fetch_array($emaitza);
?>

<!DOCTYPE html>
<html lang="eu">
<head>
<meta charset="UTF-8">
<title>Pelikularen datuak</title>
<link rel="stylesheet" href="style2.css">
</head>
<body>
  <div class="wrapper">
      <?php if ($pelikula): ?>
          <h1>Pelikularen datuak</h1>
          <p><b>ID:</b> <?php echo $pelikula['id']; ?></p>
          <p><b>Izena:</b> <?php echo $pelikula['izena']; ?></p>
          <p><b>Deskribapena:</b> <?php echo $pelikula['deskribapena']; ?></p>
          <p><b>Urtea:</b> <?php echo $pelikula['urtea']; ?></p>
          <p><b>Egilea:</b> <?php echo $pelikula['egilea']; ?></p>
          <p><b>Generoa:</b> <?php echo $pelikula['generoa']; ?></p>
          <button class="btn-secondary" onclick="history.back()">Atzera</button>
      <?php else: ?>
          <p>Pelikula ez da existitzen</p>
      <?php endif; ?>
  </div>
<?php mysqli_close($conn); ?>
</body>
</html>
