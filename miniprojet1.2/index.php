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
            <h1>Login</h1>
            <div class="form">
                <select name="role" id="role" style="pointer-events: none; cursor: default;">
                    <option value="student">Student</option>
                </select>
                <input type="text" name="email" placeholder="Email (For student)" >
                <div class="button-container">
                    <button type="submit" name="submit">Submit</button>
                </div>
            </div>
        </div>
        <footer style="text-align: center; position: absolute; bottom: 0; width: 100%; background-color: #ccc;"> 
            © Made by EL GORRIM MOHAMED. LSI24/25 
            <button type="button" name="admincon" onclick="toggleAdminStudent()">connecter comme admin</button> 
        </footer>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const savedView = localStorage.getItem('currentView');
            if (savedView === 'admin') {
                applyAdminView();
            }
        });

        function applyAdminView() {
            const role = document.getElementById('role');
            const emailInput = document.querySelector('input[name="email"]');
            const toggleButton = document.querySelector('button[name="admincon"]');
            
            role.innerHTML = '<option value="superadmin">Admin</option><option value="chef">Coordinateur</option>';
            role.removeAttribute('style');
            emailInput.style.display = 'none';
            toggleButton.textContent = 'connecter comme etudiant';
        }

        function applyStudentView() {
            const role = document.getElementById('role');
            const emailInput = document.querySelector('input[name="email"]');
            const toggleButton = document.querySelector('button[name="admincon"]');
            
            role.innerHTML = '<option value="student">Student</option>';
            role.style.cssText = 'pointer-events: none; cursor: default;';
            emailInput.style.display = 'block';
            toggleButton.textContent = 'connecter comme admin';
        }

        function toggleAdminStudent() {
            const role = document.getElementById('role');
            
            if (role.innerHTML.includes('Student')) {
                applyAdminView();
                localStorage.setItem('currentView', 'admin');
            } else {
                applyStudentView();
                localStorage.setItem('currentView', 'student');
            }
        }
    </script>
</body>
</html>

<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    require 'vendor/autoload.php';
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])){
        if(isset($_POST["role"])){
            $role = $_POST["role"];
            if($role == "student"){
                $email = $_POST["email"];

                // karverifier wach email eandzo forma s7i7a
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
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
                    
                    //kanverifier wach email kayn f database
                    if ($result->num_rows > 0) {
                        //nchofoh wach f master
                        $sql_master = "SELECT * FROM master WHERE email = '$email'";
                        $result_master = $conn->query($sql_master);
                        
                        // wla f cycle
                        $sql_cycle = "SELECT * FROM cycle WHERE email = '$email'";
                        $result_cycle = $conn->query($sql_cycle);
                        
                        if ($result_master->num_rows > 0 || $result_cycle->num_rows > 0) {
                            // lqinah fchi wehda fihom
                            session_start();
                            $_SESSION['email'] = $email; // The email from login form
                            $_SESSION['authenticated'] = true;
                            header("location:download.php");
                        } else {
                            // lqinah f student
                            $student_data = $result->fetch_assoc();
                            $selected_program = isset($student_data['selected_program']) ? $student_data['selected_program'] : '';
                            
                            session_start();
                            $_SESSION['email'] = $email;
                            
                            if ($selected_program == 'master') {
                                header("location:mst.php");
                            } else if ($selected_program == 'ci') {
                                header("location:ci.php");
                            } else {
                                header("location:student.php?=". urlencode($email));
                            }
                            exit();
                        }
                    } else { 
                        try {
                        $mail = new PHPMailer(true);
        
                        // l3ibat dyal server SMTP
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'confirmationfsttinscri@gmail.com'; // lkhsim li ghaybqa ysrd les emails
                        $mail->Password = 'wwsz xtux pumu jkcm';
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;
                        $to = $email;
                        $link = 'http://localhost/miniprojet1.2/student.php?email=' . urlencode($email);

                        //  hna ghadi yban chkon ghaysyft email
                        $mail->setFrom('confirmationfsttinscri@gmail.com', 'FSTT Registration');
                        $mail->addAddress($email);

                        // dakchi li ghaytla3lo f message
                        $mail->isHTML(true);
                        $mail->Subject = "Welcome to FSTT";
                        $mail->Body = "
                        <div style='font-family: Arial, sans-serif; text-align: center;'>
                            <img src='https://fstt.ac.ma/Portail2023/wp-content/uploads/2023/03/Untitled-3-300x300.png' alt='FSTT Logo' style='width: 100px; height: 100px;'>
                            <h2>Welcome to FSTT</h2>
                            <p style='font-size: 1.2em;'>You have started your registration for the first year engineering cycle at FSTT. To complete your registration, please click the link below:</p>
                            <a href='{$link}' style='display: inline-block; padding: 10px 20px; margin: 20px 0; background-color: #007BFF; color: #fff; text-decoration: none; border-radius: 5px;'>Complete Registration</a>
                            <p style='font-size: 0.9em; color: #555;'>If you did not initiate this registration, please ignore this email or contact our support team.</p>
                        </div>";

                        $mail->send();
                        echo "<p style='color: black; 
                                font-weight: bold; 
                                font-size: 1em; 
                                text-align: center;'>
                                An email has been sent to $email. Please click the link in the email to complete your registration.</p>";
                            } catch (Exception $e) {
                                echo "<p style='color: red; 
                                font-weight: bold; 
                                font-size: 1em; 
                                text-align: center;'>
                                Message could not be sent. Mailer Error: {$mail->ErrorInfo}</p>";
                        }
                    }
                    $conn->close();
                } else if(empty($email)){
                    echo "<p style='color: black; 
                            font-weight: bold; 
                            font-size: 1em; 
                            text-align: center;'>
                            Please Enter a valid email address.</p>";
                }
                else {
                    echo "<p style='color: black; 
                            font-weight: bold; 
                            font-size: 1em; 
                            text-align: center;'>
                            Invalid email format. Please use a valid email address.</p>";
                }
            } else if($role == "superadmin" || $role == "chef"){
                header("location:admin.php?role=" . urlencode($role));
                exit();
            }
        }
    }
?>