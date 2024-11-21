<?php
session_start();
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: admin.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        header('HTTP/1.1 403 Forbidden');
        exit;
    }
}
function getExcelFiles($type) {
    $files = [];
    $dir = 'concour/';
    
    $patterns = [
        'master' => ['se_mst', 'aisd_mst', 'itbd_mst', 'gc_mst', 'ge_mst', 'mmsd_mst'],
        'engineering' => ['lsi_ci', 'gi_ci', 'geo_ci', 'gemi_ci', 'ga_ci']
    ];
    
    if (isset($patterns[$type])) {
        foreach ($patterns[$type] as $pattern) {
            $file = $dir . $pattern . '_students.xlsx';
            if (file_exists($file)) {
                $files[] = [
                    'name' => $pattern . '_students.xlsx',
                    'modified' => date('Y-m-d', filemtime($file))
                ];
            }
        }
    }
    
    return $files;
}

// methode d AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['type'])) {
    header('Content-Type: application/json');
    echo json_encode(getExcelFiles($_POST['type']));
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="https://fstt.ac.ma/Portail2023/wp-content/uploads/2023/03/Untitled-3-300x300.png">
    <style>
        .container { max-width: 800px; margin: 20px auto; padding: 20px; }
        select { width: 200px; padding: 8px; margin-bottom: 20px; }
        .file-list { list-style: none; padding: 0; }
        .file-item { 
            padding: 10px;
            margin: 5px 0;
            background: #f5f5f5;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
        }
        #loading { display: none; color: #666; }
    </style>
</head>
<body>
    <div class="container" style="height: 80vh;">
        <h1 style="color: #003366;">Excel Files List</h1>
        <select id="programType">
            <option value="">Select Program Type</option>
            <option value="master">Master</option>
            <option value="engineering">Cycle D'ingenieur</option>
        </select>
        
        <div id="loading">Loading...</div>
        <div id="fileList"></div>
    </div>

    <script>
        document.getElementById('programType').addEventListener('change', async function() {
            const loading = document.getElementById('loading');
            const fileList = document.getElementById('fileList');
            
            if (!this.value) {
                fileList.innerHTML = '';
                return;
            }

            loading.style.display = 'block';
            fileList.innerHTML = '';

            try {
                const response = await fetch('dashboard.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `type=${this.value}`
                });

                const files = await response.json();
                
                if (files.length === 0) {
                    fileList.innerHTML = '<p>No files found</p>';
                    return;
                }

                const ul = document.createElement('ul');
                ul.className = 'file-list';

                files.forEach(file => {
                    ul.innerHTML += `
                        <li class="file-item">
                            <span>${file.name}</span>
                            <span>${file.modified}</span>
                            <a href="concour/${file.name}" download class="download-btn" style="background: #003366; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px;">Download</a>
                        </li>`;
                });

                fileList.appendChild(ul);
            } catch (error) {
                fileList.innerHTML = `<p>Error loading files</p>`;
            } finally {
                loading.style.display = 'none';
            }
        });
    </script>
</body>
</html>