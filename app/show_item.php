<?php
//Datu basearekin konexioa ezartzeko
$hostname = "db";
$username = "admin";
$password = "test";
$db = "database";

$conn = mysqli_connect($hostname, $username, $password, $db);
//Konekzioa ez bada ezarri, errorea bistaratu

if ($conn->connect_error) { die("Database connection failed: " . $conn->connect_error); }
//GET parametro bidez, jasotako pelikularen IDa hartu eta balidatu
if (!isset($_GET['item']) || !is_numeric($_GET['item'])) {
    die("Invalid item ID");
}
$item_id = (int)$_GET['item'];
//Pelikula horren datuak datu-basean bilatu
$stmt = mysqli_prepare($conn, "SELECT * FROM pelikulak WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $item_id);
mysqli_stmt_execute($stmt);
$emaitza = mysqli_stmt_get_result($stmt);
$pelikula = mysqli_fetch_array($emaitza);
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="eu">
<head>
<meta charset="UTF-8">
<title>Pelikularen datuak</title>
<link rel="stylesheet" href="style2.css">
<script src="js/common.js" defer></script>
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
          <button class="btn-secondary" data-back="true">Atzera</button>
      <?php else: ?>
          <p>Pelikula ez da existitzen</p>
      <?php endif; ?>
  </div>
<?php mysqli_close($conn); ?>
</body>
</html>
