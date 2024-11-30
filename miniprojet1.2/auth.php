
<?php
require_once 'includes/session_manager.php';
require_once 'includes/config.php';
require_once 'includes/auth_service.php';

initializeSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$auth = new AuthService();

try {
    $loginType = filter_input(INPUT_POST, 'login_type', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $program = filter_input(INPUT_POST, 'program', FILTER_SANITIZE_STRING);

    if ($loginType === 'admin') {
        if ($auth->authenticateAdmin($email, $password)) {
            $_SESSION['user'] = [
                'type' => 'admin',
                'email' => $email,
                'authenticated' => true
            ];
            header('Location: dashboard2.php');
            exit;
        }
    } else if ($loginType === 'coordinator') {
        if ($auth->authenticateCoordinator($email, $password, $program)) {
            $_SESSION['user'] = [
                'type' => 'coordinator',
                'email' => $email,
                'program' => $program,
                'authenticated' => true
            ];
            header('Location: dashboard.php');
            exit;
        }
    }

    //wila dkshi makhdmsh lih
    $_SESSION['login_error'] = 'Invalid credentials';
    header('Location: index.php');
    exit;

} catch (Exception $e) {
    error_log($e->getMessage());
    $_SESSION['login_error'] = 'An error occurred during login';
    header('Location: index.php');
    exit;
}