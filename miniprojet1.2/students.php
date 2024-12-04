<?php
require_once 'includes/session_manager.php';
require_once 'includes/config.php';
require_once 'db_connection.php';
initializeSession();

if (!isAuthenticated()) {
    header('Location: admin.php');
    exit;
}

$pageTitle = isCoordinator() ? "Programme {$_SESSION['program']}" : "All Programs";


include'emailconfig.php';
// ms7 l'etudiant
if (isset($_POST['delete_student']) && isset($_POST['table'])) {
    try {
        $pdo = Database::getInstance();
        $id = filter_input(INPUT_POST, 'student_id', FILTER_SANITIZE_NUMBER_INT);
        $table = $_POST['table'] === 'master' ? 'master' : 'cycle';
        $email = filter_input(INPUT_POST, 'student_email', FILTER_SANITIZE_EMAIL);
        $fullname = filter_input(INPUT_POST, 'student_name', FILTER_SANITIZE_STRING);
        
        if (!$email || !$fullname) {
            throw new Exception('Invalid email or fullname');
        }
        
        // Delete the student first
        $sql = "DELETE FROM $table WHERE id = ?";
        $params = [$id];
        
        if (isCoordinator()) {
            $sql .= " AND classement = ?";
            $params[] = $_SESSION['program'];
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        // sard email
        $mail = prepareMailer();
        $mail->addAddress($email, $fullname);
        $mail->Subject = "Suppression de Candidature";
        $mail->Body = "
        <div style='font-family: Arial, sans-serif; text-align: center;'>
            <img src='https://fstt.ac.ma/Portail2023/wp-content/uploads/2023/03/Untitled-3-300x300.png' alt='FSTT Logo' style='width: 100px; height: 100px;'>
            <h2>Candidature Supprimée avec Succès</h2>
            <p style='font-size: 1.2em;'>Cher(e) Candidat(e),</p>
            <p style='font-size: 1.1em;'>Votre candidature à l'UAE a été supprimée avec succès de notre système.</p>
            <p style='font-size: 1.1em;'>Si vous souhaitez postuler à nouveau dans le futur, vous devrez recommencer un nouveau processus de candidature.</p>
            <p style='font-size: 0.9em; color: #666; margin-top: 20px;'>Cordialement,<br>Administration UAE</p>
        </div>";
        $mail->send();
        
        $_SESSION['message'] = 'Student deleted successfully';
        
        header('Location: students.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Error deleting student: ' . $e->getMessage();
    } catch (Exception $e) {
        $_SESSION['error'] = 'Error deleting student: ' . $e->getMessage();
    }
}

// ms7 li maklmosh inscription
if (isset($_POST['delete_incomplete'])) {
    try {
        $pdo = Database::getInstance();
        $id = filter_input(INPUT_POST, 'student_id', FILTER_SANITIZE_NUMBER_INT);
        $email = filter_input(INPUT_POST, 'student_email', FILTER_SANITIZE_EMAIL);
        $fullname = filter_input(INPUT_POST, 'student_name', FILTER_SANITIZE_STRING);
        
        $sql = "DELETE FROM student WHERE id = ?";
        $params = [$id];
        
        if (isCoordinator()) {
            $sql .= " AND program = ?";
            $params[] = $_SESSION['program'];
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        $_SESSION['message'] = 'Incomplete registration deleted successfully';
        
        $mail = prepareMailer();
        $mail->addAddress($email, $fullname);
        $mail->Subject = "Suppression de Candidature";
        $mail->Body = "
        <div style='font-family: Arial, sans-serif; text-align: center;'>
            <img src='https://fstt.ac.ma/Portail2023/wp-content/uploads/2023/03/Untitled-3-300x300.png' alt='FSTT Logo' style='width: 100px; height: 100px;'>
            <h2>Candidature Supprimée avec Succès</h2>
            <p style='font-size: 1.2em;'>Cher(e) Candidat(e),</p>
            <p style='font-size: 1.1em;'>Votre candidature à l'UAE a été supprimée avec succès de notre système.</p>
            <p style='font-size: 1.1em;'>Si vous souhaitez postuler à nouveau dans le futur, vous devrez recommencer un nouveau processus de candidature.</p>
            <p style='font-size: 0.9em; color: #666; margin-top: 20px;'>Cordialement,<br>Administration UAE</p>
        </div>";
        $mail->send();

        header('Location: students.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Error deleting student: ' . $e->getMessage();
    }
}


if (isset($_POST['send_warning'])) {
    try {
        $pdo = Database::getInstance();
        $student_id = filter_input(INPUT_POST, 'student_id', FILTER_SANITIZE_NUMBER_INT);
        $email = filter_input(INPUT_POST, 'student_email', FILTER_SANITIZE_EMAIL);
        $fullname = filter_input(INPUT_POST, 'student_name', FILTER_SANITIZE_STRING);
        
        $mail = prepareMailer();
        $mail->addAddress($email, $fullname);
        $mail->Subject = "Warning: Incomplete Registration";
        $mail->Body = "
        <div style='font-family: Arial, sans-serif; text-align: center;'>
            <img src='https://fstt.ac.ma/Portail2023/wp-content/uploads/2023/03/Untitled-3-300x300.png' alt='FSTT Logo' style='width: 100px; height: 100px;'>
            <h2>Avertissement : Inscription Incomplète</h2>
            <p style='font-size: 1.2em;'>Cher(e) $fullname,</p>
            <p style='font-size: 1.1em; color: #dc3545;'>Votre inscription à l'UAE est incomplète.</p>
            <p style='font-size: 1.1em;'>Veuillez compléter votre inscription dans les 24 heures.</p>
            <p style='font-size: 1.1em;'><strong>Si vous ne complétez pas le processus d'inscription, votre candidature sera automatiquement supprimée.</strong></p>
            <p style='font-size: 0.9em; color: #666; margin-top: 20px;'>Cordialement,<br>Administration UAE</p>
        </div>";

        $mail->send();
        $_SESSION['message'] = 'Warning email sent successfully';
        
        header('Location: students.php');
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = 'Error sending warning: ' . $e->getMessage();
    }
}

// Handle PDF viewing
if (isset($_GET['pdf']) && isset($_GET['table'])) {
    try {
        $pdo = Database::getInstance();
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $table = $_GET['table'] === 'master' ? 'master' : 'cycle';
        
        $sql = "SELECT pdf_file, file_name FROM $table WHERE id = ?";
        $params = [$id];
        
        if (isCoordinator()) {
            $sql .= " AND classement = ?";
            $params[] = $_SESSION['program'];
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();

        if ($result && $result['pdf_file']) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . $result['file_name'] . '"');
            echo $result['pdf_file'];
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Error retrieving PDF: ' . $e->getMessage();
    }
}

function getStudents() {
    try {
        $pdo = Database::getInstance();
        $params = [];
        $whereClause = isCoordinator() ? "WHERE classement = ?" : "";
        
        if (isCoordinator()) {
            $params[] = $_SESSION['program'];
        }
        
        // Get master students with calculated average
        $masterSql = "SELECT *, 
            'master' as source,
            CASE 
                WHEN moyen2 IS NOT NULL THEN (moyen1 + moyen2) / 2 
                ELSE moyen1 
            END as average 
            FROM master $whereClause";
        $masterStmt = $pdo->prepare($masterSql);
        $masterStmt->execute($params);
        $masterStudents = $masterStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get cycle students with moyen1 as average
        $cycleSql = "SELECT *, 
            'cycle' as source,
            moyen1 as average 
            FROM cycle $whereClause";
        $cycleStmt = $pdo->prepare($cycleSql);
        $cycleStmt->execute($params);
        $cycleStudents = $cycleStmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Merge and sort results
        $allStudents = array_merge($masterStudents, $cycleStudents);
        usort($allStudents, function($a, $b) {
            return strcmp($a['fullname'], $b['fullname']);
        });
        
        return $allStudents;
    } catch(PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return [];
    }
}

function getIncompleteRegistrations() {
    try {
        $pdo = Database::getInstance();
        $sql = "SELECT s.*, s.selected_program as program FROM student s 
                LEFT JOIN master m ON s.email = m.email 
                LEFT JOIN cycle c ON s.email = c.email 
                WHERE m.email IS NULL AND c.email IS NULL";
        
        if (isCoordinator()) {
            $sql .= " AND s.selected_program = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$_SESSION['program']]);
        } else {
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return [];
    }
}

$students = getStudents();
$incompleteStudents = getIncompleteRegistrations();
$programTitle = isCoordinator() ? $_SESSION['program'] : "All Programs";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Students - <?php echo htmlspecialchars($programTitle); ?></title>
    <link rel="icon" href="https://fstt.ac.ma/Portail2023/wp-content/uploads/2023/03/Untitled-3-300x300.png" sizes="192x192">
    <link rel="stylesheet" href="style2.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .student-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .student-table th, .student-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .student-table th {
            background-color: #f5f5f5;
        }
        .student-table tr:hover {
            background-color: #f9f9f9;
        }
        .btn {
            padding: 6px 12px 0px;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            margin: 2px;
        }
        .btn-view {
            background-color: #007bff;
            color: white;
        }
        .btn-delete {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            border: none;
            cursor: pointer;
        }
        .btn-delete:hover {
            background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .tab-navigation {
            margin-bottom: 20px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
        }

        .tab-button {
            background: none;
            border: none;
            padding: 10px 20px;
            margin-right: 10px;
            color: #718096;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .tab-button.active {
            color: #2b6cb0;
            border-bottom: 2px solid #2b6cb0;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }
        .btn-warning {
            background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%) !important;
            color: #000 !important;
            border: none;
            cursor: pointer;
            font-weight: 500;
        }
        .btn-warning:hover {
            background: linear-gradient(135deg, #ffb300 0%, #ffa000 100%) !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
        }
        .list-controls {
            display: flex;
            gap: 1.5rem;
            margin: 20px 0;
            padding: 20px 0;
        }
        
        .list-controls .btn {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            text-align: center;
            border: none;
            min-width: 200px;
            color: #003366;
            font-weight: 600;
            border-left: 4px solid #3182ce;
            transition: all 0.3s ease;
        }

        .list-controls .btn:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .list-controls .btn-primary {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border-left-color: #2b6cb0;
        }

        .list-controls .btn-warning {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%) !important;
            color: #003366 !important;
            border-left-color: #ffc107;
        }

        .list-controls .btn-warning:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%) !important;
        }

        /* Update the count display */
        .btn::after {
            content: attr(data-count);
            display: block;
            font-size: 1.75rem;
            font-weight: 600;
            color: #2d3748;
            margin-top: 0.75rem;
        }
        .list-section {
            margin-top: 20px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }

        .btn-primary:hover, .btn-warning:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="main-content" style="margin-left: 50px;width:auto">
        <h1>Students Management<?php echo isCoordinator() ? " - " . htmlspecialchars($programTitle) : ""; ?></h1>
        
        <div class="list-controls">
            <button class="btn btn-primary" onclick="toggleSection('completedList')" 
                    data-count="<?php echo count($students); ?>">
                Registered Students
            </button>
            <?php if (!isCoordinator()): ?>
            <button class="btn btn-warning" onclick="toggleSection('incompleteList')" 
                    data-count="<?php echo count($incompleteStudents); ?>">
                Incomplete Registrations
            </button>
            <?php endif; ?>
        </div>

        <header>
            <form id="searchForm" onsubmit="return false;">
                <input type="text" id="searchInput" placeholder="Search for a student" onkeyup="filterStudents()">
            </form>
        </header>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="message error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- Registered Students Section -->
        <div id="completedList" class="list-section" style="display: none;">
            <h2>Registered Students</h2>
            <table class="student-table" id="studentTable">
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>CIN</th>
                        <th>CNE</th>
                        <th>Email</th>
                        <th>Average</th>
                        <?php if (!isCoordinator()): ?>
                        <th>Program</th>
                        <?php endif; ?>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['fullname']); ?></td>
                            <td><?php echo htmlspecialchars($student['cin']); ?></td>
                            <td><?php echo htmlspecialchars($student['cne']); ?></td>
                            <td><?php echo htmlspecialchars($student['email']); ?></td>
                            <td><?php echo number_format($student['average'], 2); ?></td>
                            <?php if (!isCoordinator()): ?>
                            <td><?php echo htmlspecialchars($student['classement']); ?></td>
                            <?php endif; ?>
                            <td>
                                <?php if ($student['pdf_file']): ?>
                                    <a href="?pdf=1&id=<?php echo $student['id']; ?>&table=<?php echo $student['source']; ?>" 
                                    class="btn btn-view" target="_blank">View PDF</a>
                                <?php endif; ?>
                                <form method="post" style="display: inline;" 
                                    onsubmit="return confirm('Are you sure you want to delete this student?');">
                                    <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                                    <input type="hidden" name="table" value="<?php echo $student['source']; ?>">
                                    <input type="hidden" name="student_email" value="<?php echo $student['email']; ?>">
                                    <input type="hidden" name="student_name" value="<?php echo $student['fullname']; ?>">
                                    <button type="submit" name="delete_student" class="btn btn-delete">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Incomplete Registrations Section -->
        <?php if (!isCoordinator()): ?>
        <div id="incompleteList" class="list-section" style="display: none;">
            <h2>Incomplete Registrations</h2>
            <table class="student-table" id="incompleteTable">
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>CIN</th>
                        <th>Email</th>
                        <?php if (!isCoordinator()): ?>
                        <th>Program</th>
                        <?php endif; ?>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($incompleteStudents as $student): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['fullname']); ?></td>
                        <td><?php echo htmlspecialchars($student['cin']); ?></td>
                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                        <?php if (!isCoordinator()): ?>
                        <td><?php echo htmlspecialchars($student['selected_program']); ?></td>
                        <?php endif; ?>
                        <td>
                            <form method="post" style="display: inline;" 
                                onsubmit="return confirm('Are you sure you want to delete this incomplete registration?');">
                                <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                                <button type="submit" name="delete_incomplete" class="btn btn-delete">Delete</button>
                            </form>
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="student_id" value="<?php echo $student['id']; ?>">
                                <input type="hidden" name="student_email" value="<?php echo $student['email']; ?>">
                                <input type="hidden" name="student_name" value="<?php echo $student['fullname']; ?>">
                                <button type="submit" name="send_warning" class="btn btn-warning">Send Warning</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <script>
    function filterStudents() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toLowerCase();
        const rows = document.querySelectorAll('#studentTable tbody tr');

        rows.forEach(row => {
            const cells = row.getElementsByTagName('td');
            let found = false;

            for (let j = 0; j < cells.length; j++) {
                const cell = cells[j];
                if (cell) {
                    const text = cell.textContent || cell.innerText;
                    if (text.toLowerCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
            }
            row.style.display = found ? '' : 'none';
        });
    }

    function toggleSection(sectionId) {
        const section = document.getElementById(sectionId);
        const allSections = document.querySelectorAll('.list-section');
        
        // Hide all sections first
        allSections.forEach(s => s.style.display = 'none');
        
        // Show the selected section
        section.style.display = 'block';
        
        // Update search to work with visible table only
        const searchInput = document.getElementById('searchInput');
        searchInput.value = '';
        filterStudents();
    }

    // Show completed list by default when page loads
    document.addEventListener('DOMContentLoaded', function() {
        toggleSection('completedList');
    });
    </script>

    <style>
    .bg-warning {
        background-color: #fff3cd !important;
    }
    .alert {
        padding: 12px;
        margin-bottom: 16px;
        border-radius: 4px;
    }
    .alert-info {
        background-color: #e1f0ff;
        color: #0c5460;
        border: 1px solid #bee5eb;
    }
    .btn-primary {
        background-color: #4a90e2;
        margin-right: 8px;
    }
    .text-center {
        text-align: center;
    }
    </style>
    <?php include 'includes/footer.php'; ?>
</body>
</html>

