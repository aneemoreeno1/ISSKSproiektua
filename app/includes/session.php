<?php
/**
 * Session initialization with SameSite support for PHP 7.2
 * Include this at the top of files that need sessions
 */

// Start output buffering to modify headers
if (!headers_sent()) {
    // Configure session before starting
    ini_set('session.use_only_cookies', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_httponly', 1);
    
    // Start session
    session_start();
    
    // Get the session cookie name
    $sessionName = session_name();
    $sessionId = session_id();
    
    // Remove the default cookie header
    header_remove('Set-Cookie');
    
    // Set cookie with SameSite attribute manually
    $cookieParams = session_get_cookie_params();
    $cookieValue = sprintf(
        '%s=%s; Path=%s; HttpOnly; SameSite=Strict',
        $sessionName,
        $sessionId,
        $cookieParams['path']
    );
    
    // Add Secure flag if HTTPS
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        $cookieValue .= '; Secure';
    }
    
    header('Set-Cookie: ' . $cookieValue, false);
}
?>
