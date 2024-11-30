<?php
require_once 'includes/session_manager.php';
require_once 'includes/config.php';
initializeSession();
requireAuth();

if (!isSuperAdmin()) {
    header('Location: logout.php');
    exit;
}

$_SESSION['dashboard_type'] = 'admin';

$host = 'localhost';
$db = 'students';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $stats = [];
    $stats['master_students'] = $pdo->query("SELECT COUNT(*) FROM master")->fetchColumn();
    $stats['cycle_students'] = $pdo->query("SELECT COUNT(*) FROM cycle")->fetchColumn();

    $stats['total_students'] = $stats['master_students'] + $stats['cycle_students'];

    $stats['female_students'] = $pdo->query("SELECT COUNT(*) FROM student WHERE genre = 'F'")->fetchColumn();
    $stats['male_students'] = $pdo->query("SELECT COUNT(*) FROM student WHERE genre = 'M'")->fetchColumn();

    // akher 5 students
    $stmt = $pdo->query("SELECT 
        id,
        CNE as cne,
        email,
        fullname,
        ddn,
        genre
        FROM student 
        ORDER BY id DESC 
        LIMIT 5");
    $recent_students = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style2.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="main-content" style="margin-left: 50px; width:auto">
        <section class="stats">
            <div class="stat-box">Total Students <span><?php echo $stats['total_students']; ?></span></div>
            <div class="stat-box">Master Students <span><?php echo $stats['master_students']; ?></span></div>
            <div class="stat-box">Engineering Cycle <span><?php echo $stats['cycle_students']; ?></span></div>
            <div class="stat-box">Female Students <span><?php echo $stats['female_students']; ?></span></div>
            <div class="stat-box">Male Students <span><?php echo $stats['male_students']; ?></span></div>
        </section>
        <section class="table-section">
            <h3>Recent Registrations</h3>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>CNE</th>
                        <th>FullName</th>
                        <th>Email</th>
                        <th>Birth Date</th>
                        <th>Genre</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_students as $index => $student): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($student['cne'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($student['fullname'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($student['email'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($student['ddn'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($student['genre'] ?? 'N/A'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>

<?php
include 'includes/footer.php';
?>