<?php
// register.php - Erabiltzaile berria gehitu

// Datu-basearen konexiorako konfigurazioa
$hostname = "db";
$username = "admin";
$password = "test";
$db = "database";

$mezua = ""; // Erabiltzaileari mezuak erakusteko aldagaia
$mezua_type = ""; // "success" edo "error"

// Datu-basearekin konexioa establetzeko
$conn = mysqli_connect($hostname, $username, $password, $db);

// Handle connection failure gracefully so we can show the message in the page
$db_available = true;
if (!$conn) {
    error_log("Database connection failed (register.php): " . mysqli_connect_error());
    $mezua = "Ezin izan da datu-basearekin konektatu";
    $mezua_type = "error";
    $db_available = false;
}

// Jadanik ondo gorde dela jakinarazten duen GET parametroa badago, erakutsi mezua
if (isset($_GET['created']) && $_GET['created'] == '1') {
    $mezua = "Ondo gorde da!";
    $mezua_type = "success";
}

// Formularioa bidali bada, datuak prozesatzeko
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // If DB isn't available, avoid calling mysqli_* and show an error message
    if (!$db_available) {
        $mezua = "Ezin izan da datu-basearekin konektatu";
        $mezua_type = "error";
    } else {
        // Formularioko datuak eskuratu
        $izena = trim($_POST['izena'] ?? '');
        $nan = trim($_POST['nan'] ?? '');
        $telefonoa = trim($_POST['telefonoa'] ?? '');
        $data = trim($_POST['data'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $pasahitza = trim($_POST['pasahitza'] ?? '');

        // 1) NAN hori duen erabiltzailea jadanik badagoen egiaztatu
        if ($nan !== '') {
            $chk_stmt = mysqli_prepare($conn, "SELECT id FROM usuarios WHERE nan = ? LIMIT 1");
            mysqli_stmt_bind_param($chk_stmt, "s", $nan);
            mysqli_stmt_execute($chk_stmt);
            $chk_res = mysqli_stmt_get_result($chk_stmt);
            if ($chk_res && mysqli_num_rows($chk_res) > 0) {
                $mezua = "Jada badago erabiltzaile bat NAN horrekin (" . htmlspecialchars($nan) . ").";
                $mezua_type = "error";
            }
            mysqli_stmt_close($chk_stmt);
        }

        // 2) NAN bikoizturik ez badago, erabiltzailea datu-basean gorde
        if ($mezua === "") {
            $stmt = mysqli_prepare($conn, "INSERT INTO usuarios (nombre, nan, telefonoa, jaiotze_data, email, pasahitza) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssssss", $izena, $nan, $telefonoa, $data, $email, $pasahitza);
            
            // Datuak ondo gorde badira, berbideraketa egin
            if (mysqli_stmt_execute($stmt)) {
                // Use PRG pattern: redirect to self with created flag
                mysqli_stmt_close($stmt);
                header('Location: register.php?created=1');
                exit();
            } else {
                $mezua = "Arazo bat egon da datu-basean.";
                error_log("Register insert error: " . mysqli_stmt_error($stmt));
                $mezua_type = "error";
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="eu">
<head>
    <meta charset="UTF-8">
    <title>Erregistratu</title>
    <link rel="stylesheet" href="style2.css">
    <script>
        
        // Izena letrak soilik direla egiaztatzeko funtzioa
        function bakarrikLetrak(testua) {
            return /^[A-Za-zÑñ\s]+$/.test(testua);
        }
        
        // Zenbakiak soilik direla egiaztatzeko funtzioa
        function bakarrikZenbakiak(testua) {
            return /^[0-9]+$/.test(testua);
        }
        
        // NAN-aren letra kalkulatzeko funtzioa
        function kalkulatuNanLetra(nanZenbakiak) {
            var kate = "TRWAGMYFPDXBNJZSQVHLCKET";
            return kate[parseInt(nanZenbakiak) % 23];
        }

        // Emailaren formatua zuzena den egiaztatzeko funtzioa
        function emailEgokia(emaila) {
            return /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/.test(emaila);
        }

        // Formularioaren datu guztiak egiaztatzeko funtzio nagusia
        function datuakEgiaztatu() {
            // Izena lortu eta egiaztatu
            var izena = document.register_form.izena.value;
            if (izena.length < 1 || !bakarrikLetrak(izena)) {
                alert("Izenak ezin du hutsik egon eta soilik letrak izan behar ditu");
                return false;
            }

            // NAN lortu eta egiaztatu
            var nan = document.register_form.nan.value;
            var nanZatiak = nan.split("-");
            if (nanZatiak.length != 2 || nanZatiak[0].length != 8 || !bakarrikZenbakiak(nanZatiak[0]) || nanZatiak[1].length != 1) {
                alert("NAN formatua okerra. Adibidea: 12345678-Z");
                return false;
            }
            if (kalkulatuNanLetra(nanZatiak[0]).toLowerCase() != nanZatiak[1].toLowerCase()) {
                alert("NAN idatzita dagoena ez da zuzena");
                return false;
            }

            // Telefonoa lortu eta egiaztatu
            var telefonoa = document.register_form.telefonoa.value;
            if (telefonoa.length != 9 || !bakarrikZenbakiak(telefonoa)) {
                alert("Telefonoak 9 zenbaki izan behar ditu");
                return false;
            }

            // Data lortu, egiaztatu eta formatua normalizatu
            var dataField = document.register_form.data;
            var data = dataField.value;

            // Navegadoreak Date objektua ematen badu, YYYY-MM-DD formatura bihurtu gu Text erabiltzen ari gara
            /*if ((!data || data.indexOf('-') === -1) && dataField.valueAsDate) {
                var dObjNorm = dataField.valueAsDate;
                var yyyyN = dObjNorm.getFullYear();
                var mmN = ('0' + (dObjNorm.getMonth() + 1)).slice(-2);
                var ddN = ('0' + dObjNorm.getDate()).slice(-2);
                data = yyyyN + '-' + mmN + '-' + ddN;
                dataField.value = data; // Eremuan formatu normalizatua gorde
            }*/
            var dataZatiak = data.split("-");
            if (data.length != 10 || dataZatiak.length != 3) {
                alert("Data formatua okerra. Adibidea: 2024-12-20");
                return false;
            }
            
            // Data baliozko den egiaztatu
            var urtea = parseInt(dataZatiak[0]);
            var hilabetea = parseInt(dataZatiak[1]);
            var eguna = parseInt(dataZatiak[2]);

            if (hilabetea < 1 || hilabetea > 12) {
                alert("Hilabetea 1 eta 12 artean egon behar da");
                return false;
            }

            // Hilabete bakoitzaren egun kopurua zehaztu (bisustua kontuan hartuz)
            var egunMaximoak = [31,28,31,30,31,30,31,31,30,31,30,31];
            if ((urtea % 4 === 0 && urtea % 100 !== 0) || (urtea % 400 === 0)) {
                egunMaximoak[1] = 29;
            }
            if (eguna < 1 || eguna > egunMaximoak[hilabetea-1]) {
                alert("Eguna okerra. " + hilabetea + ". hilabeteak " + egunMaximoak[hilabetea-1] + " egun baino ez ditu izan");
                return false;
            }
            
            // Data ez dela 120 urte baino zaharragoa egiaztatu
            var gaur = new Date();
            if (urtea < gaur.getFullYear() - 120 || urtea > gaur.getFullYear()) {
                alert("Urte okerra. Ez da 120 urte baino gehiago edo etorkizuneko data izan");
                return false;
            }

            // Emaila egiaztatu
            if (!emailEgokia(document.register_form.email.value)) {
                alert("Emaila ez da zuzena");
                return false;
            }

            // Pasahitza egiaztatu, ez dugu hash a egiten
            var pasahitza = document.register_form.pasahitza.value;
            var errep_pasahitza = document.register_form.errep_pasahitza.value;
            if (pasahitza != errep_pasahitza) {
                alert("Pasahitzak ez dira berdinak.");
                return false;
            }
            
            // Pasahitzaren segurtasuna egiaztatu
            if (pasahitza.length < 8 || !/[0-9]/.test(pasahitza) || !/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(pasahitza)) {
                alert("Pasahitza ez segurua. Gutxienez 8 karaktere, zenbaki bat eta karaktere berezi bat izan behar ditu.");
                return false;
            }

            // Datu guztiak zuzenak badira, formularioa bidali
            return true;
        }
    </script>
</head>
<body>
    <div class="wrapper">
        <h1>Erregistratu</h1>

        <!-- Mezuak erakusteko toki berezia: formaren barruan botoien gainean agertuko da -->

        <!-- Erregistro formularioa -->
        <form id="register_form" name="register_form" method="POST" onsubmit="return datuakEgiaztatu()">
            <input type="text" id="izena" name="izena" placeholder="Izena" required>

            <input type="text" id="nan" name="nan" placeholder="NAN" required><br>

            <input type="tel" id="telefonoa" name="telefonoa" placeholder="Telefonoa" required>

            <input type="text" id="data" name="data" placeholder="Jaiotza data: YYYY-MM-DD" title="Format: YYYY-MM-DD" required><br>

            <input type="text" id="email" name="email" placeholder="Email" style="width:100%" required><br>

            <input type="password" id="pasahitza" name="pasahitza" placeholder="Pasahitza" required>

            <input type="password" id="errep_pasahitza" name="errep_pasahitza" placeholder="Errepikatu Pasahitza" required><br>
            
            <!-- Formularioaren botoiak -->
            <?php if ($mezua !== ""): ?>
                <p style="text-align:center; font-size: 0.7em; margin-bottom:10px; <?php echo ($mezua_type === 'success') ? 'color: #1a6f1a;' : 'color: #7f0000ff;'; ?>">
                    <?php echo htmlspecialchars($mezua); ?>
                </p>
            <?php endif; ?>

            <div class="botoiak">
                <button type="submit" class="btn-primary" id="register_submit" style="width:100%">Erregistratu</button>
                <button type="button" class="btn-secondary" onclick="window.location.href='index.php'" style="width:100%">Atzera</button> <br>
                <button type="button" class="btn-link" onclick="window.location.href='login.php'" style="">Jada baduzu kontua? Hasi Saioa</button>
            </div>
        </form>
    </div>
</body>
</html>
