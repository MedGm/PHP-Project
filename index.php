<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Login Page</title>
    <link rel="stylesheet" href="formulaire.css">
    <link rel="icon" href="https://fstt.ac.ma/Portail2023/wp-content/uploads/2023/03/Untitled-3-300x300.png" sizes="192x192">
</head>
<body>
    <form method="POST" action="">
    <div class="form-container">
    <h1>Student List</h1>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br>
        <input type="submit" value="Submit">
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

    <div class="students">
        <?php
        session_start();

        if (isset($_POST['username']) && isset($_POST['password'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];
            if (($username == 'admin' && $password == 'admin') || ($username == 'aziz mahboub')) {
                header('Location: studentlist.php');
                exit;
        }else{
            echo '<p style="color: white; 
            font-weight: bold; 
            font-size: 1em; 
            background-color: red;
            position: absolute;
            bottom: 140px;
            transform: translateX(-235px);
            padding: 10px 5px;
            border-radius: 8px;">
            
            Login Failed
            
            </p>';
        }
    }
        ?>
    </div>
</body>
</html>