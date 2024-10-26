<?php
$dsn = 'mysql:host=localhost;dbname=students';
$user = 'root';
$pass = '';
try {
    // Create a new PDO instance
    $pdo = new PDO($dsn, $user, $pass);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get the form data
        $id = $_POST['id'];
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $filiere = $_POST['filiere'];

        // Update the student details
        $sql = "UPDATE student SET fullname = :fullname, email = :email, filiere = :filiere WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':filiere', $filiere);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Redirect to the student list page
        header('Location: studentlist.php');
        exit;
    } else {
        // Get the student details based on the ID
        $id = $_GET['id'];
        $sql = "SELECT * FROM student WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $student = $stmt->fetch(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Student</title>
    <link rel="stylesheet" href="modify.css">
</head>
<body>
    <form method="POST" action="">
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
        <div class="form-container">
        <h1>Modify Student</h1>
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($student['id']); ?>">
        <label for="fullname">Fullname:</label>
        <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($student['fullname']); ?>" required><br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required><br>
        <label for="filiere">Filiere:</label>
        <input type="text" id="filiere" name="filiere" value="<?php echo htmlspecialchars($student['filiere']); ?>" required><br>
        <input type="submit" value="Update">
        </div>
    </form>
</body>
</html> 