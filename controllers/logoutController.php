<?php

/**
 * Logout Controller
 * Handles the user logout process by destroying the session
 */

// Start the session (if not already started)
session_start();

// Clear all session variables
$_SESSION = array();

// If there's a session cookie, destroy it as well
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: ../views/loginPage.php");
exit();
