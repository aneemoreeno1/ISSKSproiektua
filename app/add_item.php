<?php
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

    echo "<p>Elementua ondo gehitu da!</p>";
    echo "<p><a href='items.php'>Zerrendara itzuli</a></p>";
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Elementu berria gehitu</title>
    
    <script>
        function bakarrikLetrak(testua) {
            var patroia = /^[A-Za-zÑñ\s]+$/;
            return patroia.test(testua);
        }

        function bakarrikZenbakiak(testua) {
            var patroia = /^[0-9]+$/;
            return patroia.test(testua);
        }

        function bakarrikLetrakEtaZenbakiak(testua) {
            var patroia = /^[A-Za-zÑñ0-9\s.,!?¡¿()-]+$/;
            return patroia.test(testua);
        }

        function datuakEgiaztatu() {
            // Izena: ez hutsa eta letrak/zenbakiak onartu
            var izena = document.add_form.izena.value;
            if (izena.length < 1) {
                window.alert("Izenak ezin du hutsik egon");
                return false;
            } else if (!bakarrikLetrakEtaZenbakiak(izena)) {
                window.alert("Izenak soilik letrak, zenbakiak eta karaktere arruntak izan behar ditu");
                return false;
            }

            // Deskribapena: luzera maximoa egiaztatu (aukerakoa)
            var deskribapena = document.add_form.deskribapena.value;
            if (deskribapena.length > 500) {
                window.alert("Deskribapenak ezin du 500 karaktere baino gehiago izan");
                return false;
            }

            // Urtea: zenbaki arrunta eta arrazoizko tartea
            var urtea = document.add_form.urtea.value;
            if (urtea !== "") {
                if (!bakarrikZenbakiak(urtea)) {
                    window.alert("Urteak zenbaki osoa izan behar du");
                    return false;
                }
                
                var urteZenbakia = parseInt(urtea);
                var gaurkoUrtea = new Date().getFullYear();
                
                if (urteZenbakia < 1888) { // Lehenengo pelikularen urtea
                    window.alert("Urtea ez da egokia. 1888 baino handiagoa izan behar da");
                    return false;
                }
                
                if (urteZenbakia > gaurkoUrtea + 5) {
                    window.alert("Urtea ez da egokia. Ezin da etorkizuneko 5 urte baino gehiago izan");
                    return false;
                }
            }

            // Egilea: letrak eta karaktere arruntak baino ez (aukerakoa)
            var egilea = document.add_form.egilea.value;
            if (egilea !== "" && !bakarrikLetrakEtaZenbakiak(egilea)) {
                window.alert("Egileak soilik letrak, zenbakiak eta karaktere arruntak izan behar ditu");
                return false;
            }

            // Generoa: letrak baino ez (aukerakoa)
            var generoa = document.add_form.generoa.value;
            if (generoa !== "" && !bakarrikLetrak(generoa)) {
                window.alert("Generoak soilik letrak izan behar ditu");
                return false;
            }

            // Datuak guztiz ongi badaude
            return true;
        }
    </script>
</head>
<body>
    <h1>Elementu berria gehitu</h1>
    <form id="add_form" name="add_form" method="POST" onsubmit="return datuakEgiaztatu()">
        Izena: <input type="text" name="izena" required><br><br>
        Deskribapena: <textarea name="deskribapena" rows="4" cols="50"></textarea><br><br>
        Urtea: <input type="number" name="urtea"><br><br>
        Egilea: <input type="text" name="egilea"><br><br>
        Generoa: <input type="text" name="generoa"><br><br>
        <input type="submit" value="Gehitu">
    </form>
    <p><a href="items.php">Atzera</a></p>
</body>
</html>
