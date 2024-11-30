<?php
function checkSessionTimeout() {
    $timeout = 1800; // 30 minutes
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
        session_unset();
        session_destroy();
        header('Location: index.php?timeout=1');
        exit;
    }
    $_SESSION['last_activity'] = time();
}

function initializeSession() {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        session_start();
    }
    checkSessionTimeout();
}

function isAuthenticated(): bool {
    return isset($_SESSION['authenticated']) && 
           $_SESSION['authenticated'] === true && 
           isset($_SESSION['user_type']);
}

function isCoordinator(): bool {
    return isAuthenticated() && 
           $_SESSION['user_type'] === 'coordinator' &&
           isset($_SESSION['program']) &&
           !empty($_SESSION['program']);
}

function isSuperAdmin(): bool {
    return isAuthenticated() && 
           isset($_SESSION['user_type']) && 
           $_SESSION['user_type'] === 'superadmin';
}

function requireAuth() {
    if (!isAuthenticated()) {
        header('Location: admin.php');
        exit;
    }
}

function requireProgram($program = null) {
    requireAuth();
    if (!isCoordinator() || ($program && $_SESSION['program'] !== $program)) {
        header('Location: admin.php');
        exit;
    }
}

function requireSuperAdmin() {
    requireAuth();
    if (!isSuperAdmin()) {
        header('Location: admin.php');
        exit;
    }
}

function getCurrentProgram() {
    return isCoordinator() ? $_SESSION['program'] : null;
}

function ensureValidSession() {
    if (!isAuthenticated()) {
        header('Location: admin.php');
        exit;
    }
    
    if (isCoordinator() && !isset($_SESSION['program'])) {
        header('Location: admin.php');
        exit;
    }
}