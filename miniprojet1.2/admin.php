<?php
require_once 'includes/session_manager.php';
require_once 'includes/config.php';
initializeSession();

// Remove the $superadmin and $coordinators arrays

//had function dyal securisation d admin , makibqash tqdr dakhal email w password ila khtiti 3 fois pour 15mins
function checkLoginAttempts($ip) {
    if (!isset($_SESSION['login_attempts'][$ip])) {
        $_SESSION['login_attempts'][$ip] = 0;
        $_SESSION['lockout_time'][$ip] = 0;
    }
    
    $attempts = $_SESSION['login_attempts'][$ip];
    $maxAttempts = 3;
    $lockoutDuration = 0; 
    
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

$role = isset($_GET['role']) ? $_GET['role'] : '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = filter_var($_POST['username'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $program = $_POST['program'] ?? '';
    
    try {
        $pdo = Database::getInstance();
        
        if ($_GET['role'] === 'superadmin') {
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['authenticated'] = true;
                $_SESSION['user_type'] = 'superadmin';
                $_SESSION['email'] = $email;
                $_SESSION['username'] = $user['name'] ?? 'Administrator';
                header('Location: dashboard2.php');
                exit;
            }
        } elseif ($_GET['role'] === 'chef' && !empty($program)) {
            $stmt = $pdo->prepare("SELECT * FROM coordinators WHERE email = :email AND program = :program");
            $stmt->execute(['email' => $email, 'program' => $program]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['authenticated'] = true;
                $_SESSION['user_type'] = 'coordinator';
                $_SESSION['email'] = $email;
                $_SESSION['program'] = $program;
                $_SESSION['username'] = $user['name'] ?? "Coordinator - $program";
                header('Location: dashboard.php');
                exit;
            }
        }
        throw new Exception('Invalid credentials');
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="https://fstt.ac.ma/Portail2023/wp-content/uploads/2023/03/Untitled-3-300x300.png" sizes="192x192">
    <style> body{ overflow: hidden;} </style>
</head>
<body>
    <div class="login-box">
        <h2><?php echo $role === 'superadmin' ? 'Administration Login' : 'Coordinateur Login'; ?></h2>
        <div class="form">
            <form method="POST" action="admin.php?role=<?php echo htmlspecialchars($role); ?>">
                <?php if ($role === 'chef'): ?>
                <select name="program" required>
                    <option value="">Selectionner</option>
                    <optgroup label="Cycle Ingenieur">
                        <option value="LSI">Logiciels et systèmes Intelligens</option>
                        <option value="GI">Genie industriel</option>
                        <option value="GEO">Geoinformation</option>
                        <option value="GEMI">Genie Electrique et Management Industriel</option>
                        <option value="GA">Genie Agroalimentaire</option>
                    </optgroup>
                    <optgroup label="Master">
                        <option value="SE">Sciences d'Environnement</option>
                        <option value="AISD">Intelligence Artificielle et Sciences de Données</option>
                        <option value="ITBD">IT et Big Data</option>
                        <option value="GC">Genie Civil</option>
                        <option value="GE">Genie Energitique</option>
                        <option value="MMSD">Modélisation Mathématique et Science de Données</option>
                    </optgroup>
                </select>
                <?php endif; ?>
                <input type="email" name="username" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <div class="button-container">
                    <button type="submit" name="login">Login</button>
                    <button type="button" onclick="window.location.href='index.php'">Back</button>
                </div>
                <?php if($error): ?>
                    <p class="error"><?php echo htmlspecialchars($error); ?></p>
                <?php endif; ?>
            </form>
        </div>
        <?php if(!$role): ?>
            <div class="role-select">
                <a href="admin.php?role=superadmin">Super Admin Login</a>
                <a href="admin.php?role=chef">Program Coordinator Login</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>