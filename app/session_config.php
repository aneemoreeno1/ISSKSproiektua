<?php
// Sesioa hasteko eta cookiearen konfigurazioa ezartzeko
if (session_status() === PHP_SESSION_NONE) {

    $cookieOptions = [
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => true,
        'httponly' => true,
        'samesite' => 'Strict'
    ];

    session_set_cookie_params($cookieOptions);
    session_start();
}
?>
