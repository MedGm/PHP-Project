
<?php
require_once 'includes/session_manager.php';
initializeSession();

// Clear all session data
$_SESSION = array();

// Destroy session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Destroy session
session_destroy();

// Redirect to login
header('Location: index.php');
exit;