<?php
// Simple session start
session_start();

if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

// Session timeout
$timeout = 900;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    session_unset();
    session_destroy();
}
$_SESSION['last_activity'] = time();

// Security functions
function safe_output($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Database connection
$hostname = "db";
$username = "admin";
$password = "test";
$db = "database";

$conn = mysqli_connect($hostname, $username, $password, $db);
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die("Database connection error");
}
mysqli_set_charset($conn, 'utf8');

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
                        <td><?= safe_output($row['izena']) ?></td>
                        <td><?= safe_output($row['urtea']) ?></td>
                        <td><?= safe_output($row['generoa']) ?></td>
                        <td>
                            <a href="show_item.php?item=<?= urlencode($row['id']) ?>" title="Ikusi" aria-label="Ikusi">Ikusi</a> 
                            <a href="modify_item.php?item=<?= urlencode($row['id']) ?>" title="Editatu" aria-label="Editatu">Editatu</a> 
                            <a href="delete_item.php?item=<?= urlencode($row['id']) ?>" title="Ezabatu" aria-label="Ezabatu" class="delete-link">Ezabatu</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p class="info-message">Ez daude pelikularik.</p>
        <?php endif; ?>

        <div class="botoiak">
            <a href="add_item.php" class="btn-primary">+</a>
            <a href="index.php" class="btn-secondary">Hasierara Bueltatu</a>
        </div>
    </div>
</body>
</html>
<?php mysqli_close($conn); ?>
