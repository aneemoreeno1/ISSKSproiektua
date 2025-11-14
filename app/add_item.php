<?php

//____________________________Pelikula berria gehitu_________________________________________________
//Datu basearekin konexioa ezartzeko
$hostname = "db";
$username = "admin";
$password = "test";
$db = "database";

$conn = mysqli_connect($hostname, $username, $password, $db);
//Konekzioa ez bada ezarri, errorea bistaratu
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$mezua = ""; // Mezua erakusteko textu hutsa (aldagaia)

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //Formulariorako datuak eskuratu
    $izena = $_POST['izena'];
    $deskribapena = $_POST['deskribapena'];
    $urtea = $_POST['urtea'];
    $egilea = $_POST['egilea'];
    $generoa = $_POST['generoa'];
    //Pelikula gehitu datu basera
    $stmt = mysqli_prepare($conn, "INSERT INTO pelikulak (izena, deskribapena, urtea, egilea, generoa) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssiss", $izena, $deskribapena, $urtea, $egilea, $generoa);
    if (mysqli_stmt_execute($stmt)) {
        $mezua = "Pelikula ondo gehitu da!";
    } else {
        $mezua = "Arazo bat egon da: " . mysqli_stmt_error($stmt);
    }
    mysqli_stmt_close($stmt);
}
?>


<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <title>Pelikula berria gehitu</title>
    <link rel="stylesheet" href="style2.css">
    <script src="js/common.js" defer></script>
    <script src="js/items.js" defer></script>
</head>
<body>
    <div class="wrapper">
        <h1>Pelikula berria gehitu</h1>

        <?php if ($mezua !== ""): ?>
            <p style="text-align:center; font-weight:bold; font-size:1.2em; color:#66ff66;">
                <?php echo $mezua; ?>
            </p>
        <?php endif; ?>

        <form id="item_add_form" name="item_add_form" method="POST">
            <div class="form-grid">
                <div>
                    <label for="izena">Izena:</label><br>
                    <input type="text" name="izena" style="width: 100%;" required>
                </div>             

                <div>
                    <label for="urtea">Urtea:</label><br>
                    <input type="number" name="urtea" style="width: 100%" value="<?php echo date('Y'); ?>">
                </div>

                <div>
                    <label for="egilea">Egilea:</label><br>
                    <input type="text" name="egilea" style="width: 100%;">
                </div>

                <div>
                    <label for="generoa">Generoa:</label><br>
                    <input type="text" name="generoa" style="width: 100%;">
                </div>
            </div>

            <div class="full-width">
                    <label for="deskribapena">Deskribapena:</label><br>
                    <textarea name="deskribapena" rows="4" style="width: 100%;"></textarea>
                </div>

            <div class="botoiak" style="margin-top:18px;">
                <button id="item_add_submit" type="submit" class="btn-primary">Gehitu</button>
                <button type="button" class="btn-secondary" data-navigate="items.php">Atzera</button>
            </div>
        </form>
    </div>
</body>
</html>
