<?php
session_start();

define('ADMIN_USERNAME', 'admin@fstt.ac.ma');
define('ADMIN_PASSWORD', password_hash('fstt2024', PASSWORD_DEFAULT));

//had function dyal sucrisation d admin , makibqash tqdr dakhal email w password ila khtiti 3 fois pour 15mins
function checkLoginAttempts($ip) {
    if (!isset($_SESSION['login_attempts'][$ip])) {
        $_SESSION['login_attempts'][$ip] = 0;
        $_SESSION['lockout_time'][$ip] = 0;
    }
    
    $attempts = $_SESSION['login_attempts'][$ip];
    $maxAttempts = 3;
    $lockoutDuration = 900; // 15 mins
    
    if ($attempts >= $maxAttempts) {
        $timeLeft = ($_SESSION['lockout_time'][$ip] + $lockoutDuration) - time();
        if ($timeLeft > 0) {
            $minutesLeft = ceil($timeLeft / 60);
            return ['allowed' => false, 'message' => "Account locked. Try again in $minutesLeft minutes."];
        } else {
            $_SESSION['login_attempts'][$ip] = 0;
            $_SESSION['lockout_time'][$ip] = 0;
        }
    }
    
    if ($attempts == $maxAttempts - 1) {
        $_SESSION['lockout_time'][$ip] = time();
    }
    
    return ['allowed' => true, 'message' => $attempts > 0 ? "Failed attempts: $attempts/$maxAttempts" : ''];
}

if (isset($_POST['login'])) {
    if ($_POST['username'] === ADMIN_USERNAME && 
        password_verify($_POST['password'], ADMIN_PASSWORD)) {
        $_SESSION['admin'] = true;
        header('Location: dashboard.php');
        exit();
    }
    $ip = $_SERVER['REMOTE_ADDR'];
    $loginCheck = checkLoginAttempts($ip);
    
    if (!$loginCheck['allowed']) {
        $error = $loginCheck['message'];
    } else {
        $_SESSION['login_attempts'][$ip]++;
        $error = "Invalid credentials. " . $loginCheck['message'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="https://fstt.ac.ma/Portail2023/wp-content/uploads/2023/03/Untitled-3-300x300.png" sizes="192x192">
</head>
<body>
    <div class="login-box">
        <h2>Admin Login</h2>
        <form method="POST">
            <input type="email" name="username" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
            <?php if(isset($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>