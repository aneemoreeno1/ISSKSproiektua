<?php
session_start();


$hostname = "db";
$username = "admin";
$password = "test";
$db = "database";

$conn = mysqli_connect($hostname, $username, $password, $db);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$query = mysqli_query($conn, "SELECT * FROM usuarios") or die(mysqli_error($conn));
?>

<!DOCTYPE html>
<html lang="eu">
<head>
    <link rel="stylesheet" href="style2.css">
    <meta charset="UTF-8">
    <title>Erabiltzaileak</title>
</head>
<body>
    <div class="wrapper" >
        <h1>Erabiltzaileak</h1>
        <table>
            <tr>
                <th style="text-align:center; width: 20px;">ID</th>
                <th>Izena</th>
                <th style="text-align:right; width: 20px;"> </th>
            </tr>

            <?php while ($row = mysqli_fetch_array($query)) { ?>
                <tr>
                    <td style="text-align:center; width: 20px;"><?= $row['id'] ?></td>
                    <td><?= $row['nombre'] ?></td>
                    <td>
                        <a style="text-align:right" href="show_user.php?user=<?= $row['id'] ?>">Ikusi</a>
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['id']) { ?>
                            | <a href="modify_user.php?user=<?= $row['id'] ?>">Editatu</a>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>

        <div class="botoiak">
                <button type="button" class="btn-primary" style="width:100%" onclick="window.location.href='items.php'">Pelikulak Ikusi</button>     
                <button type="button" class="btn-link" style="padding: 5px 2px 5px 140px;" onclick="window.location.href='login.php'"> Saioa Hasi</button> | 
                <button type="button" class="btn-link" onclick="window.location.href='register.php'">Erregistratu</button>
            </div>
        </div>
    </div>

<?php mysqli_close($conn); ?>
</body>
</html>
