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

$hostname = "db";
$username = "admin";
$password = "test";
$db = "database";

$conn = mysqli_connect($hostname, $username, $password, $db);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Elementuak lortu
$query = mysqli_query($conn, "SELECT * FROM pelikulak") or die(mysqli_error($conn));
?>

<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <title>Pelikulak</title>
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <div class="wrapper">
        <h1>Pelikulak</h1>

        <?php if ($query && mysqli_num_rows($query) > 0): ?>
            <table>
                <tr>
                    <th>Izena</th>
                    <th>Urtea</th>
                    <th>Generoa</th>
                    <th>Ekintzak</th>
                </tr>
                <!-- Pelikula bakoitzeko datuak erakusteko -->
                <?php while ($row = mysqli_fetch_array($query)): ?>
                    <tr>
                        <td><?= $row['izena'] ?></td>
                        <td><?= $row['urtea'] ?></td>
                        <td><?= $row['generoa'] ?></td>
                        <td>
                            <a href="show_item.php?item=<?= $row['id'] ?>" title="Ikusi" aria-label="Ikusi"><img src="irudiak/view.svg" alt="Ikusi" style="width:18px;height:18px;vertical-align:middle; color: #7F0001;"></a> 
                            <a href="modify_item.php?item=<?= $row['id'] ?>" title="Editatu" aria-label="Editatu"><img src="irudiak/edit.svg" alt="Editatu" style="width:18px;height:18px;vertical-align:middle; color: #7F0001;"></a> 
                            <a href="delete_item.php?item=<?= $row['id'] ?>" title="Ezabatu" aria-label="Ezabatu" onclick="return confirm('Benetan ezabatu?');"><img src="irudiak/zakarrontzia.svg" alt="Ezabatu" style="width:18px;height:18px;vertical-align:middle; color: #7F0001;"></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p style="text-align:center;">Ez daude pelikularik.</p>
        <?php endif; ?>

        <div class="botoiak">
            <button type="button" class="btn-primary" onclick="window.location.href='add_item.php'">+</button>
            <button type="button" class="btn-secondary" onclick="window.location.href='index.php'">Hasierara Bueltatu</button>
        </div>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>
