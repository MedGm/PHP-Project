<?php
$dsn = 'mysql:host=localhost;dbname=students';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $filiere = $_POST['filiere'];

    $sql = 'INSERT INTO student (fullname, email, filiere) VALUES (:fullname, :email, :filiere)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['fullname' => $fullname, 'email' => $email, 'filiere' => $filiere]);

    echo '<p style="color: green; font-weight: bold; 
    font-size: 1em; position: absolute;
    transform: translateY(140px);
    ">Student added successfully!</p>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link rel="stylesheet" href="addstudent.css">
</head>
<body>
    <div class="return">
    <input type="button" value="return" onclick="window.location.href='studentlist.php'">
    </div>
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
    <div class="container">
        <h2 >Add Student</h2>
        <form method="post" action="addstudent.php">
            <div>
                <label for="fullname">Full Name</label>
                <input type="text" class="form-control" id="fullname" name="fullname" required>
            </div>
            <div>
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div>
                <label for="filiere">Filiere</label>
                <input type="text" id="filiere" name="filiere" required>
            </div>
            <div class="btn">
            <button type="submit'">Add Student</button>
            </div>
        </form>
    </div>
</body>
</html>