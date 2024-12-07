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
    
    <!-- Core Framework Links -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/nprogress@0.2.0/nprogress.css">
    <script src="https://cdn.jsdelivr.net/npm/nprogress@0.2.0/nprogress.js"></script>
    
    <!-- Existing Stylesheets -->
    <link rel="stylesheet" href="style2.css">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="https://fstt.ac.ma/Portail2023/wp-content/uploads/2023/03/Untitled-3-300x300.png" sizes="192x192">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        .sidebar {
            /* Existing sidebar base styles remain unchanged */
            transition: all 0.3s ease;
        }

        .sidebar ul {
            padding: 0;
            margin: 20px 0;
        }

        .sidebar ul li {
            margin: 8px 0;
            position: relative;
        }

        .sidebar ul li a {
            display: flex;
            align-items: center;
            padding: 12px 25px;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
            font-weight: 500;
        }

        .sidebar ul li a:hover {
            background: rgba(255, 255, 255, 0.1);
            border-left-color: #4a90e2;
            transform: translateX(4px);
        }

        .sidebar ul li a.active {
            background: rgba(255, 255, 255, 0.15);
            border-left-color: #4a90e2;
            font-weight: 600;
        }

        .sidebar ul li a i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
            font-size: 1.1em;
            transition: transform 0.3s ease;
        }

        .sidebar ul li a:hover i {
            transform: scale(1.1);
        }

        .sidebar h2 {
            padding: 20px 25px;
            font-size: 1.2em;
            font-weight: 600;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 10px;
        }

        /* Tooltip styles */
        .sidebar ul li a::after {
            content: attr(data-tooltip);
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            white-space: nowrap;
            z-index: 1000;
        }

        .sidebar ul li a:hover::after {
            opacity: 1;
            visibility: visible;
            left: calc(100% + 10px);
        }

        /* Subtle active indicator animation */
        @keyframes activeIndicator {
            0% { transform: scaleY(0); }
            100% { transform: scaleY(1); }
        }

        .sidebar ul li a.active::before {
            content: '';
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: #4a90e2;
            animation: activeIndicator 0.3s ease forwards;
            transform-origin: top;
        }

        /* Top Navigation Bar Styles */
        .top-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04);
            position: sticky;
            top: 0;
            z-index: 100;
            transition: all 0.3s ease;
        }

        /* Breadcrumb Styles */
        .breadcrumb {
            display: flex;
            align-items: center;
            font-size: 0.9rem;
        }

        .breadcrumb p {
            color: #64748b;
        }

        .breadcrumb span {
            color: #1e293b;
            font-weight: 500;
            position: relative;
            margin-left: 0.5rem;
        }

        .breadcrumb span::before {
            content: '>';
            color: #94a3b8;
            margin: 0 0.5rem;
        }

        /* User Menu Styles */
        .user-menu {
            position: relative;
        }

        .user-menu-btn {
            display: flex;
            align-items: center;
            padding: 0.5rem;
            border: none;
            background: transparent;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .user-menu-btn i {
            font-size: 1.5rem;
            color: #64748b;
        }

        .user-menu-btn:hover i {
            color: #1e293b;
            transform: scale(1.05);
        }

        .user-dropdown {
            position: absolute;
            right: 0;
            top: calc(100% + 0.5rem);
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            width: 200px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .user-dropdown.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .user-info {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .user-info p {
            color: #1e293b;
            font-weight: 500;
            margin: 0;
        }

        .user-info small {
            color: #64748b;
            font-size: 0.875rem;
        }

        .user-dropdown a {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: #64748b;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .user-dropdown a i {
            margin-right: 0.75rem;
            font-size: 1rem;
        }

        .user-dropdown a:hover {
            background: #f8fafc;
            color: #1e293b;
        }

        /* Notification Badge */
        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: #ef4444;
            color: white;
            font-size: 0.75rem;
            padding: 0.125rem 0.375rem;
            border-radius: 9999px;
            transform: translate(50%, -50%);
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2><?php echo isCoordinator() ? "Programme {$_SESSION['program']}" : 'University Abdelmalek Essaâdi'; ?></h2>
        <ul>
            <li>
                <a href="<?php echo isCoordinator() ? 'dashboard.php' : 'dashboard2.php'; ?>" 
                   class="<?php echo in_array($currentPage, ['dashboard', 'dashboard2']) ? 'active' : ''; ?>"
                   data-tooltip="View Dashboard">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
            </li>
            <?php if (!isCoordinator()): ?>
            <li>
                <a href="charts.php" 
                   class="<?php echo $currentPage === 'charts' ? 'active' : ''; ?>"
                   data-tooltip="View Statistics">
                    <i class="fas fa-chart-bar"></i>
                    Statistiques
                </a>
            </li>
            <?php endif; ?>
            <li>
                <a href="students.php" 
                   class="<?php echo $currentPage === 'students' ? 'active' : ''; ?>"
                   data-tooltip="Manage Students">
                    <i class="fas fa-user-graduate"></i>
                    Students
                </a>
            </li>
            <li>
                <a href="excel_files.php" 
                   class="<?php echo $currentPage === 'excel_files' ? 'active' : ''; ?>"
                   data-tooltip="Manage Excel Files">
                    <i class="fas fa-file-excel"></i>
                    Excel Files
                </a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <div class="top-nav" style="margin-left:20px">
            <div class="breadcrumb">
                <p>Home > <span><?php echo getCurrentPageTitle(); ?></span></p>
            </div>
            
            <div class="header-actions">
                <div class="user-menu">
                    <button class="user-menu-btn" onclick="toggleUserMenu()" aria-label="User menu">
                        <i class="fas fa-user-circle"></i>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <div class="user-info">
                            <p><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></p>
                            <small><?php echo isCoordinator() ? 'Coordinator' : 'Admin'; ?></small>
                        </div>
                        <a href="profile.php">
                            <i class="fas fa-user"></i> Profile
                        </a>
                        <a href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function toggleUserMenu() {
                const dropdown = document.getElementById('userDropdown');
                dropdown.classList.toggle('active');

                // Close dropdown when clicking outside
                document.addEventListener('click', function closeDropdown(e) {
                    if (!e.target.closest('.user-menu')) {
                        dropdown.classList.remove('active');
                        document.removeEventListener('click', closeDropdown);
                    }
                });
            }

            // Add smooth transition for fixed header on scroll
            let lastScroll = 0;
            window.addEventListener('scroll', () => {
                const topNav = document.querySelector('.top-nav');
                const currentScroll = window.pageYOffset;

                if (currentScroll <= 0) {
                    topNav.style.boxShadow = '0 2px 4px rgba(0,0,0,0.04)';
                    return;
                }

                if (currentScroll > lastScroll) {
                    // Scrolling down
                    topNav.style.transform = 'translateY(-100%)';
                } else {
                    // Scrolling up
                    topNav.style.transform = 'translateY(0)';
                    topNav.style.boxShadow = '0 4px 6px -1px rgba(0,0,0,0.1)';
                }

                lastScroll = currentScroll;
            });
        </script>

        <div class="content-container">
