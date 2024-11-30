<?php
require_once 'includes/session_manager.php';
require_once 'includes/config.php';
initializeSession();

//ghir superdamin li 3ndo l7a9
if (!isSuperAdmin()) {
    header('Location: dashboard.php');
    exit;
}

requireAuth();

$program = getCurrentProgram();
$whereClause = isCoordinator() ? "WHERE classement = :program" : "";

try {
    $pdo = Database::getInstance();
    
    if (isCoordinator()) {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total,
                   SUM(CASE WHEN s.genre = 'F' THEN 1 ELSE 0 END) as female,
                   SUM(CASE WHEN s.genre = 'M' THEN 1 ELSE 0 END) as male
            FROM student s
            JOIN " . (in_array($program, ['SE', 'AISD', 'ITBD', 'GC', 'GE', 'MMSD']) ? 'master' : 'cycle') . " p
            ON s.email = p.email
            WHERE p.classement = :program
        ");
        $stmt->execute(['program' => $program]);
    } else {
        // hadi d superdamin
        $stats = [];
        
        // Total student counts
        $stats['master_students'] = $pdo->query("SELECT COUNT(*) FROM master")->fetchColumn();
        $stats['cycle_students'] = $pdo->query("SELECT COUNT(*) FROM cycle")->fetchColumn();
        $stats['total_students'] = $pdo->query("SELECT COUNT(*) FROM student")->fetchColumn();
        $stats['incomplete_registration'] = abs($stats['total_students'] - ($stats['master_students'] + $stats['cycle_students']));

        // Master programs distribution
        $master_programs = $pdo->query("
            SELECT classement as filiere, COUNT(*) as count 
            FROM master 
            GROUP BY classement 
            ORDER BY count DESC
        ")->fetchAll();

        // Cycle programs distribution
        $cycle_programs = $pdo->query("
            SELECT classement as filiere, COUNT(*) as count 
            FROM cycle 
            GROUP BY classement 
            ORDER BY count DESC
        ")->fetchAll();

        // Diplomas distribution for Master
        $master_diplomas = $pdo->query("
            SELECT classement, COUNT(*) as count 
            FROM master 
            GROUP BY classement
        ")->fetchAll();

        // Diplomas distribution for Cycle
        $cycle_diplomas = $pdo->query("
            SELECT filiere as classement, COUNT(*) as count 
            FROM cycle 
            GROUP BY filiere
        ")->fetchAll();

        // Grades distribution
        $grades_distribution = $pdo->query("
            SELECT 
                'Master' as program_type,
                COUNT(CASE 
                    WHEN (moyen2 IS NOT NULL AND (moyen1 + moyen2)/2 >= 12 AND (moyen1 + moyen2)/2 < 14) 
                         OR (moyen2 IS NULL AND moyen1 >= 12 AND moyen1 < 14)
                    THEN 1 END) as count_12_14,
                COUNT(CASE 
                    WHEN (moyen2 IS NOT NULL AND (moyen1 + moyen2)/2 >= 14 AND (moyen1 + moyen2)/2 < 16)
                         OR (moyen2 IS NULL AND moyen1 >= 14 AND moyen1 < 16)
                    THEN 1 END) as count_14_16,
                COUNT(CASE 
                    WHEN (moyen2 IS NOT NULL AND (moyen1 + moyen2)/2 >= 16 AND (moyen1 + moyen2)/2 <= 20)
                         OR (moyen2 IS NULL AND moyen1 >= 16 AND moyen1 <= 20)
                    THEN 1 END) as count_16_18,
                COUNT(*) as total_students
            FROM master
            UNION ALL
            SELECT 
                'Cycle' as program_type,
                COUNT(CASE WHEN moyen1 >= 12 AND moyen1 < 14 THEN 1 END) as count_12_14,
                COUNT(CASE WHEN moyen1 >= 14 AND moyen1 < 16 THEN 1 END) as count_14_16,
                COUNT(CASE WHEN moyen1 >= 16 AND moyen1 <= 20 THEN 1 END) as count_16_18,
                COUNT(*) as total_students
            FROM cycle
        ")->fetchAll();
    }
} catch (PDOException $e) {
    error_log($e->getMessage());
    die('Database error occurred');
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques</title>
    <link rel="stylesheet" href="style2.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .main-content {
            padding: 20px;
            margin-left: 125px;
            margin-right: 125px;
            background: #f5f6fa;
            
        }
        
        .charts-wrapper {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
            padding: 20px;
        }

        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            margin: 0;
            transition: transform 0.3s ease;
        }

        .chart-container:hover {
            transform: translateY(-5px);
        }

        .chart-container.full-width {
            grid-column: 1 / -1;
        }

        .chart-title {
            text-align: center;
            margin-bottom: 15px;
            color: #2c3e50;
            font-weight: bold;
        }

        .dark-theme {
            background: #2c3e50;
            color: white;
        }

        @media (max-width: 1200px) {
            .charts-wrapper {
                grid-template-columns: 1fr;
            }
        }

        .charts-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            padding: 20px;
        }
        
        .cycle-section, .master-section {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }
        
        .grades-table {
            margin-top: 25px;
            width: 100%;
            border-collapse: collapse;
        }
        
        .grades-table th, .grades-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        
        .grades-table th {
            background-color: #f5f6fa;
        }

        .registration-overview {
            width: 400px;  
            margin: 0 auto;
            padding: 15px;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="main-content" style="margin-left: 100px;">
        <h1 class="chart-title">Tableau de Bord Statistique</h1>
        
        <div class="registration-overview">
            <div class="chart-container">
                <canvas id="registrationChart"></canvas>
            </div>
        </div>

        <div class="charts-grid">
            <!-- Cycle Section -->
            <div class="cycle-section">
                <div class="chart-container">
                    <canvas id="cycleDiplomasChart"></canvas>
                </div>
                <div class="chart-container">
                    <canvas id="cycleProgramsChart"></canvas>
                </div>
            </div>

            <!-- Master Section -->
            <div class="master-section">
                <div class="chart-container">
                    <canvas id="masterDiplomasChart"></canvas>
                </div>
                <div class="chart-container">
                    <canvas id="masterProgramsChart"></canvas>
                </div>
            </div>
        </div>

        <div class="chart-container full-width">
            <h3>Distribution des Moyennes</h3>
            <table class="grades-table">
                <thead>
                    <tr>
                        <th>Programme</th>
                        <th>12-14</th>
                        <th>14-16</th>
                        <th>16-18</th>
                        <th>Total Étudiants</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($grades_distribution as $row): ?>
                    <tr>
                        <td><?php echo $row['program_type']; ?></td>
                        <td><?php echo $row['count_12_14'] ?? '0'; ?></td>
                        <td><?php echo $row['count_14_16'] ?? '0'; ?></td>
                        <td><?php echo $row['count_16_18'] ?? '0'; ?></td>
                        <td><?php echo $row['total_students']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        font: {
                            size: 12
                        }
                    }
                },
                title: {
                    display: true,
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    padding: 20
                }
            }
        };

        const themeColors = {
            primary: '#84c1ff',     
            secondary: '#4a90e2',   
            light: '#e6f3ff',      
            dark: '#003366',       
            hover: '#4BC0C0'       
        };

        // Distribution des Inscriptions
        new Chart(document.getElementById('registrationChart').getContext('2d'), {
            type: 'pie',
            data: {
                labels: ['Master', 'Cycle Ingénieur', 'Inscriptions Incomplètes'],
                datasets: [{
                    data: [
                        <?php echo $stats['master_students']; ?>,
                        <?php echo $stats['cycle_students']; ?>,
                        <?php echo $stats['incomplete_registration']; ?>
                    ],
                    backgroundColor: [themeColors.primary, themeColors.secondary, themeColors.light],
                    borderWidth: 2
                }]
            },
            options: {
                ...commonOptions,
                maintainAspectRatio: true,
                responsive: true,
                plugins: {
                    ...commonOptions.plugins,
                    title: {
                        ...commonOptions.plugins.title,
                        text: 'Distribution des Inscriptions'
                    }
                }
            }
        });

        // Distribution des diplômes (Cycle)
        new Chart(document.getElementById('cycleDiplomasChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_column($cycle_diplomas, 'classement')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($cycle_diplomas, 'count')); ?>,
                    backgroundColor: [themeColors.primary, themeColors.secondary, themeColors.dark, themeColors.light]
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    title: {
                        ...commonOptions.plugins.title,
                        text: 'Distribution des Diplômes obtenus (Cycle)'
                    }
                }
            }
        });

        // Filières populaires - Cycle
        new Chart(document.getElementById('cycleProgramsChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($cycle_programs, 'filiere')); ?>,
                datasets: [{
                    label: 'Nombre d\'étudiants',
                    data: <?php echo json_encode(array_column($cycle_programs, 'count')); ?>,
                    backgroundColor: themeColors.primary
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    title: {
                        ...commonOptions.plugins.title,
                        text: 'Filières les Plus Choisies - Cycle'
                    }
                }
            }
        });

        // Filières populaires - Master
        new Chart(document.getElementById('masterProgramsChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($master_programs, 'filiere')); ?>,
                datasets: [{
                    label: 'Nombre d\'étudiants',
                    data: <?php echo json_encode(array_column($master_programs, 'count')); ?>,
                    backgroundColor: themeColors.secondary
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    title: {
                        ...commonOptions.plugins.title,
                        text: 'Filières les Plus Choisies - Master'
                    }
                }
            }
        });

        // Replace the masterDiplomasChart JavaScript code
        new Chart(document.getElementById('masterDiplomasChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_column($master_diplomas, 'classement')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($master_diplomas, 'count')); ?>,
                    backgroundColor: [themeColors.primary, themeColors.secondary, themeColors.dark, themeColors.light]
                }]
            },
            options: {
                ...commonOptions,
                plugins: {
                    ...commonOptions.plugins,
                    title: {
                        ...commonOptions.plugins.title,
                        text: 'Distribution des Masters par Spécialité'
                    }
                }
            }
        });
    </script>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
