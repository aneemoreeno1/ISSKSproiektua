<?php
// Saioa hasi: erabiltzailearen datuak gordetzeko
session_start();

// Datu-basearekin konexioa egiteko konfigurazioa
$hostname = "db";
$username = "admin";
$password = "test";
$db = "database";

// Datu-basearen konexioa sortzeko
$conn = mysqli_connect($hostname, $username, $password, $db);

// Konexioak huts egiten badu, errorea erakutsi
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Erabiltzaile guztien datuak datu-baseatik eskuratzeko  
$query = mysqli_query($conn, "SELECT * FROM usuarios") or die(mysqli_error($conn));
?>

<!DOCTYPE html>
<html lang="eu">
<head>
    <link rel="stylesheet" href="style2.css"> <!-- lerro hau style2 fitxategiarekin konektatzeko da, eta guztietan kopiatu eta itatsi behar da -->
    <meta charset="UTF-8">
    <title>Erabiltzaileak</title>
</head>
<body>
    <div class="wrapper" > <!-- hau orri guztietan errepikatuko da, erdiko karratu txuria da, style2 orrialdean dago zehaztuta nolakoa izango den-->
        <h1>Erabiltzaileak</h1>
        <table>
            <tr>
                <th style="text-align:center; width: 20px;">ID</th> <!--Id textua erdian jartzeko erabilita, zenbakiak direnez eskuinean edo ezkerrean ez zen ongi ikusten -->
                <th>Izena</th>
                <th style="text-align:right; width: 90px;"> </th>
            </tr>

            <?php while ($row = mysqli_fetch_array($query)) { ?>
                <tr>
                    <td style="text-align:center; width: 20px;"><?= $row['id'] ?></td>
                    <td><?= $row['nombre'] ?></td>
                    <td>
                        <a href="show_user.php?user=<?= $row['id'] ?>" title="Ikusi" aria-label="Ikusi"><img src="irudiak/view.svg" alt="Ikusi" style="width:18px;height:18px;vertical-align:middle; color: #7F0001;"></a>
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['id']) { ?> <!-- erabiltzailearen saioa irekita badago orduan erabiltzaileak bere datuak editatu dezake horregatik sesion-->
                        <a href="modify_user.php?user=<?= $row['id'] ?>" title="Editatu" aria-label="Editatu"><img src="irudiak/edit.svg" alt="Editatu" style="width:18px;height:18px;vertical-align:middle; color: #7F0001;"></a>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>

        <div class="botoiak">
                <button type="button" class="btn-primary" style="width:100%" onclick="window.location.href='items.php'"><b> Pelikulak Ikusi</b></button>     <!-- hauek botoiak dira, primario sekundairio eta linkak, hauekin hierarkia mantenitzen da, erabiltzaileak botoi nagusiena erraz identifikatzen du-->
                <button type="button" class="btn-link" style="padding: 5px 2px 5px 140px;" onclick="window.location.href='login.php'"> Saioa Hasi</button> | 
                <button type="button" class="btn-link" onclick="window.location.href='register.php'">Erregistratu</button>
            </div>
        </div>
    </div>

<?php mysqli_close($conn); ?>
</body>
</html>
