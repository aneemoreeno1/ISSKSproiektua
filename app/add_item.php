<?php
// add_item.php - Pelikula berria gehitu

$hostname = "db";
$username = "admin";
$password = "test";
$db = "database";

$conn = mysqli_connect($hostname, $username, $password, $db);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $izena = $_POST['izena'];
    $deskribapena = $_POST['deskribapena'];
    $urtea = $_POST['urtea'];
    $egilea = $_POST['egilea'];
    $generoa = $_POST['generoa'];

    $sql = "INSERT INTO pelikulak (izena, deskribapena, urtea, egilea, generoa)
            VALUES ('$izena', '$deskribapena', '$urtea', '$egilea', '$generoa')";
    mysqli_query($conn, $sql) or die(mysqli_error($conn));

    echo "<p>Pelikula ondo gehitu da!</p>";
    echo "<p><a href='items.php'>Zerrendara itzuli</a></p>";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pelikula berria gehitu</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function bakarrikLetrak(testua) {
            return /^[A-Za-zÑñ\s]+$/.test(testua);
        }

        function bakarrikZenbakiak(testua) {
            return /^[0-9]+$/.test(testua);
        }

        function bakarrikLetrakEtaZenbakiak(testua) {
            return /^[A-Za-zÑñ0-9\s.,!?¡¿()-]+$/.test(testua);
        }

        function gutxienezLetraBat(testua) {
            return /[A-Za-zÑñ]/.test(testua);
        }

        function datuakEgiaztatu() {
            var izena = document.item_add_form.izena.value;
            if (izena.length < 1) {
                alert("Izenak ezin du hutsik egon");
                return false;
            } else if (!bakarrikLetrakEtaZenbakiak(izena)) {
                alert("Izenak soilik letrak, zenbakiak eta karaktere arruntak izan behar ditu");
                return false;
            } else if (!gutxienezLetraBat(izena)) {
                alert("Izenak gutxienez letra bat izan behar du");
                return false;
            }

            var deskribapena = document.item_add_form.deskribapena.value;
            if (deskribapena.length > 500) {
                alert("Deskribapenak ezin du 500 karaktere baino gehiago izan");
                return false;
            }

            var urtea = document.item_add_form.urtea.value;
            if (urtea !== "") {
                if (!bakarrikZenbakiak(urtea)) {
                    alert("Urteak zenbaki osoa izan behar du");
                    return false;
                }
                var urteZenbakia = parseInt(urtea);
                var gaurkoUrtea = new Date().getFullYear();
                if (urteZenbakia < 1888) {
                    alert("Urtea ez da egokia. 1888 baino handiagoa izan behar da");
                    return false;
                }
                if (urteZenbakia > gaurkoUrtea + 5) {
                    alert("Urtea ez da egokia. Ezin da etorkizuneko 5 urte baino gehiago izan");
                    return false;
                }
            }

            var egilea = document.item_add_form.egilea.value;
            if (egilea !== "" && !bakarrikLetrakEtaZenbakiak(egilea)) {
                alert("Egileak soilik letrak, zenbakiak eta karaktere arruntak izan behar ditu");
                return false;
            } else if (egilea !== "" && !gutxienezLetraBat(egilea)) {
                alert("Egileak gutxienez letra bat izan behar du");
                return false;
            }

            var generoa = document.item_add_form.generoa.value;
            if (generoa !== "" && !bakarrikLetrak(generoa)) {
                alert("Generoak soilik letrak izan behar ditu");
                return false;
            } else if (generoa !== "" && !gutxienezLetraBat(generoa)) {
                alert("Generoak gutxienez letra bat izan behar du");
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
    <h1>Pelikula berria gehitu</h1>

    <form id="item_add_form" name="item_add_form" method="POST" onsubmit="return datuakEgiaztatu()">
        <label for="izena">Izena:</label><br>
        <input type="text" name="izena" required><br><br>

        <label for="deskribapena">Deskribapena:</label><br>
        <textarea name="deskribapena" rows="4" cols="50"></textarea><br><br>

        <label for="urtea">Urtea:</label><br>
        <input type="number" name="urtea" value="<?php echo date('Y'); ?>"><br><br>

        <label for="egilea">Egilea:</label><br>
        <input type="text" name="egilea"><br><br>

        <label for="generoa">Generoa:</label><br>
        <input type="text" name="generoa"><br><br>

        <button id="item_add_submit" type="submit">Gehitu</button>
        <button type="button" onclick="window.location.href='items.php'">Atzera</button>
    </form>
</body>
</html>
