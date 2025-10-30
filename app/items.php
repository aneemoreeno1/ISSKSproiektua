<?php

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
                    <th>ID</th>
                    <th>Izena</th>
                    <th>Ekintzak</th>
                </tr>
                <!-- Pelikula bakoitzeko datuak erakusteko -->
                <?php while ($row = mysqli_fetch_array($query)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= $row['izena'] ?></td>
                        <td>
                            <a href="show_item.php?item=<?= $row['id'] ?>">Ikusi</a> | 
                            <a href="modify_item.php?item=<?= $row['id'] ?>">Editatu</a> | 
                            <a href="delete_item.php?item=<?= $row['id'] ?>">Ezabatu</a>
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
