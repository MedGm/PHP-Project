<?php
require_once 'includes/session_manager.php';
require_once 'includes/config.php';
initializeSession();

if (!isAuthenticated() || !isCoordinator()) {
    header('Location: admin.php');
    exit;
}

$program = getCurrentProgram();
if (!$program) {
    header('Location: admin.php');
    exit;
}

$_SESSION['dashboard_type'] = 'coordinator';

// Add strict coordinator check
if (!isCoordinator()) {
    header('Location: logout.php');
    exit;
}

// 3la wd maywq3ch problem wana kandor fles pages akhrin
$_SESSION['dashboard_type'] = 'coordinator';
$program = $_SESSION['program'];

$program = getCurrentProgram();


$whereClause = "WHERE classement = :program";


try {
    $pdo = Database::getInstance();
    $isMaster = in_array($program, ['SE', 'AISD', 'ITBD', 'GC', 'GE', 'MMSD']);
    $table = $isMaster ? 'master' : 'cycle';
    
    // Get total students count
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM $table 
        WHERE classement = :program
    ");
    $stmt->execute(['program' => $program]);
    $stats['program_students'] = $stmt->fetchColumn();

    // Get gender distribution
    $stmt = $pdo->prepare("
        SELECT s.genre, COUNT(*) as count
        FROM student s
        JOIN $table t ON s.email = t.email
        WHERE t.classement = :program
        GROUP BY s.genre
    ");
    $stmt->execute(['program' => $program]);
    $genderStats = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    $stats['female_students'] = $genderStats['F'] ?? 0;
    $stats['male_students'] = $genderStats['M'] ?? 0;

    // Get diploma distribution for cycle programs
    if (!$isMaster) {
        $stmt = $pdo->prepare("
            SELECT filiere as diplome, COUNT(*) as count
            FROM cycle
            WHERE classement = :program
            GROUP BY filiere
        ");
        $stmt->execute(['program' => $program]);
        $stats['diploma_distribution'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // chkon li tsjlo lkhriyin
    $stmt = $pdo->prepare("
        SELECT 
            s.id,
            s.cne,
            s.fullname,
            s.email,
            s.ddn,
            s.genre,
            t." . ($isMaster ? "moyen2" : "moyen1") . " as average,
            t.filiere as diplome
        FROM student s
        JOIN $table t ON s.email = t.email
        WHERE t.classement = :program
        ORDER BY t.id DESC
        LIMIT 10
    ");
    $stmt->execute(['program' => $program]);
    $recent_students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // grade distribution
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(CASE WHEN " . ($isMaster ? "moyen2" : "moyen1") . " BETWEEN 12 AND 13.99 THEN 1 END) as range_12_14,
            COUNT(CASE WHEN " . ($isMaster ? "moyen2" : "moyen1") . " BETWEEN 14 AND 15.99 THEN 1 END) as range_14_16,
            COUNT(CASE WHEN " . ($isMaster ? "moyen2" : "moyen1") . " >= 16 THEN 1 END) as range_16_plus
        FROM $table
        WHERE classement = :program
    ");
    $stmt->execute(['program' => $program]);
    $stats['grade_distribution'] = $stmt->fetch();

} catch (PDOException $e) {
    error_log($e->getMessage());
    die('Database error occurred');
}

$programNames = [
    'LSI' => 'Logiciels et Systèmes Intelligents',
    'GI' => 'Génie Industriel',
    'GEO' => 'Géoinformation',
    'GEMI' => 'Génie Electrique et Management Industriel',
    'GA' => 'Génie Agroalimentaire',
    'SE' => 'Sciences d\'Environnement',
    'AISD' => 'Intelligence Artificielle et Sciences de Données',
    'ITBD' => 'IT et Big Data',
    'GC' => 'Génie Civil',
    'GE' => 'Génie Energétique',
    'MMSD' => 'Modélisation Mathématique et Science de Données'
];

include 'includes/header.php';
?>

<div class="main-content" style="margin-left: 50px; width:auto">
    <header>
        <h1><?php echo $programNames[$program] ?></h1>
    </header>

    <section class="stats">
        <div class="stat-box">Total Students <span><?php echo $stats['program_students']; ?></span></div>
        <div class="stat-box">Female Students <span><?php echo $stats['female_students']; ?></span></div>
        <div class="stat-box">Male Students <span><?php echo $stats['male_students']; ?></span></div>
        <?php if (!$isMaster): ?>
            <?php foreach ($stats['diploma_distribution'] ?? [] as $diploma): ?>
                <div class="stat-box"><?php echo $diploma['diplome']; ?> <span><?php echo $diploma['count']; ?></span></div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

    <section class="grade-distribution">
        <h3>Grade Distribution</h3>
        <div class="grade-stats">
            <div class="grade-box">
                12-14: <span><?php echo $stats['grade_distribution']['range_12_14']; ?></span>
            </div>
            <div class="grade-box">
                14-16: <span><?php echo $stats['grade_distribution']['range_14_16']; ?></span>
            </div>
            <div class="grade-box">
                16+: <span><?php echo $stats['grade_distribution']['range_16_plus']; ?></span>
            </div>
        </div>
    </section>

    <section class="table-section">
        <h3>Recent Registrations</h3>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>CNE</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Birth Date</th>
                    <th>Gender</th>
                    <th>Average</th>
                    <?php if (!$isMaster): ?>
                        <th>Diploma</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recent_students as $index => $student): ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo htmlspecialchars($student['cne']); ?></td>
                    <td><?php echo htmlspecialchars($student['fullname']); ?></td>
                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                    <td><?php echo htmlspecialchars($student['ddn']); ?></td>
                    <td><?php echo htmlspecialchars($student['genre']); ?></td>
                    <td><?php echo htmlspecialchars($student['average']); ?></td>
                    <?php if (!$isMaster): ?>
                        <td><?php echo htmlspecialchars($student['diplome']); ?></td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</div>

<style>
.grade-distribution {
    margin: 20px 0;
    padding: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.grade-stats {
    display: flex;
    justify-content: space-around;
    margin-top: 15px;
}

.grade-box {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 6px;
    text-align: center;
    min-width: 120px;
}

.grade-box span {
    display: block;
    font-size: 1.5em;
    font-weight: bold;
    color: #003366;
    margin-top: 5px;
}

.table-section {
    margin-top: 20px;
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

th, td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

th {
    background-color: #f8f9fa;
    font-weight: bold;
}

tr:hover {
    background-color: #f5f5f5;
}

.stat-box {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
    min-width: 150px;
}

.stat-box span {
    display: block;
    font-size: 1.8em;
    font-weight: bold;
    color: #003366;
    margin-top: 10px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[type="search"]');
    const tableRows = document.querySelectorAll('tbody tr');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }

    document.querySelectorAll('th').forEach(header => {
        header.addEventListener('click', function() {
            const table = this.closest('table');
            const index = Array.from(this.parentElement.children).indexOf(this);
            const isNumeric = this.classList.contains('numeric');
            
            sortTable(table, index, isNumeric);
        });
    });
});

function sortTable(table, column, isNumeric) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const direction = table.dataset.sortDir === 'asc' ? -1 : 1;

    rows.sort((a, b) => {
        let aValue = a.children[column].textContent.trim();
        let bValue = b.children[column].textContent.trim();
        
        if (isNumeric) {
            aValue = parseFloat(aValue);
            bValue = parseFloat(bValue);
        }
        
        return aValue > bValue ? direction : -direction;
    });

    table.dataset.sortDir = direction === 1 ? 'asc' : 'desc';
    rows.forEach(row => tbody.appendChild(row));
}
</script>

<?php include 'includes/footer.php'; ?>