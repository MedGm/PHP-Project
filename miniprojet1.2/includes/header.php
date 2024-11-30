<?php
function getCurrentPageTitle() {
    $currentPage = basename($_SERVER['PHP_SELF'], '.php');
    $titles = [
        'dashboard' => 'Dashboard',
        'dashboard2' => 'Dashboard',
        'students' => 'Students',
        'charts' => 'Statistics',
        'excel_files' => 'Excel Files',
        'profile' => 'Profile'
    ];
    return $titles[$currentPage] ?? 'Unknown Page';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo getCurrentPageTitle(); ?> - UAE</title>
    <link rel="stylesheet" href="style2.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <h2><?php echo isCoordinator() ? "Programme {$_SESSION['program']}" : 'University Abdelmalek Essaâdi'; ?></h2>
        <ul>
            <li><a href="<?php echo isCoordinator() ? 'dashboard.php' : 'dashboard2.php'; ?>" 
                   class="<?php echo in_array($currentPage, ['dashboard', 'dashboard2']) ? 'active' : ''; ?>">
                Dashboard
            </a></li>
            <?php if (!isCoordinator()): ?>
                <li><a href="charts.php" class="<?php echo $currentPage === 'charts' ? 'active' : ''; ?>">
                    Statistiques
                </a></li>
            <?php endif; ?>
            <li><a href="students.php" class="<?php echo $currentPage === 'students' ? 'active' : ''; ?>">
                Students
            </a></li>
            <li><a href="excel_files.php" class="<?php echo $currentPage === 'excel_files' ? 'active' : ''; ?>">
                Excel Files
            </a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="top-nav" style="margin-left:20px">
            <div class="breadcrumb">
                <p>Home > <span><?php echo getCurrentPageTitle(); ?></span></p>
            </div>
            
            <div class="header-actions">
                <div class="user-menu">
                    <button class="user-menu-btn" onclick="toggleUserMenu()">
                        <i class="fas fa-user-circle"></i>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <div class="user-info">
                            <p><?php echo $_SESSION['username'] ?? 'User'; ?></p>
                            <small><?php echo isCoordinator() ? 'Coordinator' : 'Admin'; ?></small>
                        </div>
                        <a href="#" onclick="showProfile()"><i class="fas fa-user"></i> Profile</a>
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-container">
