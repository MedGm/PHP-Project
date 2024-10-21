    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" href="https://fstt.ac.ma/Portail2023/wp-content/uploads/2023/03/Untitled-3-300x300.png" sizes="192x192">
        <link rel="stylesheet" href="style.css">
        <title>Modify Student</title>
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
            <div class="modify">
                <h1>Modify Student</h1>
                <input type="hidden" name="id" value="<?php echo $_GET['id'] ?? ''; ?>">
                <input type="text" name="fullname" placeholder="Fullname" value="<?php echo isset($_GET['fullname']) ? htmlspecialchars($_GET['fullname']) : ''; ?>">
                <input type="text" name="email" placeholder="Email" value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>">
                <select name ="filiere" id="filiere" value="<?php echo isset($_GET['filiere']) ? htmlspecialchars($_GET['filiere']) : ''; ?>">
                    <option value="LSI">Logiciels et systemes intelligents</option>
                    <option value="GI">Genie industriel</option>
                    <option value="GEO">Geoinformation</option>
                    <option value="GEMI">Genie Electrique et Management industriel</option>
                    <option value="GA">Genie Agroalimentaire</option>
                </select>
                <input type="submit" name="submit" value="modify">
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
        die("Connection failed: {$conn->connect_error}");
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(isset($_POST['id']) && isset($_POST['fullname']) && isset($_POST['email']) && isset($_POST['filiere'])) {
            $id = $_POST['id'];
            $fullname = $_POST['fullname'];
            $email = $_POST['email'];
            $filiere = $_POST['filiere'];

            //modifier les informations de l'etudiant
            
            $stmt = $conn->prepare("UPDATE student SET fullname = ?, email = ?, filiere = ? WHERE id = ?");
            $stmt->bind_param('sssi', $fullname, $email, $filiere, $id);

            if ($stmt->execute()) {
                header("Location: professor.php");
                exit();
            } else {
                echo "Error: {$stmt->error}";
            }

            $stmt->close();
        }
    }
    $conn->close();
    ?>
