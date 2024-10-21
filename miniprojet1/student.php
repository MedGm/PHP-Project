<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://fstt.ac.ma/Portail2023/wp-content/uploads/2023/03/Untitled-3-300x300.png" sizes="192x192">
    <link rel="stylesheet" href="style.css">
    <title>Add Student</title>
</head>
<body>
<ul class="logos">
    <li class="fst-logo">
        <img src="fst-1024x383.png" alt="FSTTLogo" style="width: 230px; height: 80px;">
    </li>
    <li class="uni-logo">
        <img src="logo-uae-1024x297.png" alt="UniLogo"style="width: 230px; height: 60px;">
    </li>
</ul>
    <form action="" method="post"> 
        <div class="addstudent">
            <h1>Add Student</h1>
            <input type="text" name="fullname" placeholder="Fullname">
            <input type="text" name="email" placeholder="Email" value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>">
            <select name ="filiere" id="filiere">
                <option value="LSI">Logiciels et systemes intelligents</option>
                <option value="GI">Genie industriel</option>
                <option value="GEO">Geoinformation</option>
                <option value="GEMI">Genie Electrique et Management industriel</option>
            </select>
            <input type="submit" name="submit" value="Add">
        </div>
    </form>
    <footer style="text-align: center; position: absolute; bottom: 0; width: 100%; background-color: #ccc;"> © Made by EL GORRIM MOHAMED. LSI24/25</footer>
</body>
</html>

<?php
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'students';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['fullname']) && isset($_POST['email']) && isset($_POST['filiere'])) {
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $filiere = $_POST['filiere'];
        
        if(empty($email) || empty($fullname)) {
            echo "<p style='color: white; 
                    font-weight: bold; 
                    font-size: 1em; 
                    text-align: center;'>Please do not leave any field empty.</p>";
            exit();
        }
        // karverifier wach email kitsala b @etu.uae.ac.ma
        
        if ((filter_var($email, FILTER_VALIDATE_EMAIL) 
                && preg_match('/@etu\.uae\.ac\.ma$/', $email))){
            $stmt = $conn->prepare('INSERT INTO student (fullname, email, filiere) VALUES (?, ?, ?)');
            $stmt->bind_param('sss', $fullname, $email, $filiere);

            if ($stmt->execute()) {
                echo "<p style='color: white; 
                        font-weight: bold; 
                        font-size: 1em; 
                        text-align: center;'>Student added successfully!</p>";
                header('location: student2.php');
            } else {
                echo 'Error: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "<p style='color: white; 
                    font-weight: bold; 
                    font-size: 1em; 
                    text-align: center;'>Please enter a valid email address.</p>";
        }
    }
}
$conn->close();
?>