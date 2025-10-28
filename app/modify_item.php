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

// ID lortu GET bidez
$item_id = $_GET['item'];

// Pelikularen datuak kargatu
$sql = "SELECT * FROM pelikulak WHERE id = $item_id";
$emaitza = mysqli_query($conn, $sql);

if ($emaitza->num_rows > 0) {
    $pelikula = mysqli_fetch_array($emaitza);
} else {
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
    $sql = "UPDATE pelikulak 
            SET izena = '$izena', 
                deskribapena = '$deskribapena', 
                urtea = '$urtea', 
                egilea = '$egilea', 
                generoa = '$generoa'
            WHERE id = $item_id";
    
    $emaitza = mysqli_query($conn, $sql);
    
    if ($emaitza) {
        echo "<script>alert('Datuak eguneratuak!');</script>";
        // Datuak berriro kargatu
        $sql = "SELECT * FROM pelikulak WHERE id = $item_id";
        $emaitza = mysqli_query($conn, $sql);
        $pelikula = mysqli_fetch_array($emaitza);
    } else {
        echo "Errorea: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <title>Pelikularen datuak aldatu</title>
    <link rel="stylesheet" href="style2.css">
    <script>
        function bakarrikLetrak(testua) {
            var patroia = /^[A-Za-zÑñ\s]+$/;
            return patroia.test(testua);
        }

        function bakarrikZenbakiak(testua) {
            var patroia = /^[0-9]+$/;
            return patroia.test(testua);
        }

        function gutxienezLetraBat(testua) {
            var patroia = /[A-Za-zÑñ]/;
            return patroia.test(testua);
        }

        function karaktereArruntaK(testua) {
            var patroia = /^[A-Za-zÑñ0-9\s.,!?¡¿()-]+$/;
            return patroia.test(testua);
        }

        function datuakEgiaztatu() {
            // Izena
            var izena = document.item_modify_form.izena.value;
            if (izena.length < 1) {
                alert("Izenak ezin du hutsik egon");
                return false;
            } else if (!karaktereArruntaK(izena)) {
                alert("Izenak soilik letrak, zenbakiak eta karaktere arruntak izan behar ditu");
                return false;
            }

            // Deskribapena
            var deskribapena = document.item_modify_form.deskribapena.value;
            if (deskribapena.length > 500) {
                alert("Deskribapenak ezin du 500 karaktere baino gehiago izan");
                return false;
            }

            // Urtea
            var urtea = document.item_modify_form.urtea.value;
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

            // Egilea
            var egilea = document.item_modify_form.egilea.value;
            if (egilea !== "") {
                if (!karaktereArruntaK(egilea)) {
                    alert("Egileak soilik letrak, zenbakiak eta karaktere arruntak izan behar ditu");
                    return false;
                } else if (!gutxienezLetraBat(egilea)) {
                    alert("Egileak gutxienez letra bat izan behar du");
                    return false;
                }
            }

            // Generoa
            var generoa = document.item_modify_form.generoa.value;
            if (generoa !== "") {
                if (!bakarrikLetrak(generoa)) {
                    alert("Generoak soilik letrak izan behar ditu");
                    return false;
                } else if (!gutxienezLetraBat(generoa)) {
                    alert("Generoak gutxienez letra bat izan behar du");
                    return false;
                }
            }

            return true;
        }
    </script>
</head>
<body>
    <div class="wrapper">
        <h1>Pelikularen datuak aldatu</h1>

        <form id="item_modify_form" name="item_modify_form" method="POST" onsubmit="return datuakEgiaztatu()">
            <div class="form-grid">
                <div>
                    <label for="izena">Izena:</label>
                    <input type="text" id="izena" name="izena" value="<?php echo $pelikula['izena']; ?>" required>
                </div>

                <div>
                    <label for="urtea">Urtea:</label>
                    <input type="number" id="urtea" name="urtea" value="<?php echo $pelikula['urtea']; ?>">
                </div>

                <div>
                    <label for="egilea">Egilea:</label>
                    <input type="text" id="egilea" name="egilea" value="<?php echo $pelikula['egilea']; ?>">
                </div>

                <div>
                    <label for="generoa">Generoa:</label>
                    <input type="text" id="generoa" name="generoa" value="<?php echo $pelikula['generoa']; ?>">
                </div>
            </div>

            <div class="full-width" style="margin-top:12px;">
                <label for="deskribapena">Deskribapena:</label>
                <textarea id="deskribapena" name="deskribapena" rows="4" cols="50"><?php echo $pelikula['deskribapena']; ?></textarea>
            </div>

            <div class="botoiak" style="margin-top:18px;">
                <button type="submit" id="item_modify_submit" class="btn-primary">Datuak gorde</button>
                <button type="button" id="items_back" class="btn-secondary" onclick="window.location.href='items.php'">Atzera</button>
            </div>
        </form>
    </div>
</body>
</html>
