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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $erabiltzailea = $_POST['erabiltzailea'];
    $pasahitza = $_POST['pasahitza'];

    $sql = "SELECT * FROM usuarios WHERE nombre='$erabiltzailea' AND pasahitza='$pasahitza'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['user_name'] = $row['nombre'];
        header("Location: index.php");
        exit;
    } else {
        echo "<p style='color:#ff6666; text-align:center; margin-bottom:15px;'>Erabiltzaile edo pasahitza okerrak</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <title>Erabiltzaileak</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="wrapper">
        <h1>Erabiltzaileak:</h1>

        <table>
            <tr>
                <th>ID</th>
                <th>Izena</th>
                <th>Ekintzak</th>
            </tr>

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

        <div class="botoiak">
            <button type="button" onclick="window.location.href='login.php'">Saioa Hasi</button>
            <button type="button" onclick="window.location.href='register.php'">Erregistratu</button>
            <button type="button" onclick="window.location.href='items.php'">Pelikulak Ikusi</button>
        </div>
    </div>

<?php mysqli_close($conn); ?>
</body>
</html>
