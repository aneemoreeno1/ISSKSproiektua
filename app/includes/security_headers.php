<?php
/**
 * SECURITY HEADERS - Auto-included on all PHP pages
 * This file is automatically prepended via php.ini configuration
 * 
 * All inline scripts have been refactored to external files
 * CSP now enforces strict script loading from same origin only
 */

// Only set headers if they haven't been sent yet
if (!headers_sent()) {
    // Note: CSP is set globally via .htaccess to apply to ALL responses (PHP, static files, errors)
    
    // X-Content-Type-Options: Prevent MIME type sniffing
    header("X-Content-Type-Options: nosniff");
    
    // X-Frame-Options: Prevent clickjacking
    header("X-Frame-Options: DENY");
    
    // X-XSS-Protection: Enable XSS filtering
    header("X-XSS-Protection: 1; mode=block");
    
    // Referrer-Policy
    header("Referrer-Policy: strict-origin-when-cross-origin");
    
    // Permissions-Policy
    header("Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=()");
    
    // Remove server information headers
    header_remove("X-Powered-By");
}
?>
