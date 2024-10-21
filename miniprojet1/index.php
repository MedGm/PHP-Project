<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOGIN</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="https://fstt.ac.ma/Portail2023/wp-content/uploads/2023/03/Untitled-3-300x300.png" sizes="192x192">
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
        <div class="login-box">
            <h1>Select :</h1>
            <select name="role" id="role">
                <option value="student">Student</option>
                <option value="professor">Professor</option>
            </select>
            <input type="text" name="email" placeholder="Email (For student)">
            <button type="submit" name="submit">Submit</button>
        </div>
        <footer style="text-align: center; position: absolute; bottom: 0; width: 100%; background-color: #ccc;"> © Made by EL GORRIM MOHAMED. LSI24/25 </footer>
</body>
</html>

<?php
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(isset($_POST["role"])){
            $role = $_POST["role"];
            if($role == "student"){
                $email = $_POST["email"];

                // karverifier wach email kitsala b @etu.uae.ac.ma
                if ((filter_var($email, FILTER_VALIDATE_EMAIL) 
                && preg_match('/@etu\.uae\.ac\.ma$/', $email))) {
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "students";
                    $conn = new mysqli($servername, $username, $password, $dbname);
                    if($conn->connect_error){
                        die("Connection failed: " . $conn->connect_error);
                    }
                    $sql = "SELECT * FROM student WHERE email = '$email'";
                    $result = $conn->query($sql);
                    
                    //katverifier wach deja kayn f base de données
                    if ($result->num_rows > 0) {
                        header("location:student2.php" );
                    } else { 
                        header("location:student.php?email=". urlencode($email));
                    }
                    $conn->close();
                } else if(empty($email)){
                    header("location:student.php");
                }
                else {
                    echo "<p style='color: white; 
                            font-weight: bold; 
                            font-size: 1em; 
                            text-align: center;'>
                            Invalid email format. Please use an email ending with @etu.uae.ac.ma.</p>";
                }
            } else if($role == "professor"){ //en cas endna prof kandkhlo lel page dyal list directement
                header("location:professor.php");
            }
        }
    }
?>