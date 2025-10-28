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

$query = mysqli_query($conn, "SELECT * FROM usuarios")
    or die(mysqli_error($conn));
<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <title>Erabiltzaileak</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <h1>Erabiltzaileak:</h1>
    <table>
        <tr><th>ID</th><th>Izena</th><th> </th></tr>
        <?php while ($row = mysqli_fetch_array($query)) { ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['nombre'] ?></td>
                <td>
                    <a href="show_user.php?user=<?= $row['id'] ?>">Ikusi</a>
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['id']) { ?>
                        | <a href="modify_user.php?user=<?= $row['id'] ?>">Editatu</a>
                    <?php } ?>
                </td>
            </tr>
        <?php } ?>
    </table>

    <p class="botoiak">
        <button onclick="window.location.href='login.php'">Saioa Hasi</button>
        <button onclick="window.location.href='register.php'">Erregistratu</button>
        <button onclick="window.location.href='items.php'">Pelikulak Ikusi</button>
    </p>

</body>
</html>
