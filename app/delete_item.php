<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);
session_start();

// Saio-Kontrola: Erabiltzailea autentifikatuta ez badago, saioa hasteko orrira bidali
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\'; style-src \'self\' \'unsafe-inline\'; img-src \'self\'; font-src \'self\'; connect-src \'self\'; frame-ancestors \'self\';');

// delete_item.php - Pelikula ezabatu
$hostname = "db";
$username = "admin";
$password = "test";
$db = "database";

$conn = mysqli_connect($hostname, $username, $password, $db);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Formularioa bidali bada (ezabaketa baieztatu)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['item_id'])) {
    $item_id = intval($_POST['item_id']);

    $stmt = $conn->prepare("DELETE FROM pelikulak WHERE id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $stmt->close();
    
    header("Location: items.php");
    exit;
}

// GET bidez item ID-a jaso bada (ezabaketa orria erakusteko)
if (isset($_GET['item'])) {
    $item_id = intval($_GET['item']);

    $stmt = $conn->prepare("SELECT * FROM pelikulak WHERE id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $pelikula = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <title>Ezabatu Pelikula</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <div class="wrapper">
        <h1>Ezabatu Pelikula</h1>
    
        <?php if ($pelikula): ?>
            <p><strong>ID:</strong> <?php echo $pelikula['id']; ?></p>
            <p><strong>Izena:</strong> <?php echo $pelikula['izena']; ?></p>
            <p><strong>Urtea:</strong> <?php echo $pelikula['urtea']; ?></p>
    
            <form method="post">
                <input type="hidden" name="item_id" value="<?php echo $pelikula['id']; ?>">
                <button id="item_delete_submit" type="submit" class="btn-primary">Ezabatu</button>
                <button type="button" class="btn-secondary" onclick="window.location.href='items.php'">Bueltatu</button>
            </form>
               
        <?php else: ?>
            <p>Ez da pelikularik aurkitu.</p>
        <?php endif; ?>

<?php mysqli_close($conn); ?>
</body>
</html>
