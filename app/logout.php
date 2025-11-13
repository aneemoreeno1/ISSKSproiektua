<?php
// logout.php - Secure logout functionality

// Include security utilities
require_once 'security.php';

// Start secure session
start_secure_session();

// Verify CSRF token for logout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        // Clear all session data
        $_SESSION = array();
        
        // Delete the session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destroy the session
        session_destroy();
        
        // Redirect to login page
        header("Location: login.php?logout=1");
        exit;
    } else {
        die("Segurtasun errorea.");
    }
} else {
    // GET request - show logout confirmation
    ?>
    <!DOCTYPE html>
    <html lang="eu">
    <head>
        <meta charset="UTF-8">
        <title>Saioa Itxi</title>
        <link rel="stylesheet" href="style2.css">
    </head>
    <body>
        <div class="wrapper" style="width: 20%">
            <h1>Saioa Itxi</h1>
            <p>Ziur zaude saioa itxi nahi duzula?</p>
            
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                <div class="botoiak">
                    <button type="submit" class="btn-primary" style="width:100%">Bai, Itxi Saioa</button>
                    <button type="button" class="btn-secondary" style="width:100%" onclick="history.back()">Ezeztatu</button>
                </div>
            </form>
        </div>
    </body>
    </html>
    <?php
}
?>