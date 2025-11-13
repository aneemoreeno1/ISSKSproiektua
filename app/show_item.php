<?php
// Comprehensive security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
header('Cross-Origin-Opener-Policy: same-origin');
header('Cross-Origin-Embedder-Policy: require-corp');
header('Cross-Origin-Resource-Policy: same-origin');
header('Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=(), usb=(), magnetometer=(), gyroscope=(), speaker=(), vibrate=(), fullscreen=(self), sync-xhr=()');
header('Content-Security-Policy: default-src \'self\'; script-src \'self\'; style-src \'self\'; img-src \'self\'; font-src \'self\'; connect-src \'self\'; media-src \'self\'; object-src \'none\'; child-src \'self\'; frame-src \'self\'; worker-src \'self\'; frame-ancestors \'self\'; form-action \'self\'; base-uri \'self\'; manifest-src \'self\'');

$hostname = "db";
$username = "admin";
$password = "test";
$db = "database";

$conn = mysqli_connect($hostname, $username, $password, $db);
if ($conn->connect_error) { die("Database connection failed: " . $conn->connect_error); }

$item_id = intval($_GET['item']);
$stmt = $conn->prepare("SELECT * FROM pelikulak WHERE id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();
$pelikula = $result->fetch_array();
$stmt->close();
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
