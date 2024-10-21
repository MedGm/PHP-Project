<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List</title>
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
    <form class="formulaire" action="" method="post">
        <div class="top-button"><input type="submit" name="logout" value="Logout" formaction="index.php"></div>
        <div class="container">
            <h1>Student List</h1>
            <?php
                session_start();
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "students";
                $conn = new mysqli($servername, $username, $password, $dbname);
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                //affichage de list des etudiants

                $sql = "SELECT * FROM student";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                        echo "<div class='student-list'>";
                        echo "<table class='styled-table'>";
                        echo "<thead>";
                        echo "<tr>";
                        echo "<th>Fullname</th>";
                        echo "<th>Email</th>";
                        echo "<th>Filiere</th>";
                        echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";

                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row["fullname"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["email"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["filiere"]) . "</td>";
                            echo "</tr>";
                        }

                        echo "</tbody>";
                        echo "</table>";
                        echo "</div>";
                    }else{
                    echo "0 results";
                }
                $conn->close();
            ?>
        </div>
    </form>
    <footer style="text-align: center; position: absolute; bottom: 0; width: 100%; background-color: #ccc;"> © Made by EL GORRIM MOHAMED. LSI24/25 </footer>
</body>
</html>