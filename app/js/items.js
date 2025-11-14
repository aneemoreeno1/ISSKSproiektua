// Item (Pelikula) validation functions

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

function validateItemAdd() {
    var izena = document.item_add_form.izena.value;
    if (izena.length < 1) {
        alert("Izenak ezin du hutsik egon");
        return false;
    } else if (!bakarrikLetrakEtaZenbakiak(izena)) {
        alert("Izenak soilik letrak, zenbakiak eta karaktere arruntak izan behar ditu");
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

function validateItemModify() {
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
        } 
    }

    // Generoa
    var generoa = document.item_modify_form.generoa.value;
    if (generoa !== "") {
        if (!karaktereArruntaK(generoa)) {
            alert("Generoak soilik letrak, zenbakiak eta karaktere arruntak izan behar ditu");
            return false;
        } 
    }

    return true;
}

function karaktereArruntaK(testua) {
    var patroia = /^[A-Za-zÑñ0-9\s.,!?¡¿()-]+$/;
    return patroia.test(testua);
}
