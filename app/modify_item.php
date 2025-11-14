	<?php
// modify_item.php - Pelikularen datuak aldatu

$hostname = "db";
$username = "admin";
$password = "test";
$db = "database";

$conn = mysqli_connect($hostname, $username, $password, $db);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// ID lortu GET bidez eta balidatu
if (!isset($_GET['item']) || !is_numeric($_GET['item'])) {
    die("Invalid item ID");
}
$item_id = (int)$_GET['item'];

// Pelikularen datuak kargatu
$stmt = mysqli_prepare($conn, "SELECT * FROM pelikulak WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $item_id);
mysqli_stmt_execute($stmt);
$emaitza = mysqli_stmt_get_result($stmt);

if ($emaitza->num_rows > 0) {
    $pelikula = mysqli_fetch_array($emaitza);
    mysqli_stmt_close($stmt);
} else {
    mysqli_stmt_close($stmt);
    die("Pelikula ez da existitzen.");
}

// Formularioa bidali bada
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $izena = $_POST['izena'];
    $deskribapena = $_POST['deskribapena'];
    $urtea = $_POST['urtea'];
    $egilea = $_POST['egilea'];
    $generoa = $_POST['generoa'];
    
    // Datuak eguneratu
    $stmt = mysqli_prepare($conn, "UPDATE pelikulak SET izena = ?, deskribapena = ?, urtea = ?, egilea = ?, generoa = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ssissi", $izena, $deskribapena, $urtea, $egilea, $generoa, $item_id);
    
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        // Datuak berriro kargatu
        $stmt2 = mysqli_prepare($conn, "SELECT * FROM pelikulak WHERE id = ?");
        mysqli_stmt_bind_param($stmt2, "i", $item_id);
        mysqli_stmt_execute($stmt2);
        $emaitza = mysqli_stmt_get_result($stmt2);
        $pelikula = mysqli_fetch_array($emaitza);
        mysqli_stmt_close($stmt2);
    } else {
        echo "Errorea: " . mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <title>Pelikularen datuak aldatu</title>
    <link rel="stylesheet" href="style2.css">
    <script src="js/common.js" defer></script>
    <script src="js/items.js" defer></script>
</head>
<body>
    <div class="wrapper">
        <h1>Pelikularen datuak aldatu</h1>

        <form id="item_modify_form" name="item_modify_form" method="POST">
            <div class="form-grid">
                <div>
                    <label for="izena">Izena:</label><br>
                    <input type="text" id="izena" name="izena" value="<?php echo $pelikula['izena']; ?>" required>
                </div>

                <div>
                    <label for="urtea">Urtea:</label><br>
                    <input type="number" id="urtea" name="urtea" value="<?php echo $pelikula['urtea']; ?>">
                </div>

                <div>
                    <label for="egilea">Egilea:</label><br>
                    <input type="text" id="egilea" name="egilea" value="<?php echo $pelikula['egilea']; ?>">
                </div>

                <div>
                    <label for="generoa">Generoa:</label><br>
                    <input type="text" id="generoa" name="generoa" value="<?php echo $pelikula['generoa']; ?>">
                </div>
            </div>

            <div class="full-width" style="margin-top:12px;">
                <label for="deskribapena">Deskribapena:</label><br>
                <textarea id="deskribapena" name="deskribapena" rows="4" style="width: 100%; font-family: 'Segoe UI';"><?php echo $pelikula['deskribapena']; ?></textarea>
            </div>

            <div class="botoiak" style="margin-top:18px;">
                <button type="submit" id="item_modify_submit" class="btn-primary">Datuak gorde</button>
                <button type="button" id="items_back" class="btn-secondary" data-navigate="items.php">Atzera</button>
            </div>
        </form>
    </div>
</body>
</html>
