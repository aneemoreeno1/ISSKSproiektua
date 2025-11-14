<?php
// Saioa hasi: erabiltzailearen datuak gordetzeko
require_once 'includes/session.php';

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
    <link rel="stylesheet" href="style2.css">
    <meta charset="UTF-8">
    <title>Erabiltzaileak</title>
    <script src="js/common.js" defer></script>
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
                        
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['id']) { ?> <!-- erabiltzailearen saioa irekita badago orduan erabiltzaileak bere datuak editatu dezake horregatik sesion-->
                              <a style="text-align:right" href="modify_user.php?user=<?= $row['id'] ?>">Editatu</a> |
                              <a style="text-align:right" href="show_user.php?user=<?= $row['id'] ?>">Ikusi</a>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>

        <div class="botoiak">
                <button type="button" class="btn-primary" data-navigate="items.php">Pelikulak Ikusi</button>
                <button type="button" class="btn-link" data-navigate="login.php"> Saioa Hasi</button> | 
                <button type="button" class="btn-link" data-navigate="register.php">Erregistratu</button>
            </div>
        </div>
    </div>

<?php mysqli_close($conn); ?>
</body>
</html>
