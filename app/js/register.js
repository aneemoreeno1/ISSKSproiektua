// Register form validation functions

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
function validateRegister() {
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
