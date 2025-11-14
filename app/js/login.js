// Login form validation
function validateLogin() {
    var erabiltzailea = document.login_form.erabiltzailea.value;
    var pasahitza = document.login_form.pasahitza.value;

    if (erabiltzailea.length < 1) {
        alert("Sartu erabiltzaile izena");
        return false;
    }
    if (pasahitza.length < 1) {
        alert("Sartu pasahitza");
        return false;
    }
    return true;
}
