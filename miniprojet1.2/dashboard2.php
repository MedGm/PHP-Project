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
    <div class="main-content bg-gray-50 min-h-screen" style="margin-left: 50px; width:auto">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 p-4">
            <div class="bg-white rounded-lg shadow-sm p-6 transform hover:scale-105 transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Students</p>
                        <h3 class="text-2xl font-bold"><?php echo $stats['total_students']; ?></h3>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <i class="bi bi-people text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 transform hover:scale-105 transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Master Students</p>
                        <h3 class="text-2xl font-bold"><?php echo $stats['master_students']; ?></h3>
                    </div>
                    <div class="bg-purple-100 rounded-full p-3">
                        <i class="bi bi-mortarboard text-purple-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 transform hover:scale-105 transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Engineering Students</p>
                        <h3 class="text-2xl font-bold"><?php echo $stats['cycle_students']; ?></h3>
                    </div>
                    <div class="bg-yellow-100 rounded-full p-3">
                        <i class="bi bi-gear text-yellow-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 transform hover:scale-105 transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Female Students</p>
                        <h3 class="text-2xl font-bold"><?php echo $stats['female_students']; ?></h3>
                    </div>
                    <div class="bg-pink-100 rounded-full p-3">
                        <i class="bi bi-gender-female text-pink-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 transform hover:scale-105 transition-all">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Male Students</p>
                        <h3 class="text-2xl font-bold"><?php echo $stats['male_students']; ?></h3>
                    </div>
                    <div class="bg-indigo-100 rounded-full p-3">
                        <i class="bi bi-gender-male text-indigo-600"></i>
                    </div>
                </div>
            </div>
        </div>

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