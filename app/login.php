<?php
require_once 'includes/header.php';
?>

<h2>🔐 Saioa Hasi</h2>

<form id="login_form" method="POST">
    <div>
        <label>Emaila:</label><br>
        <input type="email" name="email" placeholder="zure@email.eus" required>
    </div>
    
    <div>
        <label>Pasahitza:</label><br>
        <input type="password" name="password" placeholder="Pasahitza" required>
    </div>
    
    <br>
    <button type="submit" id="login_submit">Sartu</button>
</form>

<p><a href="register.php">Ez duzu konturik? Erregistratu hemen</a></p>

<?php require_once 'includes/footer.php'; ?>
