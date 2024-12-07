<?php
require_once 'includes/session_manager.php';
initializeSession();
requireAuth();

$program = getCurrentProgram();

// Add CSRF protection
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header('HTTP/1.1 403 Forbidden');
        exit;
    }
}

function getExcelFiles($userProgram = null) {
    $files = [];
    $dir = 'concour/';
    
    if (!file_exists($dir)) {
        return $files;
    }
    
    if ($userProgram) {
        // bnsba n coordinateur aykon kitl3 lih ghir fichier d filier dyalo
        $pattern = $dir . strtolower($userProgram) . '_*.xlsx';
        $files = glob($pattern);
    } else {
        // ama superadmin, aychof gae fichiers
        $files = glob($dir . '*.xlsx');
    }
    
    return array_map(function($file) {
        return [
            'name' => basename($file),
            'modified' => date('Y-m-d', filemtime($file))
        ];
    }, $files);
}

$files = getExcelFiles(isCoordinator() ? $program : null);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excel Files Management</title>
    <link rel="icon" href="https://fstt.ac.ma/Portail2023/wp-content/uploads/2023/03/Untitled-3-300x300.png" sizes="192x192">
    <link rel="stylesheet" href="style2.css">
    <link rel="stylesheet" href="style.css">
    <meta name="csrf-token" content="<?php echo $_SESSION['csrf_token']; ?>">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="main-content" style="margin-left: 50px; width:auto">
        
        <section class="table-section">
            <h3>Excel Files List</h3>
            <div id="loading" style="display: none;">Loading...</div>
            <div id="fileList">
                <?php if (empty($files)): ?>
                    <p>No files found</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>File Name</th>
                                <th>Last Modified</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($files as $file): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($file['name']); ?></td>
                                <td><?php echo htmlspecialchars($file['modified']); ?></td>
                                <td>
                                    <a href="concour/<?php echo htmlspecialchars($file['name']); ?>" 
                                       download 
                                       class="download-btn"
                                       style="background: #003366; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; margin-right: 10px;">
                                        Download
                                    </a>
                                    <a href="process_excel.php?file=<?php echo urlencode($file['name']); ?>" 
                                       class="process-btn"
                                       style="background: #28a745; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px;">
                                        Process File
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </section>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>