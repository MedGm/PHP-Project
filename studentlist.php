<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire</title>
    <link rel="stylesheet" href="formulaireliste.css">
    <link rel="icon" href="https://fstt.ac.ma/Portail2023/wp-content/uploads/2023/03/Untitled-3-300x300.png" sizes="192x192">
</head>
<body>
    <form action="studentlist.php" method="get">
    <div class="students">
        <input type="button" value="Add Student" onclick="window.location.href='addstudent.php'">
        <div class="logosfst">
            <img src="fst-1024x383.png" alt="FSTTLogo">
        </div>
        <div class="unilogo">
            <img src="logo-uae-1024x297.png" alt="UniLogo">
        </div>
        <div class="barrier">
            <img src="barrier.png" alt="">
        </div>
        <div class="barrier2">
            <img src="barrier2.png" alt="">
        </div>
    </div>
    </form>
    <div class="studentslist">
    <h1>Student List</h1>
    <div class="student-header">
        <div>Fullname</div>
        <div>Email</div>
        <div>Filiere</div>
    </div>
        <?php
        include('database.php');
        try {
            $sql = "SELECT * FROM student";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($result) > 0) {
            foreach ($result as $row) {
                echo '<div class="student-row">';
            echo '<div>' . htmlspecialchars($row['fullname']) . '</div>';
            echo '<div>' . htmlspecialchars($row['email']) . '</div>';
            echo '<div>' . htmlspecialchars($row['filiere']) . '</div>';
            echo '<div><input type="button" value="Modify" onclick="window.location.href=\'modifystudent.php?id=' . htmlspecialchars($row['id']) . '\'"></div>';
            echo '<div class="delete-btn">';
            echo '<form action="studentlist.php" method="get">';
            echo '<input type="hidden" name="delete_id" value="' . htmlspecialchars($row['id']) . '">';
            echo '<input type="submit" value="Delete">';
            echo '</form>';
            echo '</div>';
            echo '</div>';
            }
            } else {
            echo "0 results";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

        if (isset($_GET['delete_id'])) {
            $delete_id = $_GET['delete_id'];
            try {
            $sql = "DELETE FROM student WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id' => $delete_id]);
            header("Location: studentlist.php");
            exit();
            } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            }
        }
        $pdo = null;
        ?>
    </div>
</body>
</html>