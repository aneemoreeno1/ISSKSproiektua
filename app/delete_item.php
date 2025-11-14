<?php
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
    $item_id = $_POST['item_id'];

    $stmt = mysqli_prepare($conn, "DELETE FROM pelikulak WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $item_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    header("Location: items.php");
    exit;
}

// GET bidez item ID-a jaso bada (ezabaketa orria erakusteko)
if (isset($_GET['item'])) {
    if (!is_numeric($_GET['item'])) {
        die("Invalid item ID");
    }
    $item_id = (int)$_GET['item'];

    $stmt = mysqli_prepare($conn, "SELECT * FROM pelikulak WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $item_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $pelikula = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <title>Ezabatu Pelikula</title>
    <link rel="stylesheet" href="style2.css">
    <script src="js/common.js" defer></script>
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
                <button type="button" class="btn-secondary" data-navigate="items.php">Bueltatu</button>
            </form>
               
        <?php else: ?>
            <p>Ez da pelikularik aurkitu.</p>
        <?php endif; ?>

<?php mysqli_close($conn); ?>
</body>
</html>
